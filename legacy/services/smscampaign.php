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

if(!isset($_POST['content'])){
	$output = array(
		"status" => false,
		"error" => "Content can not be empty"
	);
	die(json_encode($output));
}

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
	$outlet = $tokenid['outlet'];
	$admin_created = $tokenid['mobile'];	
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


date_default_timezone_set('Asia/Calcutta');
$date = date("g:i a j F, Y");
$dateStamp = date("Ymd");

$post_msg = $_POST['content'];
$post_title = $_POST['name'];

		
//1. Check if SMS content is valid

		$content_formatted = $post_msg." www.zaitoon.online";

		// Account details
		$username = 'support@accelerate.net.in';
		$hash = 'c91c1f65213965173fb2353ea7863061d5df526e5ecf94badb88b8f6545fa45e';

		$numbers = array('91'.$admin_created);
		$sender = urlencode('ZAITON');						
		$message = rawurlencode($content_formatted);					 
		$numbers = implode(',', $numbers);
	 
		// Prepare data for POST request
		$data = array('username' => $username, 'hash' => $hash, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
	 
		// Send the POST request with cURL
		$ch = curl_init('http://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$SMSresponse = curl_exec($ch);
		curl_close($ch);
		$my_rep = json_decode($SMSresponse, true);
		
		mysql_query("UPDATE  `sms_limt` SET  `used` =  `used`+1 WHERE 1");
		
		if($my_rep['status'] == 'failure'){
			$output = array(
				"status" => false,
				"error" => "SMS Content is Invalid. Note: Maximum of 140 Characters allowed."
			);
			die(json_encode($output));				
		}
		
		
		

//2. Check the List of Target Classes		
if(isset($_POST['target']) && $_POST['target']!=""){	
	$classwise = "";
	$total_target = 0;
	
	$target_list = explode(", ", $_POST['target']);
	$unique_list = array_unique($target_list); 
		
	foreach ($unique_list as $value) {
	
	    $target_count_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`mobile`) as total FROM `z_users` WHERE `memberType`='{$value}' AND `isBlocked` = 0"));
	
	    $target_class_count = 0;
	    if($target_count_check['total'] != "" && $target_count_check['total'] != 0){
	    	
	    	    $target_class_count = $target_count_check['total'];
	    	
	    	    $classwise[] = array(
			"class" => $value,
			"count" => $target_class_count
		    );
	    }
	        
	    $total_target = $total_target + $target_class_count;		
	}

	/* No user found in the target */
	if($total_target == 0){
		$output = array(
			"status" => false,
			"error" => "No user found in the set Target. Keep Target field blank to send this SMS to all users."
		);
		die(json_encode($output));
	}
}
else{
	$classwise = "";
	$total_target = 0;
	$target_count_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`mobile`) as total FROM `z_users` WHERE `isBlocked` = 0"));
	if($target_count_check['total'] != "" && $target_count_check['total'] != 0){
		$total_target = $target_count_check['total'];
	}
	
	$classwise[] = array(
		"class" => "ALL USERS",
		"count" => $total_target
	);

}
			


//3. SMS Balance Check

	$sms_bal_check = mysql_fetch_assoc(mysql_query("SELECT * FROM `sms_limt` WHERE 1"));
	$balance = $sms_bal_check['purchased'] - $sms_bal_check['used'];
	if($balance <= $total_target){
		$output = array(
			"status" => false,
			"error" => "Insufficient SMS Credits. You needed ".$total_target.", but have only ".$balance." credits left."
		);
		die(json_encode($output));
	}
	
	
	
	//FINAL RESPONSE	
	$output = array(
		"status" => true,
		"error" => "",
		"checkText" => "Sample SMS has been sent to your number ".$admin_created,
		"response" => $classwise,
		"totalExpectedCost" =>  $total_target
	);
	
	die(json_encode($output));	

?>