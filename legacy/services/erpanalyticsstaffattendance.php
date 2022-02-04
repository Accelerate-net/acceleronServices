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


$status = true;
$error = "";

/* ADD MAIN LOGIC */

$month = $_POST['month'];

if($month == '201804'){
$sample = '[{ "date": "03-04-2018", "day": "Tuesday", "status": "2" }, { "date": "12-04-2018", "day": "Thursday", "status": "2" }, { "date": "14-04-2018", "day": "Saturday", "status": "2" }, { "date": "15-04-2018", "day": "Sunday", "status": "2" }, { "date": "23-04-2018", "day": "Monday", "status": "2" }, { "date": "02-04-2018", "day": "Monday", "status": "5" }]';
}
else if($month == '201805'){
$sample = '[{ "date": "01-05-2018", "day": "Tuesday", "status": "1" }, { "date": "02-05-2018", "day": "Thursday", "status": "1" }, { "date": "10-05-2018", "day": "Saturday", "status": "0" }, { "date": "11-05-2018", "day": "Sunday", "status": "0" }, { "date": "12-05-2018", "day": "Monday", "status": "1" }, { "date": "13-05-2018", "day": "Monday", "status": "5" }]';
}
else{
$sample = '[]';
}


$list = json_decode($sample);

$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);

/*
[{
	"date": "03-04-2018",
	"day": "Tuesday",
	"status": "2"
}, {
	"date": "12-04-2018",
	"day": "Thursday",
	"status": "2"
}, {
	"date": "14-04-2018",
	"day": "Saturday",
	"status": "2"
}, {
	"date": "15-04-2018",
	"day": "Sunday",
	"status": "2"
}, {
	"date": "23-04-2018",
	"day": "Monday",
	"status": "2"
}, {
	"date": "02-05-2018",
	"day": "Monday",
	"status": "5"
}]
*/

?>

