<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');


/*
	Version 	: 1.0
	Author  	: Abhijith C S
	Last Modified 	: 29.11.2017
	Description	: To set the item availability 
*/




error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

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

if(!isset($_POST['status'])){
	$output = array(
		"status" => false,
		"error" => "Status to be set is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['code'])){
	$output = array(
		"status" => false,
		"error" => "Item Code to be changed is missing"
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




	$special_menu = "z_menu";

	$check = mysql_fetch_assoc(mysql_query("SELECT code, isSpecial FROM z_outlets WHERE code='{$outlet}'"));
	if($check['isSpecial'] == 1){
		//Special Menu for IIT Madras etc
		$special_menu = "z_menu_".$check['code'];
	}
	

$code = $_POST['code'];
$status = $_POST['status'];

$query = "UPDATE ".$special_menu." SET ".$outlet." = '{$status}' WHERE code='{$code}'";


mysql_query($query);

$msg = array(
	'status' => true
);

echo json_encode($msg);
		
?>