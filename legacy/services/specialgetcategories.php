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


//Check if the token is tampered
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$main_types = mysql_query("SELECT DISTINCT mainType FROM z_master_menu");

while($mainInfo = mysql_fetch_assoc($main_types)){

	$sub_types = mysql_query("SELECT DISTINCT subType FROM z_master_menu WHERE mainType = '{$mainInfo['mainType']}'");
	
	$sub_list = [];
	while($subInfo = mysql_fetch_assoc($sub_types)){
		
		$subInfoFancy = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_types` WHERE `short` = '{$subInfo['subType']}'"));
	
		$sub_list[] = array(
			"name" => $subInfoFancy['name'],
			"value" => $subInfo['subType']
		);
	}
	
	$list[] = array(
		"category" => $mainInfo['mainType'],
		"list" => $sub_list
	);
		
}
		
$output =array(
	"status" => true,
	"error"=> "",
	"list" => $list
);

echo json_encode($output);

?>
