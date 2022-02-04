<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

/*
	Version 	: 1.0
	Author  	: Abhijith C S
	Last Modified 	: 18.03.2018
	Description	: To list out the orders 
*/

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

require 'errorlist.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => $vegaError_TokenMissing,
		"errorCode" => 400
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
		"error" => $vegaError_TokenExpired,
		"errorCode" => 400
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
		"error" => $vegaError_TokenInvalid,
		"errorCode" => 400
	);
	die(json_encode($output));
}


$orderstatus = $_POST['status'];
$list = "";
$limiter = "";

if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 5";	
}
$status = false;
$error = '';
$query = "SELECT * FROM `zaitoon_orderlist` WHERE `status`='{$orderstatus}' AND `outlet`='{$outlet}' AND `isVerified`=1 ORDER BY `orderID` DESC".$limiter;
$all = mysql_query($query);
while($order = mysql_fetch_assoc($all)){

	$userinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_users WHERE mobile='{$order['userID']}'"));
	
	$status = true;
	$error = '';
	
	$cart = json_decode($order['cart']);
	$list[] = array(
		'orderID' => $order['orderID'], 
		'userID' => $order['userID'],
		'userName' => $userinfo['name'],
		'status' => $order['status'],  
		'cart' => $cart,
		'deliveryAddress' => json_decode($order['deliveryAddress']),
		'date' => $order['date'], 
		'isTakeaway' => $order['isTakeaway']==1?true:false,
		'timePlace' => $order['timePlace'], 
		'timeConfirm' => $order['timeConfirm'], 
		'timeDeliver' => $order['timeDeliver'],
		'comments' => str_replace("'", "", $order['comments']),
		'isPrepaid' => $order['modeOfPayment'] == "PRE"?true:false,
		'amountPaid' => $order['paidAmount'],	
		'platform' => $order['platform'],				
		'feedback' => $order['feedback']                                
	);
}
$count = "";
$query1 = "SELECT `orderID` FROM zaitoon_orderlist WHERE `status`='{$orderstatus}' AND `outlet`='{$outlet}' AND `isVerified`=1";
$count = mysql_query($query1);
$rowcount = mysql_num_rows($count);

$output = array(
	"status" => $status,
	"count" => $rowcount,
	"errorCode" => '',
	"error" => $error,
	"response" => $list
);

echo json_encode($output);
		
?>