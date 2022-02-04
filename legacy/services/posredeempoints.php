<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
sleep(11);
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
		"errorCode" => 404,
		"error" => "Security Token is missing. Please login again to prove your identity."
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
		"errorCode" => 404,
		"error" => "Security Token is too old. Please login again to prove your identity."
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
		"errorCode" => 404,
		"error" => "Something suspicious noticed. Please login again to prove your identity."
	);
	die(json_encode($output));
}

$mobile = $_POST['mobile'];

if(!isset($_POST['totalBillAmount'])){
	$output = array(
		"status" => false,
		"error" => "Bill Amount is missing"
	);
	die(json_encode($output));
}
else{
	$totalBillAmount = $_POST['totalBillAmount'];
}


//DO THE NECESSARY CODING HERE TO FIND COUPON DATA
	
$status = true;
$error = "";

$isValid = true;
$validityError = 'Minimum bill of Rs. 450';
$totalDiscount = $totalBillAmount * 0.2;
$totalPoints = 100;
	
	
$output = array(
	"status" => $status,
	"error" => $error,
	"isValid" => $isValid,
	"validityError" => $validityError,
	"discount" => $totalDiscount,
	"pointsRedeemed" => $totalPoints,
	"referenceID" => 'ADMADAMDAD'

);

//REFERENCE ID --> once against the data table

echo json_encode($output);

?>
