<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require '../connect.php';

//SMS Credentials
define('SMS_INCLUDE_CHECK', true);
require '../smsblackbox.php';

$_POST = json_decode(file_get_contents('php://input'), true);
date_default_timezone_set('Asia/Calcutta');

$mobile = mysql_real_escape_string($_POST['mobile']);
$name = mysql_real_escape_string($_POST['name']);
$homebranch = mysql_real_escape_string($_POST['branch']);

if($mobile == ""){
    $output = array(
    	"response" => "",
    	"status" => false,
    	"error" => "Invalid mobile number"
    );
    
    die(json_encode($output));
}

$query = "SELECT * from smart_registered_users WHERE mobile='{$mobile}'";
$main = mysql_query($query);
$userData = mysql_fetch_assoc($main);

$sent_otp = 1000;
$isNewUser = true;

if($userData['mobile'] == $mobile){
    //Login
    $isNewUser = false;
    
    //Blocked User
    if($userData['is_blocked'] == 1){
        $output = array(
        	"response" => "",
        	"status" => false,
        	"error" => $mobile." mobile number is blocked. Please contact hello@zaitoon.restaurant"
        );
        
        die(json_encode($output));
    }
    
    //To prevent repeated resending of OTP
    $total_otp_attempts = $userData['otp_attempts'];
    if($total_otp_attempts > 7){
        $output = array(
    		"response" => "",
    		"status" => false,
    		"error" => "Too many attempts, reach out to hello@zaitoon.restaurant"
    	); 
    	die(json_encode($output));
    }
    
    $last_otp = strtotime($userData['otp_sent_timestamp']);
    $current_stamp = time();
    $current_update = date("Y-m-d H:i:s", $current_stamp);
    	
    if(($current_stamp - $last_otp) < 60) { //60 seconds
    	$output = array(
    		"response" => $response,
    		"timeleft" => 60-($current_stamp-$last_otp),
    		"status" => true,
    		"error" => "OTP is already sent to ".$mobile
    	);
    	
    	die(json_encode($output));
    }
    
    $sent_otp = $userData['otp_code'];
}
else{
    //Sign Up
    $isNewUser = true;
    
    if($mobile == "" || $name == "" || $homebranch == ""){
        $output = array(
        	"response" => "",
        	"status" => false,
        	"error" => "Required values missing"
        );
        
        die(json_encode($output));
    }

    mysql_query("INSERT INTO `smart_registered_users`(`mobile`, `name`, `home_branch`, `is_verified`) VALUES ('{$mobile}', '{$name}', '{$homebranch}', 0)");
}


$status = false;
$error = '';

//Resending OTP Case
if($sent_otp > 1000){
	$otp = $sent_otp;
}
else{	
	$otp = rand(1001,9999);
}

$otp = 2000; //TEST OTP

mysql_query("UPDATE `smart_registered_users` SET `otp_code`='{$otp}', `otp_sent_timestamp` = '{$current_update}', `otp_attempts` = `otp_attempts` + 1 WHERE mobile='{$mobile}'");
$response = array(
	"userid" => $mobile,
	"isOTPSent" => true,
	"type" => $isNewUser ? "register" : "login"
);
$status = true;

//Sending OTP						
$message = $otp." is your OTP for sign in with www.foodbaxa.com";
//vegaSendSMS($mobile, $message);	

$output = array(
	"response" => $response,
	"status" => $status,
	"error" => $error
);

echo json_encode($output);
?>
