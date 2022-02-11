<?php

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

/*
	Version 	: 1.0
	Author  	: Abhijith C S
	Last Modified 	: 27.11.2017
	Description	: To confirm an order
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
}
else{
	$output = array(
		"status" => false,
		"error" => $vegaError_TokenInvalid,
		"errorCode" => 400
	);
	die(json_encode($output));
}


$orderid = $_POST['id'];


date_default_timezone_set('Asia/Calcutta');
$time = date("g:i a");

$status = false;
$query = "SELECT * FROM `zaitoon_orderlist` WHERE `orderID`='{$orderid}' AND `outlet`='{$outlet}'";
$all = mysql_query($query);
$order = mysql_fetch_assoc($all);
if(!empty($order)){
	$status = true;
	$query = "UPDATE zaitoon_orderlist SET status = 1 , timeConfirm = '{$time}' WHERE orderID='{$orderid}'";
	mysql_query($query);
	
	//Confirmation SMS to customer
	$greet = "Track your order at www.zaitoon.online";
	$del_time = 45;
	$ready_time = 30;	
	
	if($order['isTakeaway'] == 0){							
	    $message = "Your order # ".$order['orderID']." with Zaitoon has been confirmed. It will be delivered in approximately ".$del_time." minutes. ".$greet;
	}	
	else{
	    $message = "Your order # ".$order['orderID']." with Zaitoon has been confirmed. It will be ready for pick up in approximately ".$ready_time." minutes. ".$greet;	
	} 
	
	
	vegaSendSMS($order['userID'], $message);
	
}

$msg = array(
	"status" => $status,
	"errorCode" => '',
	"error" => ''
);

echo json_encode($msg);
		
?>