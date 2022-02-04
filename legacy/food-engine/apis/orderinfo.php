<?php
//Headers
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
		"error" => "Expired Token"
	);
	die(json_encode($output));
}



$oid = $_POST['orderID'];

$query = "SELECT * FROM `zaitoon_orderlist` WHERE `orderID`='{$oid}'";
$all = mysql_query($query);
$order = mysql_fetch_assoc($all);

$list = "";

$status = false;
$error = 'User is not Authorized';

if($order['userID'] == $tokenid['mobile'])
{
	//Delivery agent
	$agent = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_deliveryagents` WHERE code='{$order['agentMobile']}'"));
	if($agent['name'] != ""){
		$myAgentName = $agent['name'];
	}
	else{
		$myAgentName = "Zaitoon Agent";
	}
	
	//Outlet Info.
	$center = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_outlets` WHERE `code`='{$order['outlet']}'"));
	$outletData = $center['name'].", ".$center['line1'].", ".$center['line2'].", ".$center['city'];
	$outletContact = $center['contact'];

	$status = true;
	$error = '';
	$cart = json_decode($order['cart']);
	$list = array(
		'orderID' => $order['orderID'],
		'isTakeaway' => $order['isTakeaway'] == 1? true: false,
		'status' => $order['status'],
		'comment' => $order['comments'],
		'address' => $order['deliveryAddress'],
		'cart' => $cart,
		'date' => $order['date'],
		'timePlace' => $order['timePlace'],
		'timeConfirm' => $order['timeConfirm'],
		'timeDispatch' => $order['timeDispatch'],
		'timeDeliver' => $order['timeDeliver'],
		'agentName' => $myAgentName,
		'agentMobile' => $order['agentMobile'],
		'outletAddress' => $outletData,
		'outletContact' => $outletContact
		);
}

$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);
?>
