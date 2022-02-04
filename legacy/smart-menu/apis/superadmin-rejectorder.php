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

$outlet = "JPNAGAR";
$master_order_id = $_POST['masterOrderId'];
$sub_order_id = $_POST['subOrderId'];

function errorResponse($error){
    $output = array(
		"status" => false,
		"error" => $error
	);
	die(json_encode($output));
}

$orderCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `branch` = '{$outlet}' AND is_active = 1 AND order_status != 2 AND master_order_id = '{$master_order_id}'"));
$isFailed = true;
if($orderCheck['master_order_id'] == $master_order_id){
    $subOrderCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$master_order_id}' AND `order_id` = '{$sub_order_id}' AND `is_active` = 1 AND status = 0"));
    if($subOrderCheck['order_id'] == $sub_order_id){
        mysql_query("UPDATE `smart_orders` SET `status`= 5 WHERE `order_id` = '{$sub_order_id}' AND `fk_master_order` = '{$master_order_id}'");
        $isFailed = false;
    }
}

$finalOutput = array(
    "status" => !$isFailed,
    "data" => $isFailed ? "Error: Failed to Reject the order" : "The order is successfully rejected"
);

die(json_encode($finalOutput));

?>
