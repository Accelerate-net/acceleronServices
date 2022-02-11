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
else if($user_check['isRewardEnabled'] == 0){
	$output = array(
		"status" => false,
		"error" => "NOTENROLLED"
	);
	die(json_encode($output));
}

$onemonth = date('Ymd', strtotime(' -30 day'));

$plus = 0;
$plusQuery = mysql_fetch_assoc(mysql_query("SELECT SUM(coins) AS plusPoints FROM `z_rewards` WHERE `userID`='{$mobile}' AND `isApproved` = 1 AND `isCredit` = 1 AND `time`>'{$onemonth}'"));
if($plusQuery['plusPoints']) {$plus = $plusQuery['plusPoints'];}

$minus = 0;
$minusQuery = mysql_fetch_assoc(mysql_query("SELECT SUM(coins) AS minusPoints FROM `z_rewards` WHERE `userID`='{$mobile}' AND `isApproved` = 1 AND `isCredit` = 0 AND `time`>'{$onemonth}'"));
if($minusQuery['minusPoints']) {$minus = $minusQuery['minusPoints'];}

$schemeCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE `coinsVolume`<='{$plus}' ORDER BY `index` DESC LIMIT 1"));
$nextIndex = $schemeCheck['index'] + 1;
$nextScheme = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE `index`='{$nextIndex}'"));
$nextCoins = $nextScheme['coinsVolume'] - $plus;

if($plus-$minus >= 50){ //Minimum 50 coins to redeem it as voucher
	$isRedeemAvailable = true;
}
else{
	$isRedeemAvailable = false;
}

	$response = array(
		"points" => $plus-$minus,
		"total" => $plus,
		"club" => $schemeCheck['className'],
		"redeemFlag" => $isRedeemAvailable,
		"nextClubBrief" => "Earn ".$nextCoins." coins more to enter ".$nextScheme['className']." Club"		
	);


$output = array(
	"status" => true,
	"error" => $error,
	"response" => $response
);

echo json_encode($output);

?>
