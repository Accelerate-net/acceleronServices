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
	$isAuthenticated = 0;
}
else{

	$token = $_POST['token'];
	$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
	$tokenid = json_decode($decryptedtoken, true);
	
	//Expiry Validation
	date_default_timezone_set('Asia/Calcutta');
	$dateStamp = date_create($tokenid['date']);
	$today = date_create(date("Y-m-j"));
	$interval = date_diff($dateStamp, $today);
	$interval = $interval->format('%a');
	
	if($interval <= $tokenExpiryDays){	
		if($tokenid['mobile']){
			$userID = $tokenid['mobile'];
			$isAuthenticated = 1;
		}
		else{
			$isAuthenticated = 0;
		}		
	}
	else{
		$isAuthenticated = 0;
	}
}

date_default_timezone_set('Asia/Calcutta');
$today = date("g:i a").' '.date("d-m-Y");

$comment = mysql_real_escape_string($_POST['comment']);
$name = mysql_real_escape_string($_POST['name']);
$email = mysql_real_escape_string($_POST['email']);
$mobile = mysql_real_escape_string($_POST['mobile']);
$remark = mysql_real_escape_string($_POST['reference']);
$type = mysql_real_escape_string($_POST['type']);

if(!isset($_POST['comment']) || !isset($_POST['name']) || !isset($_POST['mobile'])){
	$output = array(
		"status" => false,
		"error" => "Parameter Missing"
	);
	
	die(json_encode($output));
}

if($isAuthenticated == 1){
	$mobile = $userID;
}

mysql_query("INSERT INTO `z_helpdesk`(`isAuthentic`, `mobile`, `name`, `email`, `comment`, `type`, `remarks`, `date`) VALUES ('{$isAuthenticated}', '{$mobile}', '{$name}', '{$email}', '{$comment}', '{$type}', '{$remark}', '{$today}')");

$status = true;
$error = '';

$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);
		
?>

