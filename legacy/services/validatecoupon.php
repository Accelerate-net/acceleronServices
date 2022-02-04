<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);


//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['coupon'])){
	$output = array(
		"status" => false,
		"error" => "Coupon Code is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['cart'])){
	$output = array(
		"status" => false,
		"error" => "Cart Object is missing"
	);
	die(json_encode($output));
}


$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken, true);

//Expiry Validation
date_default_timezone_set('Asia/Calcutta');
$dateStamp = date_create($tokenid['date']);
$today = date_create(date("Y-m-j"));
$interval = date_diff($dateStamp, $today);
$interval = $interval->format('%a');
$todayStamp = date("Ymd");

if($interval > $tokenExpiryDays){
	$output = array(
		"status" => false,
		"error" => "Expired Token"
	);
	die(json_encode($output));
}


//Check if the token is tampered
if($tokenid['mobile']){
	$user = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}



/** Rules Templates **/
//$coupon = json_decode('{ "rule": "FIRSTORDER", "discount": "75", "minimum": "400", "isAppOnly": true }',true);
//$coupon = json_decode('{ "rule": "PERCENTAGE", "minimumCart": 400, "maximum": 50, "percentage":8.9, "isAppOnly": true }',true);
//$coupon = json_decode('{ "rule": "DISCOUNTEDCOMBO", "items": [{"code":1000098,"count":20},{"code":1000099,"count":1}], "discount":100, "isAppOnly": true }',true);

$code = $_POST['coupon'];
$voucherFlag = false;

$detailed = mysql_query("SELECT * FROM z_couponrules WHERE code='{$code}' AND `isActive` = 1");
if($info = mysql_fetch_assoc($detailed)){
	$coupon = json_decode($info['rule'], true);
	$rule = $coupon['rule']; //COUPON RULE
	$voucherFlag = false;

	/* Outlet wise restriction - There is difficulty as the outlet info not sent in validate coupon request. 
	Can be done in final validations before creating final order*/
	/*
	if($info['list'] != ''){
		$outletsList = explode(',', $info['list']);
	}
	*/
}
else{ //No such coupon exist
	//Check for vouchers
	$voucher_check = mysql_query("SELECT * FROM `z_vouchers` WHERE `code`='{$code}' AND `isActive` = 1");
	if($voucher_info = mysql_fetch_assoc($voucher_check)){
		if($voucher_info['isRestricted'] == 1 && $voucher_info['userRestriction'] != $user){
			$output = array(
				"status" => false,
				"error" => "No such coupon or voucher exists."
			);
			die(json_encode($output));
		}
		else if($voucher_info['isActive'] == 0){
			$output = array(
				"status" => false,
				"error" => "This voucher is deactivated. Contact care@zaitoon.online"
			);
			die(json_encode($output));		
		}
		else if($voucher_info['expiry'] < $todayStamp){
			$output = array(
				"status" => false,
				"error" => "This voucher is expired"
			);
			die(json_encode($output));		
		}
		else{
			$voucherFlag = true;
		}
	}
	else{
		$output = array(
			"status" => false,
			"error" => "No such coupon or voucher exists."
		);
		die(json_encode($output));
	}
}


//Get Cart Info.
$cart = $_POST['cart'];


//It's a VOUCHER
if($voucherFlag){

	$discount = 0;
	
	$i = 0;
	$total = 0;
	while($cart['items'][$i]['itemCode']){
		$total = $total + ($cart['items'][$i]['itemPrice']*$cart['items'][$i]['qty']);
		$i++;
	}
	
	$voucher_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_vouchers` WHERE `code`='{$code}'"));
	$voucher_value = 0;
	if($voucher_info['value'] != ""){
		$voucher_value = $voucher_info['value'];
	}
	
	if($voucher_info['minAmount'] > $total){
		$output = array(
			"status" => false,
			"error" => "Minimum order value to redeem this voucher is Rs. ".$voucher_info['minAmount']
		);
		die(json_encode($output));	
	}
	
	if($voucher_value < $total){
		$discount = $voucher_value+0;		
	}
	else{
		$discount = $total;
	}
	
	$output = array(
		"status" => true,
		"error" => "",
		"discount" => $discount
	);
	die(json_encode($output));


}
else{
	//Rules
	switch ($rule){
		case "FIRSTORDER":{
			$discount = 0;
			//Ensure the user makes his first order
			$result = mysql_query("SELECT * FROM zaitoon_orderlist WHERE userID='{$user}'");
			if(!$order = mysql_fetch_assoc($result)){
				//Now check if the cart minimum rule applies
				$i = 0;
				$total = 0;
				while($cart['items'][$i]['itemCode']){
					$total = $total + ($cart['items'][$i]['itemPrice']*$cart['items'][$i]['qty']);
					$i++;
				}
					//setting status true if cart value is greater than min. of coupon
					if($total >= $coupon['minimum']){
						$status = true;
						$discount = $coupon['discount'];
					}
					else{
						$status = false;
						$error = "Minimum order value is ".$coupon['minimum'];
					}
	
	
			}
			else{
				$status = false;
				$error = "Coupon applicable only for first order";
			}
	
			$output = array(
				"status" => $status,
				"discount" => $discount,
				"error" => $error
			);
	
			echo json_encode($output);
			break;
		}
	
	
		case "PERCENTAGE":{
			$discount = 0;
	
			$i = 0;
			$total = 0;
			while($cart['items'][$i]['itemCode']){
				$total = $total + ($cart['items'][$i]['itemPrice']*$cart['items'][$i]['qty']);
				$i++;
			}
	
			if($total >= $coupon['minimumCart']){
				$status = true;
				$discount = round(($coupon['percentage']/100)*$total);
				if($discount >= $coupon['maximum'])
					$discount = $coupon['maximum'];
			}
			else{
				$status = false;
				$error = "Minimum order value is ".$coupon['minimumCart'];
			}
	
			$output = array(
				"status" => $status,
				"discount" => $discount,
				"error" => $error
			);
	
			echo json_encode($output);
			break;
	
		}
	
	
		case "DISCOUNTEDCOMBO":{
			$total_discount = 0;
			$combocart = $cart;
			$mycoupon = $coupon;
	
			function getDiscount($coupon){
				global $combocart;
				$isValid = false;
	
				$i = 0;
				$terminate = false;
				while($combocart['items'][$i]['itemCode'] && !$terminate){
					// echo '<br><br><br>Case-'.$i.'<br>________________________';
					// echo '<br>Taking item '.$combocart['items'][$i]['itemCode'].' from CART';
					//Search if the item is present in coupon's required items
					$j = 0;
					while($coupon['items'][$j]['code']){
						// echo '<br>[COUPON]'.$coupon['items'][$j]['code'];
						if($combocart['items'][$i]['itemCode'] == $coupon['items'][$j]['code'])
						{
							// echo '<br>[COUPON] found matching!';
							if($combocart['items'][$i]['qty'] >= $coupon['items'][$j]['count'])
							{
								// echo '<br>[COUPON] Proceed';
								$combocart['items'][$i]['qty'] = $combocart['items'][$i]['qty'] - $coupon['items'][$j]['count'];
								$isValid = true;
							}
							else {
								// echo '<br>[COUPON] TERMINATE!!!!!!!';
								$terminate = true;
								$isValid = false;
								break;
							}
							break;
						}
						$j++;
					}
					$i++;
				}
	
				if($isValid){
					// echo '<br><br>*********************************************SUCCESS!*********************************************';
					return $coupon['discount'];
				}
				else {
					return 0;
				}
			}
	
			//Calculate the grand total of discounts
			while($disc = getDiscount($mycoupon))
			{
				$total_discount = $total_discount + $disc;
			}
	
	
			//Results
			if($total_discount > 0){
				$output = array(
					"status" => true,
					"discount" => $total_discount,
				);
			}
			else{
				$output = array(
					"status" => false,
					"error" => "Your cart does not contain required items."
				);
			}
			echo json_encode($output);
			break;
		}
	
	
	}
}


?>
