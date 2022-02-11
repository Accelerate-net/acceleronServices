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


$status = false;
$error = 'Details not found';

$pre_takeaway_sum = 0;
$pre_delivery_sum = 0;
$pre_takeaway_count = 0;
$pre_delivery_count = 0;

$cod_takeaway_sum = 0;
$cod_delivery_sum = 0;
$cod_takeaway_count = 0;
$cod_delivery_count = 0;

$query_date = $_POST['fromDate'];
$toDate = $_POST['toDate'];

if(strtotime($query_date) > strtotime($toDate)){
	$output = array(
		"status" => false,
		"error" => "Invalid Date Range"
	);
	die(json_encode($output));
}

while(1){

	//Break if query date exceeds max of date range
	if(strtotime($query_date) > strtotime($toDate)){
		break;
	}
	
	$dateStamp = date("dmY", strtotime($query_date));
	
//PRE
	$query_delivery = "SELECT COUNT(`orderID`) as grandCount, SUM(`paidAmount`) as grandSum FROM `zaitoon_orderlist` WHERE `stamp`='{$dateStamp}' AND `outlet`='{$outlet}' AND `status`='2' AND `isVerified`='1' AND `isTakeaway`='0' AND `modeOfPayment`='PRE'";
	
	$find_delivery = mysql_fetch_assoc(mysql_query($query_delivery));
	
	$query_takeaway = "SELECT COUNT(`orderID`) as grandCount, SUM(`paidAmount`) as grandSum FROM `zaitoon_orderlist` WHERE `stamp`='{$dateStamp}' AND `outlet`='{$outlet}' AND `status`='2' AND `isVerified`='1' AND `isTakeaway`='1' AND `modeOfPayment`='PRE'";
	
	$find_takeaway = mysql_fetch_assoc(mysql_query($query_takeaway));
	
	
	$pre_takeaway_sum += $find_takeaway['grandSum'];
	$pre_delivery_sum += $find_delivery['grandSum'];
	$pre_takeaway_count += $find_takeaway['grandCount'];
	$pre_delivery_count += $find_delivery['grandCount'];	


//COD
	$query_delivery = "SELECT COUNT(`orderID`) as grandCount, SUM(`paidAmount`) as grandSum FROM `zaitoon_orderlist` WHERE `stamp`='{$dateStamp}' AND `outlet`='{$outlet}' AND `status`='2' AND `isVerified`='1' AND `isTakeaway`='0' AND `modeOfPayment`='COD'";
	
	$find_delivery = mysql_fetch_assoc(mysql_query($query_delivery));
	
	$query_takeaway = "SELECT COUNT(`orderID`) as grandCount, SUM(`paidAmount`) as grandSum FROM `zaitoon_orderlist` WHERE `stamp`='{$dateStamp}' AND `outlet`='{$outlet}' AND `status`='2' AND `isVerified`='1' AND `isTakeaway`='1' AND `modeOfPayment`='COD'";
	
	$find_takeaway = mysql_fetch_assoc(mysql_query($query_takeaway));
	
	
	$cod_takeaway_sum += $find_takeaway['grandSum'];
	$cod_delivery_sum += $find_delivery['grandSum'];
	$cod_takeaway_count += $find_takeaway['grandCount'];
	$cod_delivery_count += $find_delivery['grandCount'];
	
	
	
	//Increment by 1 day
	$query_date = date('Y-m-d', strtotime($query_date.' +1 day'));	
	
	
}

//Final Response	
	
	$response = array(
		"pre_sum_takeaway" => $pre_takeaway_sum,
		"pre_sum_delivery" => $pre_delivery_sum,
		"pre_count_takeaway" => $pre_takeaway_count,			
		"pre_count_delivery" => $pre_delivery_count,
		"cod_sum_takeaway" => $cod_takeaway_sum,
		"cod_sum_delivery" => $cod_delivery_sum,
		"cod_count_takeaway" => $cod_takeaway_count,			
		"cod_count_delivery" => $cod_delivery_count
	);

$output = array(
	"status" => true,
	"error" => "",
	"response" => $response
);

die(json_encode($output));		
?>