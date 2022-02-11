<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST['secret']) || $_POST['secret'] != 'NJAN_APPILAAA'){
	$output = array(
		"status" => false,
		"error" => "Error"
	);
	die(json_encode($output));
}

if(!isset($_POST['mobile']) || strlen($_POST['mobile']) != 10){
	$output = array(
		"status" => false,
		"error" => "Error"
	);
	die(json_encode($output));
}


$status = false;
$error = 'Something went wrong. Try again.';

$query = mysql_query("SELECT `name`, `email`, `mobile` FROM `z_users` WHERE `mobile`='{$_POST['mobile']}'");

if($res = mysql_fetch_assoc($query)){

	$status = true;
	$error = '';
	
	
	$response = array(
		"name" => $res['name'],
		"email" => $res['email'],
		"mobile" => $res['mobile']			
	);
}


$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $response			
);

echo json_encode($output);
?>
