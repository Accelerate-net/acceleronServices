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
		"error" => "Expired Token"
	);
	die(json_encode($output));
}



//Check if the token is tampered
if($tokenid['outlet']){
	$outlet= $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}




$queryDate = date("d-m-Y"); 

//Reservations Count 
$reservationsCount = 0;
$reservations_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`stamp`) as total FROM `z_reservations` WHERE `date`='{$queryDate}' AND `outlet`='{$outlet}' AND `status` < 2"));
if($reservations_check['total'] != ""){
	$reservationsCount = $reservations_check['total'];
}

//Orders Count
$ordersCount = 0;
$orders_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`orderID`) as total FROM `zaitoon_orderlist` WHERE `status`=0 AND `isVerified`=1 AND `outlet`='{$outlet}'"));
if($orders_check['total'] != ""){
	$ordersCount = $orders_check['total'];
}


//Help Count
$helpCount = 0;
$help_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `z_helpdesk` WHERE `status`=0"));
if($help_check['total'] != ""){
	$helpCount = $help_check['total'];
}
	
	
//Smart Orders Count
$smartOrdersCount = 0;
$smart_orders_check = mysql_query("SELECT fk_master_order, count(*) as count FROM smart_orders WHERE fk_master_order IN (SELECT master_order_id from smart_master_orders WHERE branch = '{$outlet}' AND is_active = 1 AND order_status != 2) AND is_active = 1 AND status = 0 GROUP BY fk_master_order");
$smartOrdersCount = mysql_num_rows($smart_orders_check);

$output = array(
	"status" => true,
	"error" => "",
	"reservationsCount" => $reservationsCount,
	"ordersCount" => $ordersCount,
	"smartOrdersCount" => $smartOrdersCount,
	"helpCount" => $helpCount
);

echo json_encode($output);

?>
