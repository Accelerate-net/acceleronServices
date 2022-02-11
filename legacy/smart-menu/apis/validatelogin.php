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

$mobile = mysql_real_escape_string($_POST['mobile']);
$otp = mysql_real_escape_string($_POST['otp']);

if($mobile == "" || $otp == ""){
    $output = array(
    	"response" => "",
    	"status" => false,
    	"error" => "Missing required parameters"
    );
    
    die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');

$query = "SELECT * from smart_registered_users WHERE mobile='{$mobile}' AND otp_code='{$otp}'";
$main = mysql_query($query);
$userData = mysql_fetch_assoc($main);

if($userData['mobile'] != $mobile){
    $output = array(
    	"response" => "",
    	"status" => false,
    	"error" => "Incorrect OTP"
    );
    
    die(json_encode($output)); 
}

$date = date("Y-m-j");
$loginjson = array(
	"mobile" => $userData['mobile'],
	"date" => $date
);

$textToEncrypt = json_encode($loginjson);

//To encrypt
$encryptedMessage = openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);
$token = $encryptedMessage;

//To Decrypt
$decryptedMessage = openssl_decrypt($encryptedMessage, $encryptionMethod, $secretHash);


$response = array(
	"name" => $userData['name'],
	"mobile" => $userData['mobile'],
	"isVerified" => $userData['is_verified'] == 1 ? true : false,
	"isBlocked" => $userData['is_blocked'] == 1 ? true : false,
	"lastLogin" => $userData['last_successful_login'],
	"memberSince" => $userData['date_created'],
	"memberHomeBranch" => $userData['home_branch'],
	"email" => $userData['email'],
	"token" => $token
);

$current_stamp = time();
$current_update = date("Y-m-d H:i:s", $current_stamp);
mysql_query("UPDATE `smart_registered_users` SET `otp_code`='1000', `last_successful_login`='{$current_update}', `otp_attempts` = 0, `is_verified` = 1  WHERE `mobile` = '{$mobile}'");

$output = array(
	"response" => $response,
	"status" => true,
	"error" => ""
);

echo json_encode($output);
?>
