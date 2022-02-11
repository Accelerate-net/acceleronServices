<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);
$_POST = json_decode(file_get_contents('php://input'), true);

/*******************
  0. DOCUMENTATION
********************/
/*
	Version: 1.0
	Admin API: NO
	Brief: To fetch employee' list
*/


/**********************************
  1.1 AUTHENTICATION STANDARD PART
***********************************/

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';


//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
			"status" => false,
			"error" => "Access Token Missing",
			"errorCode" => 103,
			"response" => ""
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
			"error" => "Login Expired",
			"errorCode" => 401,
			"response" => ""
	);
	die(json_encode($output));
}

/**********************************
  1.2 AUTHENTICATION CUSTOM PART
***********************************/
//Check if the token is tampered
if($tokenid['outlet']){
	$branch = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered",
		"errorCode" => 401
	);
	die(json_encode($output));
}
	

//REQUIRED PARAMETERS

date_default_timezone_set('Asia/Kolkata');
$date = date('m/d/Y h:i:s a', time());

//Validations

if(!isset($_POST['key'])){
	$output = array(
			"status" => false,
			"error" => "Search Key is Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}


/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect.php';

$key = $_POST['key'];
$searchKeys = explode("-", $key);

$limiter = "";

if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}

$name_query = mysql_query("SELECT * FROM `z_users` WHERE `name` LIKE '{$key}%' ORDER BY `name`".$limiter);
$name_query_extended = mysql_query("SELECT * FROM `z_users` WHERE `name` LIKE '%{$key}%' AND `name` NOT LIKE '{$key}%' ORDER BY `name`".$limiter);

$email_query = mysql_query("SELECT * FROM `z_users` WHERE `email` LIKE '%{$key}%' ORDER BY `name`".$limiter);
$mobile_query = mysql_query("SELECT * FROM `z_users` WHERE `mobile` LIKE '%{$key}%' ORDER BY `name`".$limiter);

$exists = false;

while($rows = mysql_fetch_assoc($name_query)){

		$exists = true;

		$response[] = array(
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
			"guestType"=> "App User",
			"photoURL"=> ""
		);
}

while($rows = mysql_fetch_assoc($name_query_extended)){ //Extended

		$exists = true;

		$response[] = array(
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
			"guestType"=> "App User",
			"photoURL"=> ""
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}

while($rows = mysql_fetch_assoc($email_query)){

		$exists = true;

		$response[] = array(
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
			"guestType"=> "App User",
			"photoURL"=> ""
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}



while($rows = mysql_fetch_assoc($mobile_query)){

		$exists = true;

		$response[] = array(
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
			"guestType"=> "App User",
			"photoURL"=> ""
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}


if(!$exists){
  $output = array(
			"status" => false,
			"error" => "No data found with key as ".$key,
			"errorCode" => "",
			"posts" => ""
		);
		die(json_encode($output));
	}

?>