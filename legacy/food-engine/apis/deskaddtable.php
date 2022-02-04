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
		"error" => "Expired Token"
	);
	die(json_encode($output));
}



if(!isset($_POST['name'])){
	$output = array(
		"status" => false,
		"error" => "Table Name is missing"
	);
	die(json_encode($output));
}


if(!isset($_POST['section'])){
	$output = array(
		"status" => false,
		"error" => "Table Section is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['capacity'])){
	$output = array(
		"status" => false,
		"error" => "Capacity is missing"
	);
	die(json_encode($output));
}



$name = mysql_real_escape_string($_POST['name']);
$section = mysql_real_escape_string($_POST['section']);
$capacity = mysql_real_escape_string($_POST['capacity']);



//Check if the token is tampered
if($tokenid['outlet']){
	$branch = $tokenid['outlet'];
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

//Already Exists Case
$test = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_desk_tables` WHERE `name`='{$name}' AND branch='{$branch}'"));
if($test['name'] != ""){
	$output = array(
		"status" => false,
		"error" => "Table with same name already exists."
	);
	die(json_encode($output));
}

$query = "INSERT INTO `z_desk_tables`(`name`, `branch`, `section`, `capacity`) VALUES ('{$name}', '{$branch}', '{$section}', '{$capacity}')";
$main = mysql_query($query);

$output = array(
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>

