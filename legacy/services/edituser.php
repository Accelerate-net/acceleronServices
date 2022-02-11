<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
error_reporting(0);

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


$name = $_POST['name'];
$email = $_POST['email'];


$status = false;
$error = 'User does not exist';


	$query = "SELECT * from z_users WHERE mobile='{$tokenid['mobile']}'";
	$main = mysql_query($query);
	$rows = mysql_fetch_assoc($main);

	if($rows['mobile']){
	$status = true;
	$error = '';

	$query = "UPDATE z_users SET `name`='{$name}' , `email`='{$email}' WHERE mobile='{$rows['mobile']}'";
	$main = mysql_query($query);
	}



$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);
?>
