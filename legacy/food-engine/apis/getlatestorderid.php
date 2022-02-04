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

$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken,true);

//Check if the token is tampered
if($tokenid['mobile']){
	$userID = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$status = false;
$error = 'Something went wrong';


/* HARD CODED for TESTING  
if($userID == '9043960876'){
$output = array(
	"status" => true,
	"error" => "",
	"response" => "10022293"
);
die(json_encode($output));
}
*/


date_default_timezone_set('Asia/Calcutta');
$now = date("dmY");


	$info1 = mysql_fetch_assoc(mysql_query("SELECT orderID, stamp, cart, date from zaitoon_orderlist WHERE status='2' AND userID='{$userID}' AND feedback='NA' ORDER BY orderID  DESC"));
	$info2 = mysql_fetch_assoc(mysql_query("SELECT orderID from zaitoon_orderlist WHERE status='2' AND userID='{$userID}' ORDER BY orderID DESC"));

	if($info1['orderID'] != "" && $info1['orderID'] == $info2['orderID']){
		if($info1['stamp'] != $now){ //Order Not Placed Today -- REMOVE THIS LATER
				$status = true;
				$error = '';
				$response = $info1['orderID'];
				$brief = json_decode($info1['cart']);
				$dateStamp = $info1['date'];
		}
	}


$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $response,
	"cart" => $brief,
	"date" => $dateStamp
);

echo json_encode($output);
?>
