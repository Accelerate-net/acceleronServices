<?php

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

/*
	Version 	: 1.0
	Author  	: Abhijith C S
	Last Modified 	: 23.08.2019
	Description	: To send delivery order confirmation SMS
*/

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

//SMS Credentials
define('SMS_INCLUDE_CHECK', true);
require 'smsblackbox.php';

require 'errorlist.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => $vegaError_TokenMissing,
		"errorCode" => 400
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
		"error" => $vegaError_TokenExpired,
		"errorCode" => 400
	);
	die(json_encode($output));
}


//Check if the token is tampered
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
	
	
	//TEMPORARY
	if($outlet == "VELACHERY"){
	    
	}
	else{
	 	$output = array(
    		"status" => false,
    		"error" => "SMS feature is currently disabled",
    		"errorCode" => 400
    	);
    	die(json_encode($output));   
	}
}
else{
	$output = array(
		"status" => false,
		"error" => $vegaError_TokenInvalid,
		"errorCode" => 400
	);
	die(json_encode($output));
}


    $guestMobile = $_POST['customerMobile'];
    $billNumber = $_POST['billNumber'];

	//Confirmation SMS to customer
	$greet = "Reach us on 9941990003 www.zaitoon.restaurant";
	$del_time = 45;
	$ready_time = 30;	
	
	$message = "Your order # ".$billNumber." with Zaitoon has been confirmed. It will be delivered in approximately ".$del_time." minutes. ".$greet;

	vegaSendSMS($guestMobile, $message);
	

$msg = array(
	"status" => true,
	"errorCode" => '',
	"error" => ''
);

echo json_encode($msg);
		
?>