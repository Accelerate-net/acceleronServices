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
		"error" => "Session Expired. Login Again."
	);
	die(json_encode($output));
}




/* FRAMING QUERY */
//Howmany results to output
$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']},5";
}

//Shortlist based on current status
$orderstatus = "";
if(isset($_POST['status'])){
	$orderstatus = " AND status = '{$_POST['status']}'";
}

$list = array();

//Check if the token is tampered
if($tokenid['mobile']){
	$mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

//Check 1, if it is a service for merchants
$merchantService = 0;

//User Specific Orders
if(!$merchantService){

	$status = true;

	$query = "SELECT * FROM zaitoon_passeslist WHERE userID = '{$mobile}'".$orderstatus." AND isVerified='1' ORDER BY orderID DESC".$limiter;
	$all = mysql_query($query);

	while($order = mysql_fetch_assoc($all))
	{
		$cart = json_decode($order['cart']);
		$list[] = array(
			'orderID' => $order['orderID'],
			'isTakeaway' => $order['isTakeaway'] == 1? true: false,
			'userID' => $order['userID'],
			'status' => $order['status'],
			'cart' => $cart,
			'deliveryAddress' => $order['deliveryAddress'],
			'date' => $order['date'],
			'timePlace' => $order['timePlace'],
			'timeConfirm' => $order['timeConfirm'],
			'timeDeliver' => $order['timeDeliver']
			);
	}
}
else{  //All the orders

	$status = true;

	$query = "SELECT * FROM zaitoon_passeslist WHERE isVerified='1'".$orderstatus." ORDER BY orderID".$limiter;
	$all = mysql_query($query);

	while($order = mysql_fetch_assoc($all))
	{
		$cart = json_decode($order['cart']);
		$address = json_decode($order['deliveryAddress']);
		$info = mysql_fetch_array(mysql_query("SELECT name FROM z_users WHERE mobile='{$order['userID']}'"));
		$list[] = array(
			'orderID' => $order['orderID'],
			'userID' => $order['userID'],
			'status' => $order['status'],
			'cart' => $cart,
			'deliveryAddress' => $order['deliveryAddress'],
			'date' => $order['date'],
			'timePlace' => $order['timePlace'],
			'timeConfirm' => $order['timeConfirm'],
			'timeDeliver' => $order['timeDeliver']
			);
	}
}


$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);

?>
