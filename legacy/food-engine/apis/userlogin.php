<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

//SMS Credentials
define('SMS_INCLUDE_CHECK', true);
require 'smsblackbox.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$mobile = mysql_real_escape_string($_POST['mobile']);


$query = "SELECT isBlocked, otpStamp, otp from z_users WHERE mobile='{$mobile}'";
$main = mysql_query($query);
$rows = mysql_fetch_assoc($main);

$sent_otp = $rows['otp'];

//Blocked User
if($rows['isBlocked'] == 1){
$output = array(
	"response" => "",
	"status" => false,
	"error" => $mobile." mobile number is blocked. Please contact care@zaitoon.online"
);

die(json_encode($output));
}

//To prevent repeated resending of OTP
$last_otp = strtotime($rows['otpStamp']);
$current_stamp = time();
$current_update = date("Y-m-d H:i:s", $current_stamp);
	
if(($current_stamp - $last_otp) < 60) {     //60 seconds
	$output = array(
		"response" => "",
		"timeleft" => 60-($current_stamp-$last_otp),
		"status" => true,
		"error" => "OTP is already sent to ".$mobile."."
	);
	
	die(json_encode($output));
}




$status = false;
$error = '';

//Resending OTP Case
if($sent_otp != 1000){
	$otp = $sent_otp;
}
else{	
	$otp = rand(1001,9999);
}

if(!empty($rows)){
	$query1 = "UPDATE `z_users` SET `otp`='{$otp}', `otpStamp` = '{$current_update}' WHERE mobile='{$mobile}'";
	$main1 = mysql_query($query1);
	$response = array(
		"userid" => $mobile,
		"isOTPSent" => true
	);
	$status = true;
	
	//Sending OTP						
	$message = $otp." is your OTP for sign in with www.zaitoon.online";
	vegaSendSMS($mobile, $message);		
}
else{
	$response = array(
		"userid" => $mobile,
		"isOTPSent" => false
	);
	$status = false;
	$error = 'No user exists';
}

$output = array(
	"response" => $response,
	"status" => $status,
	"error" => $error
);

echo json_encode($output);

?>
