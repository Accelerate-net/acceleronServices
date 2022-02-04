<?php
//Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

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
		"error" => ""
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
		"error" => ""
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
		"error" => ""
	);
	die(json_encode($output));
}


//User block check
$block_check = mysql_fetch_assoc(mysql_query("SELECT `isBlocked`  FROM `z_users` WHERE `mobile`='{$mobile}'"));
if($block_check['isBlocked'] == 1){
	$output = array(
		"status" => false,
		"error" => "This number (".$mobile.") has been blocked. Contact care@zaitoon.online for assistance."
	);
	die(json_encode($output));
}


date_default_timezone_set('Asia/Calcutta');
$date = date("Y-m-j");

$loginjson = array(
	"mobile" => $tokenid['mobile'],
	"date" => $date
);

$textToEncrypt = json_encode($loginjson);
//To encrypt
$encryptedMessage = openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);
$newtoken = $encryptedMessage;

$status = true;

$output = array(
	"status" => $status,
	"error" => $error,
	"newtoken" => $newtoken
);

echo json_encode($output);
?>
