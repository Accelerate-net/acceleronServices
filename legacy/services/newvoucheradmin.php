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

if(!isset($_POST['details'])){
	$output = array(
		"status" => false,
		"error" => "Voucher Details are missing"
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
	$admin_mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');
$date = date("j F, Y");
$time = date("g:i a");

$dateStamp = date("Ymd");

$details = $_POST['details'];

//Random Voucher or System Generated
if($details['code'] != ''){
	$my_code = $details['code'];	
	
}
else{
	//Generate Random by System	

		//Voucher Generator
		$my_code = generateCoupon();
		while(1){
			$duplicate_check = mysql_fetch_assoc(mysql_query("SELECT `code` FROM `z_vouchers` WHERE `code`='{$my_code}'"));
			if($duplicate_check['code'] != ""){ //duplicate found				
				$my_code = generateCoupon();				
			}
			else{					
				break;
			}
		}				
}

		
		function generateCoupon(){
			$characters = '23456789ABCDEFGHJKLMNPQRSTWXYZ';
    			$randomString = '';
    			for ($i = 0; $i < 12; $i++) {
        			$randomString .= $characters[rand(0, 29)];
    			}
			return $randomString;
		}
		


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


$my_expiry = date('Ymd', strtotime($details['date']));


	$status = true;
	$error = "";
	
	$query = "INSERT INTO `z_vouchers`(`createdDate`, `code`, `value`, `minAmount`, `expiry`, `selfCreated`, `adminUser`, `isRestricted`, `userRestriction`, `isActive`) VALUES ('{$dateStamp}', '{$my_code}', '{$details['value']}', '{$details['minAmount']}', '{$my_expiry}', 0, '{$admin_mobile}', 1, '{$details['mobile']}', 1)";
	$main = mysql_query($query);

$output = array(
	"status" => $status,
	"error" => $query
);

echo json_encode($output);
?>
