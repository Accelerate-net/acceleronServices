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

//Razorpay APIs
require 'razorpay/Razorpay.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['orderID'])){
	$output = array(
		"status" => false,
		"error" => "Order ID is missing"
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
		"error" => "Login Expired. Login again."
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
		"error" => "Invalid Access Token. Login again."
	);
	die(json_encode($output));
}

$orderID = $_POST['orderID'];
$paymentID = $_POST['transactionID'];
$razor_order_id_initial = $_POST['razorpay_order_id'];
$razor_signature_initial = $_POST['razorpay_signature'];
$todayNow = date('dmY');

/* Process Razorpay Payment */
use Razorpay\Api\Api;
$api = new Api('rzp_live_4NeEyLZf2m10Ry', 'bLIuGcYQavAQad1idI8FNyXC');
$razor_order  = $api->order->fetch($razor_order_id_initial);
$razor_amount = $razor_order->amount;

//Amount Validation
$amount_check = mysql_fetch_assoc(mysql_query("SELECT `paidAmount` FROM  `zaitoon_passeslist` WHERE `orderID`='{$orderID}' AND `razorpay_order_id` ='{$razor_order_id_initial}'"));

if(!$amount_check['paidAmount']){
	$output = array(
		"status" => false,
		"error" => "Something gone wrong. Contact us if your amount was debited."
	);
	die(json_encode($output));
}
else if($amount_check['paidAmount']*100 != $razor_amount){
	$output = array(
		"status" => false,
		"error" => "Full amount was not received. Pleace contact support@accelerate.net.in"
	);
	die(json_encode($output));
}

  $generated_signature = hash_hmac('sha256', $razor_order_id_initial."|".$paymentID, 'bLIuGcYQavAQad1idI8FNyXC');
  if ($generated_signature != $razor_signature_initial) {
	$output = array(
		"status" => false,
		"error" => "Something gone wrong. Pleace contact support@accelerate.net.in"
	);
	die(json_encode($output));
  }
 
 
 
mysql_query("UPDATE `zaitoon_passeslist` SET `paymentStatus`= 1, `transactionID`='{$paymentID}',`isVerified`= 1 WHERE `orderID`='{$orderID}' AND `razorpay_order_id` ='{$razor_order_id_initial}'");


$output = array(
	"status" => true,
	"error" => "",
	"orderid" => $orderID
);
echo json_encode($output);

?>
