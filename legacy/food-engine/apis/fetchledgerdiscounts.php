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
	
	
	$notFound = 0;
	



	
				//Basic Info
				
				$details = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_outlets` WHERE `code`='{$outlet}'"));
				$details1 = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `branch`='{$outlet}' AND `code`='{$adminmobile}'"));

				$outletname = $details['name'];
				$from = $_GET['from'];
				$fromFancy = date("d-M-Y", strtotime($from));
				$to = $_GET['to'];
				$toFancy = date("d-M-Y", strtotime($to));
				$query_date = $from;
				$mode = "";	
				$manager = $details1['name'];
				$manager_mobile = $adminmobile;
				
				$grand_sum = 0;
				
				$pdf->SetTitle('Discounts Ledger - '.$outletname);
				

				//Generation Time
				date_default_timezone_set('Asia/Calcutta');
				$mdate = date("j F, Y");
				$time = date("g:i a");
				$timenow = $time." on ".$mdate;
			
				//Form
				$pdf->SetFont('helvetica','B',18);
				$pdf->Ln(3);		
				$pdf->Cell(185,10,"DISCOUNTS LEDGER",50,80,'R');
				$pdf->Ln(1);
				$pdf->SetFont('times','',15);
				$pdf->Cell(185,0,'Zaitoon '.$outletname,105,80,'R');
				$pdf->Ln(5);
				$pdf->Image('images/small_logo_black.png',10,10,60);
				$pdf->Ln(29);
				$pdf->SetFont('helvetica','',11);
				$pdf->SetY(35);
				$pdf->SetX(105);
				$pdf->SetFont('helvetica','',7);
				$pdf->Cell(90,0,"MANAGER DETAILS",100,80,'R');
				$pdf->Ln(4);	
				$pdf->SetX(105);
				$pdf->SetFont('helvetica','',11);				
				$pdf->Cell(90,0,$manager,100,80,'R');
				$pdf->Ln(5);	
				$pdf->SetX(105);				
				$pdf->Cell(90,0,"Mob. ".$manager_mobile,100,80,'R');				
				$pdf->SetY(37);
				$pdf->SetX(10);
				$pdf->SetFont('times','',10);
				$pdf->Cell(120,0,"Generated at ".$timenow);
				$pdf->Ln(4);
				$pdf->SetFont('arial','',8);
				$pdf->Cell(120,0,"www.zaitoon.online/manager");
				$pdf->SetY(48);
				$pdf->SetFont('helvetica','B',13);
				$pdf->Cell(180,0,"DISCOUNTED ORDERS REPORT",100,80,'L');
				$pdf->SetFont('helvetica','',11);
				$pdf->Ln(5);
				$pdf->SetY(48);

				$pdf->SetY(50);
				$pdf->SetFont('helvetica','',10.5);
				$pdf->Cell(180,0,"__________________________________________________________________________________________");
								
				$pre = 56;
				$pdf->SetY($pre);
				
				
				
				
				
				
				$pdf->SetFont('helvetica','B',9);
				$pdf->Cell(120,0,"Sl.");
				$pdf->SetX(20);
				$pdf->Cell(120,0,"Date");
				$pdf->SetX(60);
				$pdf->Cell(120,0,"");
				$pdf->SetX(70);
				$pdf->Cell(120,0,"Customer");
				$pdf->SetX(90);
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell(120,0,"Order No.");
				$pdf->SetX(110);								
				$pdf->Cell(120,0,"Payment");				
				$pdf->SetX(130);
				$pdf->Cell(120,0,"Amount");
				$pdf->SetX(150);
				$pdf->Cell(120,0,"Discount");	
				$pdf->SetX(168);
				$pdf->Cell(120,0,"Coupon Used");				
				
				$pdf->Ln(2);
				$pdf->Cell(180,0,"______________________________________________________________________________________________________________________");
				$pre = $pre + 9;
				$pdf->SetY($pre);				
				
	

	$n = 0;
	while(1){
	
	$query_stamp = date("dmY", strtotime($query_date));
	
	//Break if query date exceeds max of date range
	if(strtotime($query_date) > strtotime($to)){
		break;
	}

				
				$query = mysql_query("SELECT * FROM zaitoon_orderlist WHERE outlet='{$outlet}' AND stamp='{$query_stamp}' AND isVerified='1' AND status='2' AND `usedVoucher` != '' AND `usedVoucher` != '0' ORDER BY orderID");

				while($info = mysql_fetch_assoc($query)){	
				
				$notFound++;			

				$n++;
				
				
				//Order Data
				$date = $info['date'];
				$time = $info['timePlace'];
				$user= $info['userID'];
				$orderID = $info['orderID'];
				$mode = $info['modeOfPayment'] == 'COD'? "Cash":"Online";
				$type = $info['isTakeaway'] == 0? "Delivery":"Take Away";
				$cart = json_decode($info['cart']);
				$value = $info['paidAmount'];
				$remarks = $info['usedVoucher'];
				
								
				
				$discountObj = json_decode($info['cart']);
				
				
				$grand_sum += $discountObj->cartDiscount;
				
				
					
				$pdf->SetFont('helvetica','',10);
				$pdf->Cell(120,0,$n.".");
				$pdf->SetX(20);
				$pdf->SetFont('helvetica','',10);
				$pdf->Cell(120,0,$date);
				$pdf->SetX(55);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell(120,0,$time);
				$pdf->SetX(70);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell(120,0,$user);
				$pdf->SetX(90);
				$pdf->SetFont('helvetica','B',10);
				$pdf->Cell(120,0,$orderID);
				$pdf->SetX(110);
				$pdf->SetFont('helvetica','',10);
				$pdf->Cell(120,0,$mode);
				$pdf->SetX(130);
				$pdf->Cell(120,0,$value);
				$pdf->SetX(150);
				$pdf->SetFont('helvetica','B',11);
				$pdf->Cell(120,0,$discountObj->cartDiscount);
				$pdf->SetX(168);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell(120,0,$remarks);


				$pdf->Ln(8);
				
				if($n%25 == 0){
					$pdf->AddPage();
					
					$pdf->SetY(15);
					$pdf->SetFont('helvetica','B',13);
					$pdf->Cell(180,0,"SALES REPORT",100,80,'L');
					$pdf->SetFont('helvetica','',11);
	
					$pdf->SetY(17);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
									

					$pdf->SetY(22);
					
													
					$pdf->SetFont('helvetica','B',9);
					$pdf->Cell(120,0,"Sl.");
					$pdf->SetX(20);
					$pdf->Cell(120,0,"Date");
					$pdf->SetX(60);
					$pdf->Cell(120,0,"");
					$pdf->SetX(70);
					$pdf->Cell(120,0,"Customer");
					$pdf->SetX(90);
					$pdf->SetFont('helvetica','B',8);
					$pdf->Cell(120,0,"Order No.");
					$pdf->SetX(110);								
					$pdf->Cell(120,0,"Payment");				
					$pdf->SetX(130);
					$pdf->Cell(120,0,"Amount");
					$pdf->SetX(150);
					$pdf->Cell(120,0,"Discount");	
					$pdf->SetX(168);
					$pdf->Cell(120,0,"Coupon Used");				
					
					$pdf->Ln(2);
					$pdf->Cell(180,0,"______________________________________________________________________________________________________________________");
					$pdf->Ln(8);
				}
			
				
			}
			
			
		//Increment Date by 1 day:
		$query_date = date('Y-m-d', strtotime($query_date.' +1 day'));	
					
			
		}
		
		
		$pdf->Cell(180,0,"______________________________________________________________________________________________________________________");
		$pdf->Ln(8);
		$pdf->SetX(80);
		$pdf->SetFont('helvetica','B',13);
		$pdf->Cell(120,0,"Total Discount Offered");	
		$pdf->SetX(150);
		$pdf->Cell(120,0,"Rs. ".$grand_sum);
		
		
		if($notFound == 0)
			die('<br><br><center><p style="font-size:21px; color: #e74c3c">There are no Discounted Orders found between '.$from.' to '.$to.'</p><a href="https://www.zaitoon.online/manager/finance-ledger.html">BACK</a></center>');
		
		
				
		$pdf->Output('Discounts_'.$outlet.'_'.$fromFancy.'_to_'.$toFancy.'.pdf', 'I');
				
								
?>