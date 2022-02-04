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
			"error" => "Login Expired",
			"errorCode" => 401,
			"response" => ""
	);
	die(json_encode($output));
}

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

$status = false;
$error = "There are no Stock added yet";

$query = "SELECT * FROM `erp_inventoryStock` WHERE `branch`='{$branch}'";
$main = mysql_query($query);

while($rows = mysql_fetch_assoc($main))
{

	$master = mysql_fetch_assoc(mysql_query("SELECT * FROM `erp_inventoryList` WHERE `id` = '{$rows['id']}'"));

	$vendors = json_decode($rows['vendorsList']);
	$n = 0;
	$vendorList = [];
	while($vendors[$n]){
		$info = mysql_fetch_assoc(mysql_query("SELECT `name`, `contact` FROM `erp_inventoryVendors` WHERE `code`='{$vendors[$n]}'"));
		$vendorList[]=array(
			"id" => $vendors[$n],
			"name" => $info['name'],
			"contact" => $info['contact']
		);
		$n++;
	}
	
	

	$output[]=array(
		"id" => $rows['id'],
		"name" => $master['name'],
		"category"=> $master['category'],
		"unit"=> $master['unit'],
		"minStockUnit"=> $rows['minStockUnit'],
		"currentStock"=> $rows['currentStock'],
		"vendorsList"=> $vendorList
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