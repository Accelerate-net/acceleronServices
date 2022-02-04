<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type, responseType');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

require('fpdf/fpdf.php');

//Encryption Validation
if(!isset($_GET['access'])){
	die("Error. Failed to generate report.");
}

$token = $_GET['access'];

$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken, true);

//Expiry Validation
date_default_timezone_set('Asia/Calcutta');
$dateStamp = date_create($tokenid['date']);
$today = date_create(date("Y-m-j"));
$interval = date_diff($dateStamp, $today);
$interval = $interval->format('%a');

if($interval > $tokenExpiryDays){
	die("Expired. Please login again and try.");
}

//Check if the token is tampered
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
	$adminmobile = $tokenid['mobile'];
}
else{
	die("Not authorised");
}



	
	$pdf = new FPDF();
	$pdf->SetFont('helvetica','',12);
	$pdf->AddPage();
	

				//Basic Info

				$uid = $_GET['uid'];
				$type = 'sms'; $_GET['type'];
				
				$main_details = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_smsmessenger` WHERE `id`='{$uid}'"));
				if($main_details['id'] == ""){
					die('Invalid Request');				
				}
				
				$m_target_users = $main_details['target'];
				$m_target_count = $main_details['count'];
				$m_content = $main_details['content'];	
				$m_date = $main_details['date'];			
				

				//Generation Time
				date_default_timezone_set('Asia/Calcutta');
				$mdate = date("j F, Y");
				$time = date("g:i a");
				$timenow = $time." on ".$mdate;
			
				//Form
				$pdf->SetFont('helvetica','B',16);
				$pdf->Ln(3);		
				$pdf->Cell(185,10,$type == 'sms'? "SMS DELIVERY REPORT" : "PUSH NOTIFICATIONS SUMMARY",50,80,'R');
				$pdf->Ln(1);
				$pdf->SetFont('times','',15);
				$pdf->Cell(185,0,'Zaitoon Restaurant',105,80,'R');
				$pdf->Ln(5);
				$pdf->Image('images/small_logo_black.png',10,10,60);
				$pdf->Ln(29);
				$pdf->SetFont('helvetica','',11);
				$pdf->SetY(35);
				$pdf->SetX(105);
				$pdf->SetFont('helvetica','',7);
				$pdf->Cell(90,0,$type == 'sms'? "SMS CONTENT" : "PUSH CONTENT",100,80,'R');
				$pdf->Ln(3);	
				$pdf->SetX(105);
				$pdf->SetFont('helvetica','',11);	
				$pdf->MultiCell(90, 5, $m_content, 5, 'R');					
							
				$pdf->SetY(42);
				$pdf->SetX(10);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Target Users");
				$pdf->SetX(40);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$m_target_users);
				
				$pdf->Ln(5);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Target Reach");
				$pdf->SetX(40);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$m_target_count);

				$pdf->Ln(5);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Sent Date");				
				$pdf->SetX(40);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$m_date);
				
				$pdf->SetY(60);
				$pdf->SetFont('helvetica','B',13);
				$pdf->Cell(180,0,"DELIVERY RECEIPTS",100,80,'L');
				$pdf->SetFont('helvetica','',11);
				$pdf->Ln(5);
				$pdf->SetY(48);

				$pdf->SetY(62);
				$pdf->SetFont('helvetica','',10.5);
				$pdf->Cell(180,0,"__________________________________________________________________________________________");
								
				$pre = 68;
				$pdf->SetY($pre);
							
				$pdf->SetFont('helvetica','B',9);
				$pdf->Cell(120,0,"Sl.");
				$pdf->SetX(20);
				$pdf->Cell(120,0,"Mobile Number");
				$pdf->SetX(60);
				$pdf->Cell(120,0,"Name");
				$pdf->SetX(120);
				$pdf->Cell(140,0,"Class");
				$pdf->SetX(140);
				$pdf->Cell(140,0,"Status");
				$pdf->SetX(160);								
				$pdf->Cell(120,0,"Time Stamp");							
				
				$pdf->Ln(2);
				$pdf->SetFont('helvetica','',10.5);
				$pdf->Cell(180,0,"__________________________________________________________________________________________");
				$pre = $pre + 9;
				$pdf->SetY($pre);				
				
	

	
			$n = 0;
			$query = mysql_query("SELECT * FROM `z_smsdeliveryreports` WHERE `id`='{$uid}'");
			while($info = mysql_fetch_assoc($query)){					
				$n++;

				//Order Data
				$date = $info['finshedTime'];
				$status = $info['status'];
				$user = $info['user'];
				
				if($status == 1){
					$my_status = "Confirmed";
				}
				else if($status == 5){
					$my_status = "Failed";
				}
				else{
					$my_status = "Pending";
				}
				
				$user_check = mysql_fetch_assoc(mysql_query("SELECT `name`, `memberType` FROM `z_users` WHERE `mobile`='{$user}'"));	
				
				$pdf->SetFont('helvetica','',9);
				$pdf->Cell(120,0, $n.".");
				$pdf->SetX(20);
				$pdf->Cell(120,0, $user);
				$pdf->SetX(60);
				$pdf->Cell(120,0, $user_check['name']);
				$pdf->SetX(120);
				$pdf->Cell(140,0, $user_check['memberType']);
				$pdf->SetX(140);
				$pdf->Cell(140,0, $my_status);
				$pdf->SetX(160);								
				$pdf->Cell(120,0, $date != ""? $date : "-");


				$pdf->Ln(8);
				
				if($n%25 == 0){
					$pdf->AddPage();
					
					$pdf->SetY(15);
					$pdf->SetFont('helvetica','B',13);
					$pdf->Cell(180,0,"DELIVERY RECEIPTS",100,80,'L');
					$pdf->SetFont('helvetica','',11);
	
					$pdf->SetY(17);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
									

					$pdf->SetY(22);
					
													
					$pdf->SetFont('helvetica','B',9);
					$pdf->Cell(120,0,"Sl.");
					$pdf->SetX(20);
					$pdf->Cell(120,0,"Mobile Number");
					$pdf->SetX(60);
					$pdf->Cell(120,0,"Name");
					$pdf->SetX(120);
					$pdf->Cell(140,0,"Class");
					$pdf->SetX(140);
					$pdf->Cell(140,0,"Status");
					$pdf->SetX(160);								
					$pdf->Cell(120,0,"Time Stamp");							
						
							
					
					$pdf->Ln(2);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
					$pdf->Ln(8);
				}			
				
			}				
			

	

		if($n == 0)
			die('<br><br><center><p style="font-size:21px; color: #e74c3c">Summary can not be generated because, this message was sent to nobody.</p><a href="https://www.zaitoon.online/manager/messenger.html">BACK</a></center>'
		);
		
						
		$pdf->Output();								
?>