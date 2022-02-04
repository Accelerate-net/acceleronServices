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

$status = '';
$error = '';


if(!empty($rows)){
	$responsejson = array(
		"outlet" => $rows['branch'],
		"date" => $date,
		"mobile" => $mobile
	);
	$textToEncrypt = json_encode($responsejson);
	$response = openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);	
	
	$info = mysql_fetch_assoc(mysql_query("SELECT name, city from z_outlets WHERE code = '{$rows['branch']}'"));
	$branch = $info['name'];
	$branchCode = $rows['branch'];

	$status = true;
}
else{
	$response = "";
	$branch = "";
	$status = false;
	$error = 'Incorrect Username/Password';
}

$output = array(
	"branch" => $branch,
	"branchCode" => $branchCode,
	"user" => $rows['name'],
	"mobile" => $mobile,	
	"response" => $response,
	"status" => $status,
	"error" => $error
);

echo json_encode($output);

?>
