<?php
	if(!defined('SMS_INCLUDE_CHECK')) die('Warning: You are not authorized to access this page.');
	
	//Database Connection
	define('INCLUDE_CHECK', true);
	require 'connect.php';

	//SMS Balance Check
	function vegaSendSMS($req_mobile, $req_msg){
		$sms_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `sms_limt` WHERE 1"));
		
		if($sms_info['used'] < $sms_info['purchased']){
		
			// Account details
			$username = 'support@accelerate.net.in';
			$hash = 'c91c1f65213965173fb2353ea7863061d5df526e5ecf94badb88b8f6545fa45e';
			
			// Message details - to customer
			$numbers = array('91'.$req_mobile);
			$sender = urlencode('ZAITON');
			
			$message = rawurlencode($req_msg);				
		 
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
	
			mysql_query("UPDATE  `sms_limt` SET  `used` =  `used`+1 WHERE 1");
		}
	}
	
?>




