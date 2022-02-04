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

if(!isset($_POST['address'])){
	$output = array(
		"status" => false,
		"error" => "Address Object is missing"
	);
	die(json_encode($output));
}


$address = $_POST['address'];

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



$i = 0;
$id = 0;

$status = false;
$error = 'Error while adding a new address';

	$query = "SELECT * from z_users WHERE mobile='{$userID}'";
	$main = mysql_query($query);
	$rows = mysql_fetch_assoc($main);

	$useraddress = json_decode($rows['savedAddresses']);
	$i = sizeof($useraddress)-1;
	$id = ($useraddress[$i]->id) + 1;
	
	//Maximum of 4 addresses
	if(sizeof($useraddress) >= 4){
		$output = array(
			"status" => false,
			"error" => "No more than 4 addresses can be saved"
		);
		die(json_encode($output));	
	}

	$item = sizeof($useraddress);
	$useraddress[$item] = array(
		"id" => $id,
		"isDefault" => $address['isDefault'],
		"name" => $address['name'],
		"flatNo" => $address['flatNo'],
		"flatName" => $address['flatName'],
		"landmark" => $address['landmark'],
		"area" => $address['area'],
		"contact" => $address['contact']
	);

	//Error Reporting
	if($address['name'] == null || $address['flatNo'] == null || $address['flatName'] == null || $address['landmark'] == null || $address['area'] == null || $address['contact'] == null){
		$output = array(
			"status" => false,
			"error" => "Fields missing."
		);
		die(json_encode($output));
	}

	$newadd = json_encode($useraddress);
	$query = "UPDATE z_users SET `savedAddresses`='{$newadd}' WHERE mobile='{$userID}'";
	$main = mysql_query($query);

	$status = true;
	$error = "";



$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);

?>
