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

$mobile = $_POST['mobile'];
$password = $_POST['password'];

if(!isset($_POST['mobile'])){
	$output = array(
		"status" => false,
		"error" => "Username Missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['password'])){
	$output = array(
		"status" => false,
		"error" => "Password Missing"
	);
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');
$date = date("Y-m-j");


$query = "SELECT * from z_roles WHERE `code`='{$mobile}' AND `password`='{$password}' AND `role`='ADMIN'";
$main = mysql_query($query);
$rows = mysql_fetch_assoc($main);

$status = false;
$error = '';


if(!empty($rows)){


	if(isset($_POST['token']) && $_POST['token'] != ''){
	
		$token = $_POST['token'];
		$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
		$tokenid = json_decode($decryptedtoken, true);
		
		//Check if the token is tampered
		if($tokenid['outlet']){
			if($tokenid['mobile'] != $mobile){
			
				$user_info = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$tokenid['mobile']}'"));
				$output = array(
					"status" => false,
					"error" => "You are trying to login as a different User. Only the logged in User ".($user_info['name'] != ''? "(".$user_info['name'].")" : "")." can reset the Screen Lock."
				);
				die(json_encode($output));		
			}
		}
	}
	
	$status = true;
}
else{
	$status = false;
	$error = 'Incorrect Username/Password';
}

$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);

?>
