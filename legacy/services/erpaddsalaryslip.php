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
			"error" => "Access Token Missing",
			"errorCode" => 103,
			"response" => ""
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
			"error" => "Login Expired",
			"errorCode" => 401,
			"response" => ""
	);
	die(json_encode($output));
}


//Check if the token is tampered
if($tokenid['outlet']){
	$branch = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

if(!isset($_POST['details'])){
	$output = array(
			"status" => false,
			"error" => "Details Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}

$detailsObj = $_POST['details']; 

$status = false;
$error = "Something went wrong";


$payDate = date("Ymd", strtotime($detailsObj['date']));
$adminMobile = $tokenid['mobile'];


$query = "INSERT INTO `erp_payslips`(`outlet`, `staffCode`, `amount`, `modeOfPayment`, `issuedMonth`, `dateOfPayment`, `paidAdminCode`, `remarks`, `reference`) VALUES ('{$branch}', '{$detailsObj['employeeCode']}', '{$detailsObj['amount']}', '{$detailsObj['mode']}', '{$detailsObj['month']}', '{$payDate}', '{$adminMobile}', '{$detailsObj['comments']}', '{$detailsObj['reference']}' )";

$main = mysql_query($query);

$status = true;
$error = "";

$output = array(
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>