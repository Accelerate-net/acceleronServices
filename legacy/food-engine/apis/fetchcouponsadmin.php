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

$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken, true);

//Expiry Validation
date_default_timezone_set('Asia/Calcutta');
$dateStamp = date_create($tokenid['date']);
$today = date_create(date("Y-m-j"));
$interval = date_diff($dateStamp, $today);
$interval = $interval->format('%a');

if($interval > $tokenExpiryDays){
	$output = array(
		"status" => false,
		"error" => "Expired Token"
	);
	die(json_encode($output));
}


//Check if the token is tampered
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');
$today = date("Y-m-d");

$status = false;
$error = "No coupons found";

$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}


if(!isset($_POST['singleid']) || $_POST['singleid'] == ''){		
				
	$list = mysql_query("SELECT * FROM `z_couponrules` WHERE `isActive` = 1".$limiter);
	$expired_count = 0;
	while($coupon = mysql_fetch_assoc($list)){
			
			$myrule = json_decode($coupon['rule']);
			
			if($myrule->rule == "PERCENTAGE")
			{
				$output[] = array(
					"code" => $coupon['code'],
					"brief" => $coupon['brief'],
					"rule" => "PERCENTAGE",
					"percentageM" => $myrule->minimumCart,
					"percentageP" => $myrule->percentage,
					"percentageD" => $myrule->maximum,	
					"expiry" => $coupon['to'],
					"created" => $coupon['from'],							
					"list" => $coupon['list'],
					"limit" => $coupon['limit'],
					"used" => $coupon['usage'] 								
				); 
				
				$status = true;
				$error = "";
	
								
			}		
	}
	
	//Analytics
	$analytics_use_check = mysql_fetch_assoc(mysql_query("SELECT SUM(`usage`) as total FROM `z_couponrules` WHERE 1"));
	$used_count = 0;
	if($analytics_use_check['total']){
		$used_count = $analytics_use_check['total'];
	}
	
	$analytics_expire_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`code`) as total FROM `z_couponrules` WHERE `isActive` = 0"));
	$expired_count= 0;
	if($analytics_expire_check['total']){
		$expired_count= $analytics_expire_check['total'];
	}
	
	$analytics_active_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`code`) as total FROM `z_couponrules` WHERE `isActive` = 1"));
	$active_count= 0;
	if($analytics_active_check ['total']){
		$active_count = $analytics_active_check['total'];
	}
	
	
	
	//Result - Mass Request	
	$result = array(
		'status' => $status,
		'error' => $error,
		'response' => $output,
		'analytics_used' => $used_count,
		'analytics_expired' => $expired_count,
		'analytics_active' => $active_count
	);
	
	die(json_encode($result));
		
}
else{
	$coupon = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_couponrules` WHERE `code`='{$_POST['singleid']}'"));
	if($coupon['code'] !== ""){

			$myrule = json_decode($coupon['rule']);
			
			if($myrule->rule == "PERCENTAGE")
			{
				$output = array(
					"code" => $coupon['code'],
					"brief" => $coupon['brief'],
					"rule" => "PERCENTAGE",
					"percentageM" => $myrule->minimumCart,
					"percentageP" => $myrule->percentage,
					"percentageD" => $myrule->maximum,	
					"expiry" => $coupon['to'],
					"created" => $coupon['from'],							
					"list" => $coupon['list'],
					"limit" => $coupon['limit'],
					"used" => $coupon['usage'] 								
				); 
				
				$status = true;
				$error = "";
			}
		
	}
	else{
		$result = array(
			'status' => false,
			'error' => "Invalid Coupon Code"
		);
		
		die(json_encode($result));		
	}
	
	//Result - Single Request
	$result = array(
		'status' => $status,
		'error' => $error,
		'response' => $output
	);
	
	die(json_encode($result));
}



$result = array(
	'status' => $status,
	'error' => $error,
	'response' => $output
);

echo json_encode($result);
		
?>