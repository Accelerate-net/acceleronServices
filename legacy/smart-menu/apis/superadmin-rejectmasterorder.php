<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

//Razorpay APIs
require 'razorpay/Razorpay.php';

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

// if($interval > $tokenExpiryDays){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Expired Token"
// 	);
// 	die(json_encode($output));
// }
*/
$outlet = "JPNAGAR";

$orderId = 0;
if(!isset($_POST['masterorder'])){
	$output = array(
		"status" => false,
		"error" => "Master order id is missing"
	);
	die(json_encode($output));
}
else{
    $orderId = $_POST['masterorder'];
}


$masterOrder = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `master_order_id` ='{$orderId}' AND `is_active` = 1"));
if($masterOrder['order_status'] != 0){
	$output = array(	
		"status" => false,
		"error" =>"Already invoiced order can't be rejected"
	);
	die(json_encode($output));
}

mysql_query("UPDATE `smart_master_orders` SET `order_status` = 5 WHERE `master_order_id` = '{$orderId}'");

$output = array(
	"status" => true,
	"data" => "Successfully rejected the order"
);

echo json_encode($output);
?>
