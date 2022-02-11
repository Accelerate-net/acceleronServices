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


//SMS Credentials
define('SMS_INCLUDE_CHECK', true);
require 'smsblackbox.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$mobile = mysql_real_escape_string($_POST['mobile']);

if(!preg_match('/^\d{10}$/', $mobile)){
	$response = array(
		"userid" => $mobile,
		"isOTPSent" => false
	);
	$output = array(
	"response" => $response,
	"status" => $status,
	"error" => "Invalid Mobile Number"
	);

die(json_encode($output));

}

$query = "SELECT * from z_users WHERE mobile='{$mobile}'";
$main = mysql_query($query);
$rows = mysql_fetch_assoc($main);
$status = false;
$error = "";


if(empty($rows)){


	//To prevent sign up spam
	$otp_attempt_check = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_signupattempts` WHERE `mobile`='{$mobile}'"));
	
	if($otp_attempt_check['mobile'] != ""){ //Already attempted
	
		$otp = $otp_attempt_check['otp'];	
		
		//Too many requests using same number
		if($otp_attempt_check['attempts'] >= 8){
		
			$response = array(
				"userid" => $mobile,
				"isOTPSent" => false
			);
			
			$output = array(
				"response" => $response,
				"status" => false,
				"error" => "Too many attempts using ".$mobile.". Contact support@accelerate.net.in for help."
			);
			
			die(json_encode($output));		
		}	
		
		//To prevent repeated resending of OTP
		$last_otp = strtotime($otp_attempt_check['otpStamp']);
		$current_stamp = time();
		$current_update = date("Y-m-d H:i:s", $current_stamp);
			
		if(($current_stamp - $last_otp) < 60) {     //60 seconds
		
			$encryptedotpnew = openssl_encrypt($otp, $encryptionMethod, $secretHash);
			
			$response = array(
				"userid" => $mobile,
				"isOTPSent" => true,
				"otp" => $encryptedotpnew
			);
			
			$output = array(
				"timeleft" => 60-($current_stamp-$last_otp),
				"response" => $response,
				"status" => true,
				"error" => "OTP is already sent to ".$mobile."."
			);		
			
			die(json_encode($output));
		}	
		
		mysql_query("UPDATE `z_signupattempts` SET `otpStamp` = '{$current_update}', `attempts`=`attempts`+1 WHERE `mobile`='{$mobile}'");
	}
	else{ //First attempt case
		$otp = rand(1001,9999);
		$current_stamp = time();
		$current_update = date("Y-m-d H:i:s", $current_stamp);
		mysql_query("INSERT INTO `z_signupattempts`(`mobile`, `otp`, `otpStamp`) VALUES ('{$mobile}','{$otp}', '{$current_update}')");
	}
		
	
	//To encrypt
	$encryptedotp = openssl_encrypt($otp, $encryptionMethod, $secretHash);
	$decryptedotp = openssl_decrypt($encryptedotp, $encryptionMethod, $secretHash);

	$response = array(
		"userid" => $mobile,
		"isOTPSent" => true,
		"otp" => $encryptedotp
 	);
	$status = true;
	
	
	//Send SMS					
	$message = $otp." is your OTP for sign in with www.zaitoon.online";
	vegaSendSMS($mobile, $message);				
}
else{
	$response = array(
		"userid" => $mobile,
		"isOTPSent" => false
	);
	$status = false;
	$error = 'Mobile number already registered';
}

$output = array(
	"response" => $response,
	"status" => $status,
	"error" => $error
);

echo json_encode($output);

?>
