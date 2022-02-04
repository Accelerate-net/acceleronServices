<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

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


$orderstatus = $_POST['status'];
$key = $_POST['key'];

$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 3";	
}

$customstatus = "1";
if(isset($_POST['status'])){
	$customstatus = "`status`='{$_POST['status']}'";	
}


$status = false;
$error = 'Not authorized';
$result = "Not Found";

$list = "";
$recent = "";

$failFound = 0;
$orderFound = 0;

//Recent failed orders 

$query = "SELECT * FROM `zaitoon_orderlist` WHERE `userID`='{$key}' AND `modeOfPayment`='PRE' AND `isVerified`='0' ORDER BY `orderID` DESC".$limiter;
$all = mysql_query($query);
while($order = mysql_fetch_assoc($all)){

	$userinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_users WHERE mobile='{$order['userID']}'"));
	$agentinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_roles WHERE code='{$order['agentMobile']}'"));
	$outletinfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_outlets` WHERE `code`='{$order['outlet']}'"));	
	
	$status = true;
	$error = '';
	
	$failFound = 1;
	
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
		'timeDispatch' => $order['timeDispatch'], 		
		'timeDeliver' => $order['timeDeliver'],
		'comments' => $order['comments'],
		'agentCode' => $order['agentMobile'],
		'platform' => $order['platform'],
		'agentName' => $agentinfo['name'],		
		'isPrepaid' => $order['modeOfPayment'] == "PRE"?true:false,
		'isVerified' => $order['isVerified']==1?true:false, 
		'amount' => $order['paidAmount'],		
		'feedback' => $order['feedback']  ,
		'outlet' => $outletinfo['name']                               
	);
}


//Last Order
$query = "SELECT * FROM `zaitoon_orderlist` WHERE `userID`='{$key}' AND `isVerified`='1' ORDER BY `orderID` DESC LIMIT 1";
$all = mysql_query($query);
while($order = mysql_fetch_assoc($all)){

	$userinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_users WHERE mobile='{$order['userID']}'"));
	$agentinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_roles WHERE code='{$order['agentMobile']}'"));
	$outletinfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_outlets` WHERE `code`='{$order['outlet']}'"));	
	
	$status = true;
	$error = '';
	
	$orderFound = 1;
	
	$cart = json_decode($order['cart']);
	$recent = array(
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
		'timeDispatch' => $order['timeDispatch'], 		
		'timeDeliver' => $order['timeDeliver'],
		'comments' => $order['comments'],
		'agentCode' => $order['agentMobile'],
		'platform' => $order['platform'],
		'agentName' => $agentinfo['name'],		
		'isPrepaid' => $order['modeOfPayment'] == "PRE"?true:false,
		'amount' => $order['paidAmount'],
		'isVerified' => $order['isVerified']==1?true:false, 		
		'feedback' => $order['feedback'],
		'outlet' => $outletinfo['name']                            
	);
}



$output = array(
	"status" => $status,
	"error" => $error,
	"failFound" => $failFound == 1? true: false,
	"failed" => $list,
	"recentFound" => $orderFound == 1? true: false,
	"recent" => $recent
);

echo json_encode($output);
		
?>