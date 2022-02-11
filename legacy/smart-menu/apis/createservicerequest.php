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

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['qrCodeReference'])){
	$output = array(
		"status" => false,
		"error" => "Table is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['serviceType'])){
	$output = array(
		"status" => false,
		"error" => "Service type is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['mobile'])){
	$output = array(
		"status" => false,
		"error" => "Mobile is missing"
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
		"error" => "User session timeout"
	);
	die(json_encode($output));
}

//Check if the token is tampered
if($tokenid['mobile'] == $_POST['mobile']){
	$userID = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Invalid token"
	);
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');

$branchCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_table_mapping` WHERE `qr_code` = '{$_POST['qrCodeReference']}' AND `is_active`=1"));

//Check if this user is mapped to the same table only
$status = false;
if($branchCheck['assigned_branch'] != ""){
	$mappedSteward = "";
    $mappedStewardCheck = mysql_fetch_assoc(mysql_query("SELECT `steward_code` FROM `smart_steward_table_mapping` WHERE `table_number` = '{$branchCheck['assigned_table']}' AND `branch` = '{$branchCheck['assigned_branch']}'"));
    if($mappedStewardCheck['steward_code'] != ""){
        $mappedSteward = $mappedStewardCheck['steward_code'];
    }
		    
    mysql_query("INSERT INTO `smart_service_requests`(`request`, `fk_qr_code`, `branch`, `table_code`, `fk_steward_code`) VALUES ('{$_POST['serviceType']}', '{$_POST['qrCodeReference']}', '{$branchCheck['assigned_branch']}', '{$branchCheck['assigned_table']}', $mappedSteward)");
    $status = true;
}

$output = array(
	"status" => $status,
	"error" => !$status ? "Failed to create a service request" : ""
);

echo json_encode($output);

?>
