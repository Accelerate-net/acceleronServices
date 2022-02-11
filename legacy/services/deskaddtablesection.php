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
		"error" => "Section Name is missing"
	);
	die(json_encode($output));
}


$code = mysql_real_escape_string($_POST['name']);



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
$test = mysql_fetch_assoc(mysql_query("SELECT * FROM z_desk_table_sections WHERE section='{$code}' AND branch='{$branch}'"));
if($test['section'] != ""){
	$output = array(
		"status" => false,
		"error" => "Section with same name already exists."
	);
	die(json_encode($output));
}

$query = "INSERT INTO z_desk_table_sections (`section`, `branch`) VALUES ('{$code}','{$branch}')";
$main = mysql_query($query);

$output = array(
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>

