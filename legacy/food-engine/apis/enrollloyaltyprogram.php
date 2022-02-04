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

//TEMP

	$output = array(
		"status" => false,
		"error" => "Rewards Program is not commenced yet."
	);
	die(json_encode($output));

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
		"error" => "Session Expired. Login Again."
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

$user_check = mysql_fetch_assoc(mysql_query("SELECT `name`, `isRewardEnabled` FROM `z_users` WHERE `mobile`='{$mobile}'"));
if(!$user_check['name']){
	$output = array(
		"status" => false,
		"error" => "User does not exist"
	);
	die(json_encode($output));
}
else if($user_check['isRewardEnabled'] == 1){
	$output = array(
		"status" => false,
		"error" => $user_check['name']." has already Enrolled for the Rewards Program"
	);
	die(json_encode($output));
}

$nowdate = date("Y-m-j");
mysql_query("UPDATE `z_users` SET `isRewardEnabled`= 1, `rewardsSince`='{$nowdate}' WHERE `mobile`='{$mobile}'");


$output = array(
	"status" => true,
	"error" => $error,
	"response" => ""
);

echo json_encode($output);

?>
