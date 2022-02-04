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

$system_bill_number = 0;
if(!isset($_POST['systemBillNumber'])){
	$output = array(
		"status" => false,
		"error" => "System bill number is missing"
	);
	die(json_encode($output));
}
else{
    $system_bill_number = $_POST['systemBillNumber'];
}

$grand_total = 0;
if(!isset($_POST['totalBillAmount'])){
	$output = array(
		"status" => false,
		"error" => "Total payable amount is missing"
	);
	die(json_encode($output));
}
else{
    $grand_total = $_POST['totalBillAmount'];
}

$masterOrder = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `master_order_id` ='{$orderId}'"));
if($masterOrder['order_status'] != 0){
	$output = array(	
		"status" => false,
		"error" =>"Already invoiced order"
	);
	die(json_encode($output));
}

//Payment enabled check in given Outlet
$masterBranch = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_branch_master` WHERE `branch` ='{$outlet}'"));
if($masterBranch['enable_smart_order'] == 0 || $masterBranch['is_payment_enabled'] == 0){
	$output = array(	
		"status" => false,
		"error" =>"Smart order or Payment option not enabled for this outlet"
	);
	die(json_encode($output));
}

//Payment enabled check on given table
$masterTable = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_table_mapping` WHERE `assigned_branch` ='{$outlet}' AND `qr_code` = '{$masterOrder['qr_code_reference']}' AND is_active = 1"));
if($masterTable['is_maintain_mode'] == 1 || $masterTable['is_payment_enabled'] == 0){
	$output = array(	
		"status" => false,
		"error" =>"Payment option not enabled on this table"
	);
	die(json_encode($output));
}

//Check if any order pending or atleast one order accepted
$subOrderQuery = mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` ='{$orderId}' AND is_active = 1 AND status IN (0, 1, 5)");
$subOrder_pending = 0;
$subOrder_accepted = 0;
$subOrder_rejected = 0;
while($subOrder = mysql_fetch_assoc($subOrderQuery)){
    $statusCode = $subOrder['status'];
    if($statusCode == 0){
        $subOrder_pending++;
    }
    else if($statusCode == 1){
        $subOrder_accepted++;
    }
    else if($statusCode == 5){
        $subOrder_rejected++;
    }
}

//Some are in pending state
if($subOrder_pending > 0){
    $output = array(	
		"status" => false,
		"error" =>"Generate bill only after accepting all running orders"
	);
	die(json_encode($output));
}

//No accepted orders found
if($subOrder_accepted == 0){
    $output = array(	
		"status" => false,
		"error" =>"No accepted orders found"
	);
	die(json_encode($output));
}


/* Razorpay ID Creation */
use Razorpay\Api\Api;
$razor_order_id = '';

$razorpay_key = $masterBranch['razorpay_key'];
$razorpay_password = $masterBranch['razorpay_secret'];

$api = new Api($razorpay_key, $razorpay_password);
$razor_order  = $api->order->create(array('receipt' => 'R#'.$orderId, 'amount' => $grand_total*100, 'currency' => 'INR', 'payment_capture' => 1));
$razor_order_id = $razor_order->id;

mysql_query("UPDATE `smart_master_orders` SET `total_bill_amount`='{$grand_total}', `order_status` = 1,`razorpay_order_id`='{$razor_order_id}', `razorpay_status`= 1,`system_bill_number`={$system_bill_number} WHERE `master_order_id` = '{$orderId}'");

$output = array(
	"status" => true,
	"data" => "Successfully generated invoice"
);

echo json_encode($output);
?>
