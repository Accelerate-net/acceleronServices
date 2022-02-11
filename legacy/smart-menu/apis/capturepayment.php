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

// //Encryption Validation
// if(!isset($_POST['token'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Access Token is missing"
// 	);
// 	die(json_encode($output));
// }

// $token = $_POST['token'];
// $decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
// $tokenid = json_decode($decryptedtoken, true);

// //Expiry Validation
// date_default_timezone_set('Asia/Calcutta');
// $dateStamp = date_create($tokenid['date']);
// $today = date_create(date("Y-m-j"));
// $interval = date_diff($dateStamp, $today);
// $interval = $interval->format('%a');

// if($interval > $tokenExpiryDays){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Expired Token"
// 	);
// 	die(json_encode($output));
// }


// //Check if the token is tampered
// if($tokenid['mobile']){
// 	$userID = $tokenid['mobile'];
// 	$mobile = $tokenid['mobile'];
// }
// else{
// 	$output = array(
// 		"status" => false,
// 		"error" => "Token is tampered"
// 	);
// 	die(json_encode($output));
// }


if(!isset($_POST['orderId'])){
	$output = array(
		"status" => false,
		"error" => "Order ID is missing"
	);
	die(json_encode($output));
}
else{
    $orderId = $_POST['orderId'];
}

$paymentOrder = $_POST['paymentOrder'];
$transactionId = $_POST['transactionId'];

mysql_query("UPDATE `smart_master_orders` SET `razorpay_transaction_id`='{$transactionId}', `razorpay_status`= 2, `mode_of_payment` = 1, `order_status` = 2 WHERE `razorpay_order_id` = '{$paymentOrder}' AND `master_order_id` = '{$orderId}'");

$output = array(
	"status" => true,
	"error" => "Payment Successful"
);
die(json_encode($output));
?>
