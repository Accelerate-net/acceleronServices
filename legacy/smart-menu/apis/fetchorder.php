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

date_default_timezone_set('Asia/Calcutta');

$outlet = "VELACHERY";

$master_order_id = 0;
if(!isset($_POST['masterorder'])){
	$output = array(
		"status" => false,
		"error" => "Master order id is missing"
	);
	die(json_encode($output));
}
else{
    $master_order_id = $_POST['masterorder'];
}


$status = false;
$finalOrderData = [];

$masterQuery = mysql_query("SELECT * FROM `smart_master_orders` WHERE `master_order_id` = '{$master_order_id}' AND `is_active` = 1");
if($orderMaster = mysql_fetch_assoc($masterQuery)){
    $activeOrder = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$master_order_id}' AND `is_active` = 1 AND `status` = 0"));
    if($activeOrder['order_id']){
        $status = true;
    
    	$finalOrderData = array(
    		"orderId" => $activeOrder['order_id'],
    		"cart" => $activeOrder['cart'] ? json_decode($activeOrder['cart']) : [],
    		"extraCharges" => $activeOrder['extra_charges'] ? json_decode($activeOrder['extra_charges']) : [],
    		"comments" => $activeOrder['comments'],
    		"status" => $activeOrder['status'],
    		"orderDate" => $activeOrder['date_created'],
    		"totalAmount" => $activeOrder['total_amount']
    	); 
    }
}

$finalOutput = array(
    "status" => $status,
    "data" => $finalOrderData
);

die(json_encode($finalOutput));

?>
