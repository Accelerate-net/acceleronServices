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

if(!isset($_POST['details'])){
	$output = array(
		"status" => false,
		"error" => "Reservation Object is missing"
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

date_default_timezone_set('Asia/Calcutta');
$date = date("j F, Y");
$time = date("g:i a");

$dateStamp = date("dmY");


$details = $_POST['details'];


//Maximum Allowed Count
if($details['count'] >= 30){
$output = array(
	"status" => false,
	"error" => "Maximum seats we can reserve at a time is 30. Contact us for Party Arrangements."
);

die(json_encode($output));
}

	$status = true;
	$error = "";
	
	$user_check = mysql_fetch_assoc(mysql_query("SELECT `name`, `email` FROM `z_users` WHERE `mobile`='{$userID}'"));
	
	$query = "INSERT INTO z_reservations (`channel`, `userName`, `userEmail`, `stamp`, `userID`, `outlet`, `date` , `time` , `count`) VALUES ('APP', '{$user_check['name']}', '{$user_check['email']}', '$dateStamp', '{$userID}','{$details['outlet']}','{$details['date']}','{$details['time']}','{$details['count']}')";
	$main = mysql_query($query);

$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);
?>
