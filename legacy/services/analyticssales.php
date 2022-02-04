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


$n = 1;
$current_week_date = date("dmY", strtotime("-8 days"));
$previous_week_date = date('dmY', strtotime("-15 days"));

$current_week_sum = 0;
$previous_week_sum = 0;

$current_week_count = 0;
$previous_week_count = 0;

while($n <= 7){ //Iterate for 1 week (n = 7)

	
	$current_week = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) AS totalSales, COUNT(`orderID`) AS totalCount FROM `zaitoon_orderlist` WHERE `stamp`='{$current_week_date}' AND `outlet`='{$outlet}' AND `isVerified`='1' AND `status`='2'"));
	
	$previous_week = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) AS totalSales, COUNT(`orderID`) AS totalCount FROM `zaitoon_orderlist` WHERE `stamp`='{$previous_week_date}' AND `outlet`='{$outlet}' AND `isVerified`='1' AND `status`='2'"));
	
	
	$list_current_week[] = array(
			'index' => $n,
			'date' => date('d-m-Y', strtotime("-".(8-$n)." days")),
			'sales' => $current_week['totalSales']
	);
	$current_week_sum = $current_week_sum + $current_week['totalSales'];
	$current_week_count = $current_week_count + $current_week['totalCount'];
	
	
	$list_previous_week[] = array(
			'index' => $n,
			'date' => date('d-m-Y', strtotime("-".(15-$n)." days")),
			'sales' => $previous_week['totalSales']
	);
	$previous_week_sum = $previous_week_sum + $previous_week['totalSales'];
	$previous_week_count = $previous_week_count + $previous_week['totalCount'];
			
	
	$n++;
	
	$current_week_date = date('dmY', strtotime("-".(8-$n)." days"));
	$previous_week_date = date('dmY',strtotime("-".(15-$n)." days"));
}

//Customer Stats
$user_count = mysql_fetch_assoc(mysql_query("SELECT COUNT(`mobile`) AS totalUsers FROM `z_users` WHERE 1"));
$mobile_orders = mysql_fetch_assoc(mysql_query("SELECT COUNT(`orderID`) AS total FROM `zaitoon_orderlist` WHERE `platform`='MOB' AND `status`='2' AND `outlet`='{$outlet}' AND `isVerified`='1'"));
$web_orders = mysql_fetch_assoc(mysql_query("SELECT COUNT(`orderID`) AS total FROM `zaitoon_orderlist` WHERE `platform`='WEB' AND `status`='2' AND `outlet`='{$outlet}' AND `isVerified`='1'"));

//Branch Wise
$branch_info = mysql_query("SELECT `code`, `name` FROM `z_outlets` WHERE 1");
while($current_outlet = mysql_fetch_assoc($branch_info)){
	$outlet_sum = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) AS amount FROM `zaitoon_orderlist` WHERE `status`='2' AND `outlet`='{$current_outlet['code']}' AND `isVerified`='1'"));
	$outletData[] = array(
		"name" => $current_outlet['name'],
		"amount" => $outlet_sum['amount']
	);
}

$output = array(
	"status" => true,
	"error" => "",
	"current" => $list_current_week,
	"previous" => $list_previous_week,
	"currentSum" => $current_week_sum,
	"previousSum" => $previous_week_sum,
	"currentCount" => $current_week_count,
	"previousCount" => $previous_week_count,
	"totalUsers" => $user_count['totalUsers'],
	"ordersMobile" => $mobile_orders['total'],
	"ordersWeb" => $web_orders['total'],
	"ordersTotal" => $mobile_orders['total'] + $web_orders['total'],
	"outletInfo" => $outletData
	
);

echo json_encode($output);
		
?>