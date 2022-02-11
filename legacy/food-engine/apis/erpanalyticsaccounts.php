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

$sample = '{ "grandSalesSum": 452944, "grandPurchaseSum": 212831, "grandPaymentsSum": 214873, "grandRevenueSum": 12311, "grandSalarySum": 72840, "incomeList": [{ "name": "Sales (Dine In)", "amount": 1231444.13 }, { "name": "Sales (Online)", "amount": 24252 }, { "name": "Takeaways", "amount": 24253 }, { "name": "Misc", "amount": 1324 }], "incomeTotal": 132022, "expenseList": [{ "name": "Sales (Dine In)", "amount": 1231444.13 }, { "name": "Sales (Online)", "amount": 24252 }, { "name": "Takeaways", "amount": 24253 }, { "name": "Misc", "amount": 1324 }], "expenseTotal": 132022 }';


if(isset($_POST['filterBranch'])){ //filter applied sample case
$sample = '{ "grandSalesSum": 1214, "grandPurchaseSum": 2429, "grandPaymentsSum": 214873, "grandRevenueSum": 12311, "grandSalarySum": 72840, "incomeList": [{ "name": "Sales (Dine In)", "amount": 1231444.13 }, { "name": "Sales (Online)", "amount": 24252 }, { "name": "Takeaways", "amount": 24253 }, { "name": "Misc", "amount": 1324 }], "incomeTotal": 132022, "expenseList": [{ "name": "Sales (Dine In)", "amount": 1231444.13 }, { "name": "Sales (Online)", "amount": 24252 }, { "name": "Takeaways", "amount": 24253 }, { "name": "Misc", "amount": 1324 }], "expenseTotal": 132022 }';
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
	"grandSalesSum": 452944,
	"grandPurchaseSum": 212831,
	"grandPaymentsSum": 214873,
	"grandRevenueSum": 12311,
	"grandSalarySum": 72840,
	"incomeList": [{
		"name": "Sales (Dine In)",
		"amount": 1231444.13
	}, {
		"name": "Sales (Online)",
		"amount": 24252
	}, {
		"name": "Takeaways",
		"amount": 24253
	}, {
		"name": "Misc",
		"amount": 1324
	}],
	"incomeTotal": 132022,
	"expenseList": [{
		"name": "Sales (Dine In)",
		"amount": 1231444.13
	}, {
		"name": "Sales (Online)",
		"amount": 24252
	}, {
		"name": "Takeaways",
		"amount": 24253
	}, {
		"name": "Misc",
		"amount": 1324
	}],
	"expenseTotal": 132022
}
*/

?>

