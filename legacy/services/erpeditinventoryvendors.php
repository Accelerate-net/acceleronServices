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
	$branch = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


if(!isset($_POST['id'])){
 $output = array(
   "status" => false,
   "error" => "ID is missing"
 );
 die(json_encode($output));
}

$id = $_POST['id'];


$detailsObj = $_POST['details'];
$branchesList = '[]';
$inventoriesList = '[]';

if(isset($_POST['branchesList'])){
 $branchesList = json_encode($_POST['branchesList']);
}

if(isset($_POST['inventoriesList'])){
 $inventoriesList = json_encode($_POST['inventoriesList']);
}

$status = true;
$error = "";

$query = "UPDATE `erp_inventoryVendors` SET `name`='{$detailsObj['name']}',`address`='{$detailsObj['address']}',`contact`='{$detailsObj['contact']}',`inventoriesProvided`='{$inventoriesList}',`providingBranches`='{$branchesList}',`modeOfPayment`='{$detailsObj['modeOfPayment']}',`paymentReference`='{$detailsObj['paymentReference']}' WHERE `code`='{$id}'";

$main = mysql_query($query);

$output = array(
	"response" => $query,
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>