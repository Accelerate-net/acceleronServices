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



//Generate All Summary
function generateSummary($f_isActive, $f_isDatewise, $f_query_date, $f_max_to_date){

				$pdf = new FPDF();		
				$pdf->SetTitle('Zaitoon Coupons Summary');
				$pdf->AddPage();
				
				$query_date = $f_query_date;
							
				//Quick Analytics	
			if(!$f_isDatewise){			
				$allCoupons = mysql_query("SELECT `code` FROM `z_couponrules` WHERE `isActive` = '{$f_isActive}'");
				$totalCoupons = 0;
				$totalSum = 0;
				$totalRedemptions = 0;
				while($allList = mysql_fetch_assoc($allCoupons)){
					$analytics = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) as total, COUNT(`paidAmount`) as grand FROM `zaitoon_orderlist` WHERE `usedVoucher`='{$allList['code']}' AND `isVerified`=1 AND `status`!=5"));
					$totalCoupons++;
					$totalSum = $totalSum + $analytics['total'];
					$totalRedemptions = $totalRedemptions + $analytics['grand'];
				}					
			}			
				//Form
				$pdf->SetFont('helvetica','B',16);
				$pdf->Ln(3);		
				$pdf->Cell(185,10, "COUPON REVENUE SUMMARY",50,80,'R');
				$pdf->Ln(1);
				$pdf->SetFont('times','',15);
				$pdf->Cell(185,0,'Zaitoon Restaurant',105,80,'R');
				$pdf->Ln(5);
				$pdf->Image('images/small_logo_black.png',10,10,60);
				$pdf->Ln(29);
				$pdf->SetFont('helvetica','',11);			

			if(!$f_isDatewise){
				$pdf->SetY(33);
				$pdf->SetX(10);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Period");
				$pdf->SetX(60);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ': All Time');
							
				$pdf->Ln(5);
				$pdf->SetX(10);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Number of Coupons");
				$pdf->SetX(60);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$totalCoupons);
				
				$pdf->Ln(5);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Redemptions");
				$pdf->SetX(60);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$totalRedemptions);

				$pdf->Ln(5);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Total Revenue");				
				$pdf->SetX(60);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$totalSum);
				$pdf->SetY(60);
				$pdf->SetFont('helvetica','B',13);
				$pdf->Cell(180,0, "Redemption Details",100,80,'L');
				$pdf->SetFont('helvetica','',11);
				$pdf->SetY(62);
				$pdf->SetFont('helvetica','',10.5);
				$pdf->Cell(180,0,"__________________________________________________________________________________________");
				$pre = 68;
			}
			else{	
				$pdf->SetY(40);
				$pdf->SetFont('helvetica','B',13);
				$pdf->Cell(180,0,"Redemption Details from ".date("d-m-Y",  strtotime($f_query_date))." to ".date("d-m-Y", strtotime($f_max_to_date)),100,80,'L');
				$pdf->SetFont('helvetica','',11);
				$pdf->SetY(42);
				$pdf->SetFont('helvetica','',10.5);
				$pdf->Cell(180,0,"__________________________________________________________________________________________");
				$pre = 48;
			}

								
				
				$pdf->SetY($pre);
							
				$pdf->SetFont('helvetica','B',9);
				$pdf->Cell(120,0,"Sl.");
				$pdf->SetX(20);
				$pdf->Cell(120,0,"Coupon Code");
				$pdf->SetX(70);
				$pdf->Cell(120,0,"Limit");
				$pdf->SetX(90);
				$pdf->Cell(120,0,"Used");
				$pdf->SetX(130);
				$pdf->Cell(140,0,"Total Amount");
				$pdf->SetX(160);
				$pdf->Cell(140,0,"Total Discount");															
				
				$pdf->Ln(2);
				$pdf->SetFont('helvetica','',10.5);
				$pdf->Cell(180,0,"__________________________________________________________________________________________");
				$pre = $pre + 9;
				$pdf->SetY($pre);				
				
	
	
	if($f_isDatewise){
	
			//Date wise restrictions
	
			$n = 0;
			$query = mysql_query("SELECT `code`, `limit` FROM `z_couponrules` WHERE `isActive` = '{$f_isActive}'");
			
			while($info = mysql_fetch_assoc($query)){					
				$n++;
				
				//Calculating Figures
				$grand_total = 0;
				$grand_used = 0;
				$grand_discount = 0;
				
				$query_date = $f_query_date;
				$query_stamp = date("dmY", strtotime($query_date));
				
				while(strtotime($query_date) <= strtotime($f_max_to_date)){
				
					$query_stamp = date("dmY", strtotime($query_date));					
				
					$coupon_check = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) as total, COUNT(`paidAmount`) as grand FROM `zaitoon_orderlist` WHERE `usedVoucher`='{$info['code']}' AND `isVerified`=1 AND `status`!=5 AND `stamp`='{$query_stamp}'"));
															
					$grand_total = $grand_total + $coupon_check['total'];
					$grand_used = $grand_used + $coupon_check['grand'];				
					
					//Find Discount
					$disc = mysql_query("SELECT `cart` FROM `zaitoon_orderlist` WHERE `isVerified`=1 AND `status`!=5 AND `usedVoucher`='{$info['code']}' AND `stamp`='{$query_stamp}'");
					while($discOrders = mysql_fetch_assoc($disc)){
						$discountObj = json_decode($discOrders['cart']);
						$grand_discount = $grand_discount + $discountObj->cartDiscount;						
					}
									
				
					//Increment Date by 1 day:
					$query_date = date('Y-m-d', strtotime($query_date.' +1 day'));	
				}
				
				$pdf->SetFont('helvetica','',9);
				$pdf->Cell(120,0, $n.".");
				$pdf->SetX(20);
				$pdf->Cell(120,0, $info['code']);
				$pdf->SetX(70);
				$pdf->Cell(120,0, $info['limit']);
				$pdf->SetX(90);
				$pdf->Cell(140,0, $grand_used != 0? $grand_used : '-');
				$pdf->SetX(130);
				$pdf->Cell(100,0, $grand_total != 0? "Rs. ".$grand_total : '-');
				$pdf->SetX(160);								
				$pdf->Cell(120,0, $grand_discount != 0? "Rs. ".$grand_discount : '-');


				$pdf->Ln(8);
				
				//First Round
				if($n%25 == 0){
					$pdf->AddPage();
					
					$pdf->SetY(15);
					$pdf->SetFont('helvetica','B',13);
					$pdf->Cell(180,0,"Redemption Details" ,100,80,'L');
					$pdf->SetFont('helvetica','',11);
	
					$pdf->SetY(17);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
									

					$pdf->SetY(22);
					
													
					$pdf->SetFont('helvetica','B',9);
					$pdf->Cell(120,0,"Sl.");
					$pdf->SetX(20);
					$pdf->Cell(120,0,"Coupon Code");
					$pdf->SetX(70);
					$pdf->Cell(120,0,"Limit");
					$pdf->SetX(90);
					$pdf->Cell(120,0,"Used");
					$pdf->SetX(130);
					$pdf->Cell(140,0,"Total Amount");
					$pdf->SetX(160);
					$pdf->Cell(140,0,"Total Discount");						
						
							
					
					$pdf->Ln(2);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
					$pdf->Ln(8);
				}			
				
			}
							
	} //While(1)	
	
	
	//NOT datewise
	if(!$f_isDatewise){
			$n = 0;

			$query = mysql_query("SELECT `code`, `limit` FROM `z_couponrules` WHERE `isActive` = '{$f_isActive}'");
			
			while($info = mysql_fetch_assoc($query)){					
				$n++;
					
				$coupon_check = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) as total, COUNT(`paidAmount`) as grand FROM `zaitoon_orderlist` WHERE `usedVoucher`='{$info['code']}' AND `isVerified`=1 AND `status`!=5"));
				
				//Find Discount
				$totalDiscount = 0;
				$disc = mysql_query("SELECT `cart` FROM `zaitoon_orderlist` WHERE `isVerified`=1 AND `status`!=5 AND `usedVoucher`='{$info['code']}'");
				while($discOrders = mysql_fetch_assoc($disc)){
					$discountObj = json_decode($discOrders['cart']);
					$totalDiscount = $totalDiscount + $discountObj->cartDiscount;
				}
								
				
				$pdf->SetFont('helvetica','',9);
				$pdf->Cell(120,0, $n.".");
				$pdf->SetX(20);
				$pdf->Cell(120,0, $info['code']);
				$pdf->SetX(70);
				$pdf->Cell(120,0, $info['limit']);
				$pdf->SetX(90);
				$pdf->Cell(140,0, $coupon_check['grand'] != 0? $coupon_check['grand']: '-');
				$pdf->SetX(130);
				$pdf->Cell(100,0, $coupon_check['total'] != ''? "Rs. ".$coupon_check['total']: '-');
				$pdf->SetX(160);								
				$pdf->Cell(120,0, "Rs. ".$totalDiscount);


				$pdf->Ln(8);
				
				//First Round
				if($n%25 == 0){
					$pdf->AddPage();
					
					$pdf->SetY(15);
					$pdf->SetFont('helvetica','B',13);
					$pdf->Cell(180,0,"Redemption Details" ,100,80,'L');
					$pdf->SetFont('helvetica','',11);
	
					$pdf->SetY(17);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
									

					$pdf->SetY(22);
					
													
					$pdf->SetFont('helvetica','B',9);
					$pdf->Cell(120,0,"Sl.");
					$pdf->SetX(20);
					$pdf->Cell(120,0,"Coupon Code");
					$pdf->SetX(70);
					$pdf->Cell(120,0,"Limit");
					$pdf->SetX(90);
					$pdf->Cell(120,0,"Used");
					$pdf->SetX(130);
					$pdf->Cell(140,0,"Total Amount");
					$pdf->SetX(160);
					$pdf->Cell(140,0,"Total Discount");						
						
							
					
					$pdf->Ln(2);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
					$pdf->Ln(8);
				}			
											
				
			}		
	} //End - Not Daywise	
	
	if($n == 0){
		die('<br><br><center><p style="font-size:21px; color: #e74c3c">Summary can not be generated because, this coupon has not been used.</p><a href="https://www.zaitoon.online/manager/promotions.html">BACK</a></center>');	
	}					
							
	$pdf->Output('CouponSummary_Zaitoon.pdf', 'I');			
		
} //End of Function


	

				
//Generate Pages
function generatePage($f_uid, $f_isDatewise, $f_query_date, $f_max_to_date){

				$pdf = new FPDF();		
				$pdf->SetTitle('Zaitoon Coupons Summary');
				$pdf->AddPage();
				
				$query_date = $f_query_date;
				
				$main_details = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_couponrules` WHERE `code` = '{$f_uid}'"));
				
				if($main_details['code'] == ""){
					die('Invalid Code');			
				}						
							
				//Form
				$pdf->SetFont('helvetica','B',16);
				$pdf->Ln(3);		
				$pdf->Cell(185,10, "COUPON SUMMARY",50,80,'R');
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
				$pdf->Cell(90,0, "COUPON BRIEF",100,80,'R');
				$pdf->Ln(3);	
				$pdf->SetX(105);
				$pdf->SetFont('helvetica','',11);	
				$pdf->MultiCell(90, 5, $main_details['brief'], 5, 'R');					
							
				$pdf->SetY(42);
				$pdf->SetX(10);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Coupon Code");
				$pdf->SetX(40);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$main_details['code']);
				
				$pdf->Ln(5);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Maximum Limit");
				$pdf->SetX(40);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$main_details['limit']);

				$pdf->Ln(5);
				$pdf->SetFont('helvetica','', 9);
				$pdf->Cell(120,0,"Redemptions");				
				$pdf->SetX(40);
				$pdf->SetFont('helvetica','B', 10);
				$pdf->Cell(120,0, ": ".$main_details['usage']);
				
				$pdf->SetY(60);
				$pdf->SetFont('helvetica','B',13);
				$pdf->Cell(180,0,"Redemption Details of ".$main_details['code'],100,80,'L');
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
				$pdf->SetX(50);
				$pdf->Cell(120,0,"Name");
				$pdf->SetX(100);
				$pdf->Cell(140,0,"Order ID");
				$pdf->SetX(120);
				$pdf->Cell(140,0,"Amount");
				$pdf->SetX(140);								
				$pdf->Cell(120,0,"Discount");
				$pdf->SetX(160);								
				$pdf->Cell(120,0,"Date");							
				
				$pdf->Ln(2);
				$pdf->SetFont('helvetica','',10.5);
				$pdf->Cell(180,0,"__________________________________________________________________________________________");
				$pre = $pre + 9;
				$pdf->SetY($pre);				
				
	
	$n = 0;
	while($f_isDatewise){
	//Date wise restrictions
	$query_stamp = date("dmY", strtotime($query_date));
	
		//Break if query date exceeds max of date range
		if(strtotime($query_date) > strtotime($f_max_to_date)){
			break;
		}
	
				
			$query = mysql_query("SELECT `orderID`, `userID`,`paidAmount`,`cart`, `outlet`,`date` FROM `zaitoon_orderlist` WHERE `usedVoucher`='{$f_uid}' AND `isVerified`=1 AND `stamp`='{$query_stamp}'");
			while($info = mysql_fetch_assoc($query)){					
				$n++;
					
				$user_check = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_users` WHERE `mobile`='{$info['userID']}'"));
				
				$discountObj = json_decode($info['cart']);	
				
				$pdf->SetFont('helvetica','',9);
				$pdf->Cell(120,0, $n.".");
				$pdf->SetX(20);
				$pdf->Cell(120,0, $info['userID']);
				$pdf->SetX(50);
				$pdf->Cell(120,0, $user_check['name']);
				$pdf->SetX(100);
				$pdf->Cell(140,0, $info['orderID']);
				$pdf->SetX(120);
				$pdf->Cell(140,0, "Rs. ".$info['paidAmount']);
				$pdf->SetX(140);								
				$pdf->Cell(120,0, "Rs. ".$discountObj->cartDiscount);
				$pdf->SetX(160);								
				$pdf->Cell(120,0, $info['date'] != ""? $info['date'] : "-");


				$pdf->Ln(8);
				
				if($n%25 == 0){
					$pdf->AddPage();
					
					$pdf->SetY(15);
					$pdf->SetFont('helvetica','B',13);
					$pdf->Cell(180,0,"Redemption Details of ".$main_details['code'],100,80,'L');
					$pdf->SetFont('helvetica','',11);
	
					$pdf->SetY(17);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
									

					$pdf->SetY(22);
					
													
					$pdf->SetFont('helvetica','B',9);
					$pdf->Cell(120,0,"Sl.");
					$pdf->SetX(20);
					$pdf->Cell(120,0,"Mobile Number");
					$pdf->SetX(50);
					$pdf->Cell(120,0,"Name");
					$pdf->SetX(100);
					$pdf->Cell(140,0,"Order ID");
					$pdf->SetX(120);
					$pdf->Cell(140,0,"Amount");
					$pdf->SetX(140);								
					$pdf->Cell(120,0,"Discount");	
					$pdf->SetX(160);								
					$pdf->Cell(120,0,"Date");						
						
							
					
					$pdf->Ln(2);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
					$pdf->Ln(8);
				}			
				
			}
			
			//Increment Date by 1 day:
			$query_date = date('Y-m-d', strtotime($query_date.' +1 day'));	
	} //While(1)	
	
	
	//NOT datewise
	if(!$f_isDatewise){
			$n = 0;

			$query = mysql_query("SELECT `orderID`, `userID`,`paidAmount`,`cart`, `outlet`,`date` FROM `zaitoon_orderlist` WHERE `usedVoucher`='{$f_uid}' AND `isVerified`=1");
			
			while($info = mysql_fetch_assoc($query)){					
				$n++;
					
				$user_check = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_users` WHERE `mobile`='{$info['userID']}'"));
				
				$discountObj = json_decode($info['cart']);	
				
				$pdf->SetFont('helvetica','',9);
				$pdf->Cell(120,0, $n.".");
				$pdf->SetX(20);
				$pdf->Cell(120,0, $info['userID']);
				$pdf->SetX(50);
				$pdf->Cell(120,0, $user_check['name']);
				$pdf->SetX(100);
				$pdf->Cell(140,0, $info['orderID']);
				$pdf->SetX(120);
				$pdf->Cell(140,0, "Rs. ".$info['paidAmount']);
				$pdf->SetX(140);								
				$pdf->Cell(120,0, "Rs. ".$discountObj->cartDiscount);
				$pdf->SetX(160);								
				$pdf->Cell(120,0, $info['date'] != ""? $info['date'] : "-");


				$pdf->Ln(8);
				
				//First Round
				if($n%25 == 0){
					$pdf->AddPage();
					
					$pdf->SetY(15);
					$pdf->SetFont('helvetica','B',13);
					$pdf->Cell(180,0,"Redemption Details of ".$main_details['code'],100,80,'L');
					$pdf->SetFont('helvetica','',11);
	
					$pdf->SetY(17);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
									

					$pdf->SetY(22);
					
													
					$pdf->SetFont('helvetica','B',9);
					$pdf->Cell(120,0,"Sl.");
					$pdf->SetX(20);
					$pdf->Cell(120,0,"Mobile Number");
					$pdf->SetX(50);
					$pdf->Cell(120,0,"Name");
					$pdf->SetX(100);
					$pdf->Cell(140,0,"Order ID");
					$pdf->SetX(120);
					$pdf->Cell(140,0,"Amount");
					$pdf->SetX(140);								
					$pdf->Cell(120,0,"Discount");	
					$pdf->SetX(160);								
					$pdf->Cell(120,0,"Date");						
						
							
					
					$pdf->Ln(2);
					$pdf->SetFont('helvetica','',10.5);
					$pdf->Cell(180,0,"__________________________________________________________________________________________");
					$pdf->Ln(8);
				}			
											
				
			}		
	} //End - Not Daywise	
	
	if($n == 0){
		die('<br><br><center><p style="font-size:21px; color: #e74c3c">Summary can not be generated because, this coupon has not been used.</p><a href="https://www.zaitoon.online/manager/promotions.html">BACK</a></center>');	
	}					
							
	$pdf->Output('CouponSummary_Zaitoon.pdf', 'I');			
		
} //End of Function					
					

$isSuccess = false;
if(isset($_GET['uid']) && $_GET['uid'] != ""){			
				
	//Date wise filter
	$isDatewise = false;
	if(isset($_GET['from']) && $_GET['from'] != ''){
		$query_date = $_GET['from'];
		$isDatewise = true;
	}
	else{
		$isDatewise = false;
	}
					
	if(isset($_GET['to']) && $_GET['to'] != ''){
		$max_to_date = $_GET['to'];
	}
	else{
		$max_to_date = date('Y-m-d');
	}

	$isSuccess = true;
	generatePage($_GET['uid'], $isDatewise, $query_date, $max_to_date);
}
else if(isset($_GET['mode']) && $_GET['mode'] != ""){
	
	//Date wise filter
	$isDatewise = false;
	if(isset($_GET['from']) && $_GET['from'] != ''){
		$query_date = $_GET['from'];
		$isDatewise = true;
	}
	else{
		$isDatewise = false;
	}
					
	if(isset($_GET['to']) && $_GET['to'] != ''){
		$max_to_date = $_GET['to'];
	}
	else{
		$max_to_date = date('Y-m-d');
	}
		
	$isSuccess = true;
	generateSummary($_GET['mode'] == 'ACTIVE'? 1: 0, $isDatewise, $query_date, $max_to_date);
}	


	if(!$isSuccess){
		die('<br><br><center><p style="font-size:21px; color: #e74c3c">Missing Values - Summary can not be generated.</p><a href="https://www.zaitoon.online/manager/promotions.html">BACK</a></center>');	
	}
										
?>