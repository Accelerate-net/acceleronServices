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



if(!isset($_POST['uid'])){
	$output = array(
		"status" => false,
		"error" => "Order ID is missing"
	);
	die(json_encode($output));
}
else{
	$uid = $_POST['uid'];
}




	$status = false;
	$error = "No passes found.";

	$query = "SELECT * FROM `zaitoon_passmasterlist` WHERE `orderID`='{$uid}' AND `userID`='{$mobile}'";
	$all = mysql_query($query);
	

	while($order = mysql_fetch_assoc($all))	
	{
	
		$status = true;
		$error = "";
		
		$info = mysql_fetch_assoc(mysql_query("SELECT `brief`, `name`, `isImg`, `url` FROM `z_deals` WHERE `id`='{$order['dealID']}'"));
		
		$cart = json_decode($order['cart']);
		$list[] = array(
			'uniquePass' => $order['uniquePassID'],
			'price' => $order['amount'],
			'variant' => $order['choice'],
			'status' => $order['status'],
			'name' => $info['name'],
			'brief' => $info['brief'],
			'isImageAvailable' => $info['isImg'],
			'url' => $info['url']			
		);
	}


$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);

?>
