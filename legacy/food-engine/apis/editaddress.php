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

if(!isset($_POST['address']) || !isset($_POST['id'])){
	$output = array(
		"status" => false,
		"error" => "Address Object is missing"
	);
	die(json_encode($output));
}





$userID = $_POST['userID'];
$address = $_POST['address'];
$id = $_POST['id'];


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


$status = false;
$error = 'No such address exists';

$i = 0;

$userID = $tokenid['mobile'];

	$query = "SELECT * from z_users WHERE mobile='{$userID}'";
	$main = mysql_query($query);
	$rows = mysql_fetch_assoc($main);
	$useraddress = json_decode($rows['savedAddresses']);
	while ($i < sizeof($useraddress)) {
		if($id == $useraddress[$i]->id){
			$status = true;
			$error = "";
			break;
		}
		$i++;
	}

	$useraddress[$i] = array(
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



$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);

?>
