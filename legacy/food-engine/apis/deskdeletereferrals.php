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
//if(!isset($_POST['token'])){
//	$output = array(
//		"status" => false,
//		"error" => "Access Token is missing"
//	);
//	die(json_encode($output));
//}

$token = 'sHtArttc2ht+tMf9baAeQ9ukHnXtlsHfexmCWx5sJOh4XbqkOA8ZVnA1hvXhUbpRJRp2jOE8qUrAIqTQu0fyPRFZ9Gi9eWDUh9CE6wnpDR8=';
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken, true);
//Expiry Validation
date_default_timezone_set('Asia/Calcutta');
$dateStamp = date_create($tokenid['date']);
$today = date_create(date("Y-m-j"));
$interval = date_diff($dateStamp, $today);
$interval = $interval->format('%a');

// if($interval > $tokenExpiryDays){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Expired Token"
// 	);
// 	die(json_encode($output));
// }



// if(!isset($_POST['branch'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Branch Missing"
// 	);
// 	die(json_encode($output));
// }

// if(!isset($_POST['name'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Name Missing"
// 	);
// 	die(json_encode($output));
// }

// if(!isset($_POST['category'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Category Missing"
// 	);
// 	die(json_encode($output));
// }

// if(!isset($_POST['unit'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Unit Missing"
// 	);
// 	die(json_encode($output));
// }

// if(!isset($_POST['minStockUnit'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Minimum Stock Unit Missing"
// 	);
// 	die(json_encode($output));
// }

// if(!isset($_POST['vendorsList'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Vendors List Missing"
// 	);
// 	die(json_encode($output));
// }

// if(!isset($_POST['currentStock'])){
// 	$output = array(
// 		"status" => false,
// 		"error" => "Current Stock Missing"
// 	);
// 	die(json_encode($output));
// }

//$branch = mysql_real_escape_string($_POST['branch']);
$id = '101';

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

$query = "DELETE FROM `z_desk_referrals` WHERE `id`='{$id}'";
$main = mysql_query($query);

$output = array(
	"response" => $query,
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>