<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');


/*
	Version 	: 1.0
	Author  	: Abhijith C S
	Last Modified 	: 28.11.2017
	Description	: To send response to helpline mail
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



if(!isset($_POST['id'])){
	$output = array(
		"status" => false,
		"error" => "Query reference ID is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['replyText'])){
	$output = array(
		"status" => false,
		"error" => "Content can not be empty"
	);
	die(json_encode($output));
}

if(!isset($_POST['replyEmail'])){
	$output = array(
		"status" => false,
		"error" => "Add Email ID"
	);
	die(json_encode($output));
}

if(!isset($_POST['replySubject'])){
	$output = array(
		"status" => false,
		"error" => "Subject can not be empty"
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
	$admin_mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => $vegaError_TokenInvalid,
		"errorCode" => 400
	);
	die(json_encode($output));
}



$status = false;
$error = "Something went wrong";

$myContent = $_POST['replyText'];

date_default_timezone_set('Asia/Calcutta');
$now = date("g:i a").' '.date("d-m-Y");

		mysql_query("UPDATE `z_helpdesk` SET `replyAgent`='{$admin_mobile}', `dateReply`='{$now}', `status`=1, `reply`='{$myContent}' WHERE `id`='{$_POST['id']}'");

		$agent = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$admin_mobile}'")); 
		$response = array(			
			"replyDate" => $now,
			"replyContent" => $myContent,
			"replyAgent" => $agent['name']							
		);
		        		
		
	$output = array(
		"status" => true,
		"error" => $error,
		"errorCode" => '',
		"response" => $response
	);

die(json_encode($output));

?>
