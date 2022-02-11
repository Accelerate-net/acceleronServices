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
if($tokenid['mobile']){
	$mobile = $tokenid['mobile'];	
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

include 'mailer/config.php';

$token_check = mysql_fetch_assoc(mysql_query("SELECT `date`, `gmailToken` FROM `z_gtoken` WHERE `code`='ZAITOON'"));

date_default_timezone_set('Asia/Calcutta');
$now = date("Y-m-d");
$dbdate = $token_check['date'];

$error = "DB Date: ".$dbdate." Current Date: ".$now;

if($dbdate >= $now){
	$output = array(
		"status" => true,
		"error" => $error,
		"isValidToken" => true,
		"gtoken" => $token_check['gmailToken'],
		"url" => ""
	);
	die(json_encode($output));
}
else{

	$output = array(
		"status" => true,
		"error" => $error,
		"isValidToken" => false,
		"gtoken" => "",
		"url" => $login_url
	);
	die(json_encode($output));
}

?>