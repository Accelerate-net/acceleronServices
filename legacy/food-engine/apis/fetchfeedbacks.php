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


$list = "";
$limiter = "";

if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 5";	
}

$status = false;
$error = 'No feedbacks found';

//Filter Mode
$filter = "WHERE `feedback` != 'NA' AND `outlet`='".$outlet."' ORDER BY `orderID` DESC ";
if(isset($_POST['isFilter']) && $_POST['isFilter'] == 1){

	if($_POST['filter'] == 'MOST'){
		$filter = "WHERE `feedback` != 'NA' AND `outlet`='".$outlet."' ORDER BY `stars` DESC, `orderID` DESC ";
	}
	else if($_POST['filter'] == 'LEAST'){
		$filter = "WHERE `feedback` != 'NA' AND `outlet`='".$outlet."' ORDER BY `stars`, `orderID` DESC ";
	}
	else if($_POST['filter'] == 'COMMENTS'){
		$filter = "WHERE `feedback` != 'NA' AND `outlet`='".$outlet."' AND `feedComments` !='' ORDER BY `isFeedbackAttended`, `orderID` DESC ";
	}
	else{
		$filter = "WHERE `feedback` != 'NA' AND `outlet`='".$outlet."' ORDER BY `orderID` DESC ";
	}
}


$query = "SELECT orderID, userID, timeFeedback, isFeedbackAttended, feedback FROM `zaitoon_orderlist` ".$filter.$limiter;
$error = $query;
$all = mysql_query($query);
while($order = mysql_fetch_assoc($all)){

	$userinfo = mysql_fetch_assoc(mysql_query("SELECT name, email FROM z_users WHERE mobile='{$order['userID']}'"));
	
	$status = true;
	$error = '';
	
	$cart = json_decode($order['cart']);
	$list[] = array(
		'orderID' => $order['orderID'], 
		'userID' => $order['userID'],
		'time' => $order['timeFeedback'],
		'isAttended' => $order['isFeedbackAttended'] == 1 ? true: false,
		'userName' => $userinfo['name'],
		'email' => $userinfo['email'],		
		'feedback' => json_decode($order['feedback'])                               
	);
}

$output = array(
	"status" => $status,
	"error" => $error,
	"test" => $query,
	"response" => $list
);

echo json_encode($output);
		
?>