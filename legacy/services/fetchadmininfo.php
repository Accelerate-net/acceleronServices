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
$error = "No admin account found.";

$query = "SELECT * FROM z_roles WHERE code='{$admin_mobile}'";
$main = mysql_query($query);

while($rows = mysql_fetch_assoc($main))
{
	$branch_info = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_outlets` WHERE `code`='{$rows['branch']}'"));
	$output=array(
		"name" => $rows['name'],
		"username" => $rows['code'],
		"outlet"=> $branch_info['name']
	);
	$status = true;
	$error = "";
}
$error = $query;

$out = array(
	"status" => $status,
	"error" => $error,
	"response" => $output
);

echo json_encode($out);

?>
