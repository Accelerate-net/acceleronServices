<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require '../secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);
/*
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
$outlet = "";
// if($tokenid['outlet']){
// 	$outlet = $tokenid['outlet'];
// }
// else{
// 	$output = array(
// 		"status" => false,
// 		"error" => "Token is tampered"
// 	);
// 	die(json_encode($output));
// }

*/
date_default_timezone_set('Asia/Calcutta');

$outlet = "AMBUR";

$configuredQrCodes = [];

$tableQuery = mysql_query("SELECT * FROM `smart_table_mapping` WHERE `assigned_branch` = '{$outlet}' ORDER BY CAST(assigned_table as SIGNED INTEGER) ASC");
while($listedTable = mysql_fetch_assoc($tableQuery)){
    
    $stewardName = "Unknown";
    $stewardCode = "Unregistered";
    
    $myTableNumber = $listedTable['assigned_table'];
    $captainCodeQuery = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_steward_table_mapping` WHERE `table_number` = '{$myTableNumber}' AND branch = '{$outlet}'"));
    
    if($captainCodeQuery['steward_code']){
        $stewardData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_stewards` WHERE `code` = '{$captainCodeQuery['steward_code']}'"));
        $stewardName = $stewardData['name'] && $stewardData['name'] != "" ? $stewardData['name'] : "Unknown";
        $stewardCode = $captainCodeQuery['steward_code'];
    }
    
	$configuredQrCodes[] = array(
		"table" => $listedTable['assigned_table'],
		"label" => $listedTable['assigned_table'],
		"branch" => $outlet,
		"qrCode" => $listedTable['qr_code'],
		"isQrEnabled" => $listedTable['is_active'] == 1 ? true : false,
		"isMaintainMode" => $listedTable['is_maintain_mode'] == 0 ? false : true,
		"isPaymentEnabled" => $listedTable['is_payment_enabled'] == 0 ? false : true,
		"lastUpdated" => $listedTable['date_updated'],
		"isCaptainAssigned" => $captainCodeQuery['steward_code'] ? true : false,
		"assignedCaptainCode" => $stewardCode,
		"assignedCaptainName" => $stewardName
	); 
}

$finalOutput = array(
    "status" => true,
    "data" => $configuredQrCodes
);

die(json_encode($finalOutput));

?>
