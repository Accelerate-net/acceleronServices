<?php

/*** YET TO ADD "NO RESULTS" SCENARIO ***/

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
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['id'])){
	$output = array(
		"status" => false,
		"error" => "Address ID missing"
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




$id = $_POST['id'];
$user = $tokenid['mobile'];

$info = mysql_fetch_array(mysql_query("SELECT savedAddresses FROM z_users WHERE mobile='{$user}'"));
$savedAddresses = json_decode($info['savedAddresses']);

/*
Iterate through the JSON and
delete the entry matching the ID
*/
$new = [];
$i = 0;
foreach($savedAddresses as $address){
   	if(!($id == $address->id)){
      	$new[]= $savedAddresses[$i];
   	}
	$i++;
}

$modified = json_encode($new);
mysql_query("UPDATE z_users SET savedAddresses = '{$modified}' WHERE mobile='{$user}'");
		
?>
