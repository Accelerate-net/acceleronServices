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

$mobile = mysql_real_escape_string($_POST['mobile']);
$otp = mysql_real_escape_string($_POST['otp']);

date_default_timezone_set('Asia/Calcutta');
$date = date("Y-m-j");

$udate = date("j F, Y");
$utime = date("g:i a");
$mydate = $utime.' on '.$udate;

$query = "SELECT * from z_users WHERE mobile='{$mobile}' AND otp='{$otp}'";
$main = mysql_query($query);
$rows = mysql_fetch_assoc($main);
$status = '';
$error = '';

$loginjson = array(
	"mobile" => $rows['mobile'],
	"date" => $date
);

$status = false;

//$token = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,json_encode($loginjson), MCRYPT_MODE_CBC, $iv);
//hash_hmac ( "sha256" , json_encode($loginjson) , 'zaitoonkey' );

$textToEncrypt = json_encode($loginjson);

//To encrypt
$encryptedMessage = openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);
$token = $encryptedMessage;

//To Decrypt
$decryptedMessage = openssl_decrypt($encryptedMessage, $encryptionMethod, $secretHash);


if(!empty($rows)){
	$response = array(
		"name" => $rows['name'],
		"mobile" => $rows['mobile'],
		"isVerified" => $rows['isVerified'],
		"isBlocked" => $rows['isBlocked'],
		"isRewardEnabled" => $rows['isRewardEnabled'] == 1? true: false,
		"lastLogin" => $rows['lastLogin'],
		"memberSince" => $rows['memberSince'],
		"isSubmittedFeedback" => $rows['isFeedback'],
		"memberType" => $rows['memberType'],
		"savedAddresses" => json_decode($rows['savedAddresses']),
		"email" => $rows['email'],
		"token" => $token
	);
	$status = true;
	
	mysql_query("UPDATE `z_users` SET `otp`='1000', `lastLogin`='{$mydate}' WHERE `mobile` = '{$rows['mobile']}'");
}
else{
	$response = "";
	$status = false;
	$error = 'OTP Mismatch';
}

$output = array(
	"response" => $response,
	"status" => $status,
	"error" => $error
);

//$list = array('status' => $flag);
echo json_encode($output);

?>
