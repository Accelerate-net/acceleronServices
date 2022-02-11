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
//if(!isset($_POST['token'])){
//	$output = array(
//		"status" => false,
//		"error" => "Access Token is missing"
//	);
//	die(json_encode($output));
//}

//if(!isset($_POST['type'])){
//	$output = array(
//		"status" => false,
//		"error" => "Content Type is missing"
//	);
//	die(json_encode($output));
//}

//if(!isset($_POST['id'])){
//	$output = array(
//		"status" => false,
//		"error" => "Content ID is missing"
//	);
//	die(json_encode($output));
//}

$id = '107';
$token = 'sHtArttc2ht+tMf9baAeQ9ukHnXtlsHfexmCWx5sJOgQ5pvKAdIowxcvZh93iKswn896szNGszpckbWTUIEp6Q==';
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

date_default_timezone_set('Asia/Calcutta');
$today = date("Y-m-d");

$co_type = 'purchases';

if($co_type == 'promotions' || $co_type == 'coupons' || $co_type == 'purchases'){
	mysql_query("DELETE FROM `z_deals` WHERE `id`='{$id}'");
	unlink("images/contents/".$id.".jpg");
}
else if($co_type == 'combos'){
	mysql_query("DELETE FROM `z_combos` WHERE `code`='{$_POST['id']}'");
	unlink("images/combos/".$_POST['id'].".jpg");
}

$result = array(
	'status' => true,
	'error' => ""
);

echo json_encode($result);
		
?>