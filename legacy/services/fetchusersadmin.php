<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	//$output = array(
	//	"status" => false,
	//	"error" => "Access Token is missing"
	//);
	//die(json_encode($output));
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
	//die(json_encode($output));
}


$mobile = $_POST['mobile'];

$status = false;
$error = "No user found.";

$query = "SELECT * FROM z_users WHERE mobile='{$mobile}'";
$main = mysql_query($query);

while($rows = mysql_fetch_assoc($main))
{
	$output=array(
		"name" => $rows['name'],
		  "mobile" => $rows['mobile'],
		  "isVerified" => $rows['isVerified'] == "1" ? true:false,
		  "isBlocked" => $rows['isBlocked'] == "1" ? true:false,
		  "lastLogin" => $rows['lastLogin'],
		  "memberSince" => $rows['memberSince'],
		  "isSubmittedFeedback"=> $rows['isFeedback'] == "1" ? true:false,
		"memberType"=> $rows['memberType'],
		"savedAddresses"=> json_decode($rows['savedAddresses']),
		"email"=> $rows['email'],
		"password"=> $rows['password']
	);
	$status = true;
	$error = "";
}


$out = array(
	"status" => $status,
	"error" => $error,
	"response" => $output
);

echo json_encode($out);

?>
