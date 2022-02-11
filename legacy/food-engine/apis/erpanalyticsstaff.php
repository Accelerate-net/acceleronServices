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

$sample = '{ "salarySum": 212831, "totalEmployees": 342, "attendance": { "absent": 30, "present": 305, "unknown": 15, "halfday": 2 }, "roleWiseEmployees": [{ "name": "Manager", "count": "2" }, { "name": "Stewards", "count": "11" }, { "name": "Accounting", "count": "1" }, { "name": "House Keeping", "count": "2" }, { "name": "Security", "count": "1" }], "outletWiseEmployees": [{ "name": "IIT Madras", "count": "42" }, { "name": "Adyar", "count": "31" }, { "name": "Velachery", "count": "28" }, { "name": "Royapettah", "count": "27" }, { "name": "Nungambakkam", "count": "19" }, { "name": "Anna Nagar", "count": "26" }] }';


if(isset($_POST['filterBranch'])){ //filter applied sample case
$sample = '{ "salarySum": 1000, "totalEmployees": 12, "attendance": { "absent": 0, "present": 305, "unknown": 15, "halfday": 2 }, "roleWiseEmployees": [{ "name": "Manager", "count": "2" }, { "name": "Stewards", "count": "11" }, { "name": "Accounting", "count": "1" }, { "name": "House Keeping", "count": "2" }, { "name": "Security", "count": "1" }], "outletWiseEmployees": [{ "name": "IIT Madras", "count": "42" }, { "name": "Adyar", "count": "31" }, { "name": "Velachery", "count": "28" }, { "name": "Royapettah", "count": "27" }, { "name": "Nungambakkam", "count": "19" }, { "name": "Anna Nagar", "count": "26" }] }';
}

$list = json_decode($sample);

$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);

/*
{
	"salarySum": 212831,
	"totalEmployees": 342,
	"attendance": {
		"absent": 30,
		"present": 305,
		"unknown": 15,
		"halfday": 2
	},
	"roleWiseEmployees": [{
		"name": "Manager",
		"count": "2"
	}, {
		"name": "Stewards",
		"count": "11"
	}, {
		"name": "Accounting",
		"count": "1"
	}, {
		"name": "House Keeping",
		"count": "2"
	}, {
		"name": "Security",
		"count": "1"
	}],
	"outletWiseEmployees": [{
		"name": "IIT Madras",
		"count": "42"
	}, {
		"name": "Adyar",
		"count": "31"
	}, {
		"name": "Velachery",
		"count": "28"
	}, {
		"name": "Royapettah",
		"count": "27"
	}, {
		"name": "Nungambakkam",
		"count": "19"
	}, {
		"name": "Anna Nagar",
		"count": "26"
	}]
}
*/

?>

