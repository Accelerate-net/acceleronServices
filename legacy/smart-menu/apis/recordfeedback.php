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

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['masterOrderId'])){
	$output = array(
		"status" => false,
		"error" => "Order ID is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['rating'])){
	$output = array(
		"status" => false,
		"error" => "Rating is missing"
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
		"error" => "User session timeout"
	);
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');

$tags = json_encode($_POST['tags']);
$comments = json_encode(mysql_real_escape_string($_POST['comments']));
mysql_query("UPDATE `smart_master_orders` SET `feedback_rating` = '{$_POST['rating']}', `feedback_tags` = '{$tags}', `feedback_comments` = '{$comments}', `feedback_status` = 1  WHERE `master_order_id` = '{$_POST['masterOrderId']}' AND `feedback_status` = 0 AND `is_active` = 1 AND `order_status` = 2");

$output = array(
	"status" => true,
	"error" => ""
);

echo json_encode($output);

?>
