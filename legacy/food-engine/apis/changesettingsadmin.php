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
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

if(!isset($_POST['type'])){
	$output = array(
		"status" => false,
		"error" => "Type is missing"
	);
	die(json_encode($output));
}
else{
	$mode = $_POST['type'];
}


if(!isset($_POST['status'])){
	$output = array(
		"status" => false,
		"error" => "Status is missing"
	);
	die(json_encode($output));
}
else{
	$status = $_POST['status'];
	
	if($status != 0 && $status != 1){
		$output = array(
			"status" => false,
			"error" => "Status can not be ".$status
		);
		die(json_encode($output));	
	}
}





if($mode == 'PAYMENT'){
	mysql_query("UPDATE `z_outlets` SET `isAcceptingOnlinePayment` = '{$status}' WHERE `code` = '{$outlet}'");
}
else if($mode == 'REWARD'){
	mysql_query("UPDATE `z_outlets` SET `isRewardsEnabled` = '{$status}' WHERE `code` = '{$outlet}'");
} 
else if($mode == 'RESERVATION'){
	mysql_query("UPDATE `z_outlets` SET `isReservationAllowed` = '{$status}' WHERE `code` = '{$outlet}'");
}

	$output = array(
		"status" => true,
		"action" => $status == 1? true: false		
	);

echo json_encode($output);

?>
