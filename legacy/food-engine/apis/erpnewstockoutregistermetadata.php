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
	$outlet= $tokenid['outlet'];
	$admin_mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$status = false;
$error = "No vendors found.";

$query2 = "SELECT DISTINCT `category` FROM `erp_inventoryList` WHERE 1";
$main2 = mysql_query($query2);

$inventoriesList = [];
while($rows2 = mysql_fetch_assoc($main2))
{

	$all = mysql_query("SELECT DISTINCT * FROM `erp_inventoryList` WHERE `category` = '{$rows2['category']}'");

	$catWise = [];
	while($list = mysql_fetch_assoc($all)){
		$catWise[] = array(
			"code" => $list['id'],
			"name" => $list['name'],
			"unit" => $list['unit']
		);
	}

	$inventoriesList[] = array(
		"category" => $rows2['category'],
		"items" => $catWise
	);
		
	$status = true;
	$error = "";
}


$error = "";

$out = array(
	"status" => $status,
	"error" => $error,
	"inventories" => $inventoriesList
);

echo json_encode($out);

?>
