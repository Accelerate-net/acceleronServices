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
	
if(isset($_POST['target']) && $_POST['target'] != ""){

	$total_target = 0;
	
	$target_list = explode(", ", $_POST['target']);
	$unique_list = array_unique($target_list); 
	
	$member_type_all = implode (", ", $unique_list);;
	
	mysql_query("INSERT INTO `z_smsmessenger`(`dateStamp`, `date`, `target`, `title`, `content`, `user`) VALUES ('{$dateStamp}', '{$date}', '{$member_type_all}',  '{$post_title}', '{$post_msg}', '{$admin_created}')");
	$id_check = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `z_smsmessenger` WHERE `user` ORDER BY `id` DESC"));
	$my_id = $id_check['id'];
		
	foreach ($unique_list as $member_type) {
	
	    	$target_count_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`mobile`) as total FROM `z_users` WHERE `memberType`='{$member_type}' AND `isBlocked` = 0"));
	
	    	$total_target = $total_target + $target_count_check['total'];
  				
		$user_fetch = mysql_query("SELECT `mobile` FROM `z_users` WHERE `memberType`='{$member_type}' AND `isBlocked` = 0");
		while($user = mysql_fetch_assoc($user_fetch)){
			mysql_query("INSERT INTO `z_smsdeliveryreports`(`id`, `user`, `status`) VALUES ('{$my_id}', '{$user['mobile']}', 0)");
		}	    		
	}

	mysql_query("UPDATE `z_smsmessenger` SET `count`='{$total_target}' WHERE `id`='{$my_id}'");

}
else{
	$target_count_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`mobile`) as total FROM `z_users` WHERE `isBlocked` = 0"));
	mysql_query("INSERT INTO `z_smsmessenger`(`dateStamp`, `date`, `target`, `title`, `content`, `user`, `count`) VALUES ('{$dateStamp}', '{$date}', 'ALL',  '{$post_title}', '{$post_msg}', '{$admin_created}', '{$target_count_check['total']}')");
	
	$id_check = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `z_smsmessenger` WHERE `user` ORDER BY `id` DESC"));
	$my_id = $id_check['id'];
	
	$user_fetch = mysql_query("SELECT `mobile` FROM `z_users` WHERE `isBlocked` = 0");
	while($user = mysql_fetch_assoc($user_fetch)){
		mysql_query("INSERT INTO `z_smsdeliveryreports`(`id`, `user`, `status`) VALUES ('{$my_id}', '{$user['mobile']}', 0)");
	}
}
			
	//FINAL RESPONSE
	
	$output = array(
		"status" => true,
		"error" => ""
	);
	
	die(json_encode($output));	
?>