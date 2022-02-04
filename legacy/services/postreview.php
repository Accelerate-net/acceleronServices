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

if(!isset($_POST['orderID'])){
	$output = array(
		"status" => false,
		"error" => "Order ID is missing"
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

$ratingObj = $_POST['review'];
$rating = $ratingObj['rating'];
$ratingComments = $ratingObj['comment'];
$ratingComments = trim(preg_replace('/\s+/', ' ', $ratingComments));

$ratingObj['comment'] = $ratingComments;
$review = json_encode($ratingObj);

$orderID = $_POST['orderID'];


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

date_default_timezone_set('Asia/Calcutta');
$timeStamp = date("g:i a")." on ".date("j F, Y");

	$query = "SELECT * from zaitoon_orderlist WHERE orderID='{$orderID}' AND userID='{$userID}'";
	$main = mysql_query($query);
	$rows = mysql_fetch_assoc($main);
	if(!empty($rows)){
		$status = true;
		$error = '';
		$query1 = "UPDATE zaitoon_orderlist SET `feedComments` = '{$ratingComments}', `stars`='{$rating}', `feedback`='{$review}', `timeFeedback`='{$timeStamp}' WHERE userID='{$userID}' AND orderID='{$orderID}'";
		mysql_query($query1);
	}

$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);
?>
