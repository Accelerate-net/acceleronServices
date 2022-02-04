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

if(!isset($_POST['code'])){
	$output = array(
		"status" => false,
		"error" => "Voucher Code missing"
	);
	die(json_encode($output));
}



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
	$outlet= $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$my_code = $_POST['code'];

if(!ctype_alnum($my_code)){
	$output = array(
		"status" => false,
		"error" => "Voucher Code can contain only letters and numbers"
	);
	die(json_encode($output));
}

if(strlen($my_code) > 12){
	$output = array(
		"status" => false,
		"error" => "Voucher Code should not be more than 12 characters long"
	);
	die(json_encode($output));
}
else if(strlen($my_code) < 6){
	$output = array(
		"status" => false,
		"error" => "Voucher Code should be minimum 6 characters long"
	);
	die(json_encode($output));
}

//Check clash in voucher list or coupons list
$voucher_check = mysql_fetch_assoc(mysql_query("SELECT `code` FROM `z_vouchers` WHERE `code`='{$my_code}' AND `isActive`=1"));
$coupon_check = mysql_fetch_assoc(mysql_query("SELECT `code` FROM `z_couponrules` WHERE `code`='{$my_code}' AND `isActive`=1"));

if($voucher_check['code'] != ''){
	$output = array(
		"status" => false,
		"error" => "Voucher Code already exists"
	);
	die(json_encode($output));
}
else if($coupon_check['code'] != ''){
	$output = array(
		"status" => false,
		"error" => "A Coupon Code already exists with same name"
	);
	die(json_encode($output));
}
	

$output = array(
	"status" => true,
	"error" => "",
	"response" => ""
);

die(json_encode($output));

?>

