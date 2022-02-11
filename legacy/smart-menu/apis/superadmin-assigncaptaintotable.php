<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require '../secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);
/*
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
$outlet = "";
// if($tokenid['outlet']){
// 	$outlet = $tokenid['outlet'];
// }
// else{
// 	$output = array(
// 		"status" => false,
// 		"error" => "Token is tampered"
// 	);
// 	die(json_encode($output));
// }

*/
date_default_timezone_set('Asia/Calcutta');

$outlet = "JPNAGAR";

$captainCode = $_POST['captainCode'];
$table = $_POST['table'];

$existCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_steward_table_mapping` WHERE `table_number`='{$table}' AND `branch`='{$outlet}'"));
if($existCheck[id] != null){
    //Update mapping
    mysql_query("UPDATE `smart_steward_table_mapping` SET `steward_code` = '{$captainCode}' WHERE `table_number`='{$table}' AND `branch`='{$outlet}'");
}
else{
    //Insert new
    mysql_query("INSERT INTO `smart_steward_table_mapping`(`steward_code`, `table_number`, `branch`) VALUES ('{$captainCode}', '{$table}', '{$outlet}')");
}

$finalOutput = array(
    "status" => true,
    "data" => ""
);

die(json_encode($finalOutput));

?>
