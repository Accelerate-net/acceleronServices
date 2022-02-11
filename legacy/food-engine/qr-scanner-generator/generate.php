<?php

    header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    
    error_reporting(0);
    
    //Database Connection
    define('INCLUDE_CHECK', true);
    require 'connect.php';
    
    session_start();
	require('fpdf/fpdf.php');
	


	//Request
	$table = $_GET['table'];
	$branch = $_GET['branch'];
	
	if($table == "" || $branch == ""){
	    die("Table Number or Branch details are missing.");
	}
	
	$file_brand = "";
    $file_branch = "";
    $file_table = "";
    $unique_code = "";
	$check = mysql_fetch_assoc(mysql_query("SELECT * FROM smart_generated_designs WHERE branch='{$branch}' AND mapped_table='{$table}'"));
	if($check['id']){
	    $file_brand = $check['brand'];
	    $file_branch = $check['branch']; 
	    $file_table = $check['mapped_table'];
	    $unique_code = $check['code'];
	}
	else{
	    die("No QR Scanner registered. Please contact Accelerate Support (support@accelerate.net.in)");
	}
	
    $file_download_name = "QR_".$file_branch."_".$file_table.".pdf";
	
	
	//Template Specific
	$template_file_name = 'template.jpg';
	$template_width = 101.6;
	$template_height = 152.4;
	$qr_size = 300;
	
	
	$qr_url = "https://zaitoon.restaurant/smart?code=".$unique_code; 
	$qr_position_x = 26;
	$qr_position_y = 90;
	
	$qr_temp_file_name = "stored_codes/".$file_brand."/".$unique_code.".png";
	
	$is_QR_ImageAlreadyCreated = false;
	if(file_exists($qr_temp_file_name)){
	    $is_QR_ImageAlreadyCreated = true;
	}
	
	//Generate QR Code
	if(!$is_QR_ImageAlreadyCreated){
    	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://chart.googleapis.com/chart");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chs={$qr_size}x{$qr_size}&cht=qr&chl=" . urlencode($qr_url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $qrImage = curl_exec($ch);
        curl_close($ch);
        
        if($qrImage) {
            file_put_contents($qr_temp_file_name, $qrImage);
        }
        
        sleep(5);
    }
	
	class PDF extends FPDF
	{
		function content()		
		{
		        $this->Image($GLOBALS['template_file_name'], 0, 0,102,0,'JPG');
		        
				//Table Number
				$this->SetTextColor(256,256,256);
				$this->SetFont('helvetica','B',15);
				$this->SetY(55);
				$this->SetX(2);
				$this->Cell(10,10,$GLOBALS['file_table'],100,80,'C');
				
				//QR Code
				$this->Image($GLOBALS['qr_temp_file_name'], $GLOBALS['qr_position_x'], $GLOBALS['qr_position_y'], 50, 0, 'PNG');
		}	
	}
	$pdf = new PDF('P','mm',array($template_width,$template_height));
	$pdf->SetFont('helvetica','',12);
	$pdf->AddPage();
	$pdf->content();
	$pdf->Output($file_download_name, 'D');
?>