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

/*
//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Security Token is missing. Please login again to prove your identity."
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
		"errorCode" => 404,
		"error" => "Security Token is too old. Please login again to prove your identity."
	);
	die(json_encode($output));
}

//Check if the token is tampered
if($tokenid['outlet']){
	$outlet= $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Something suspicious noticed. Please login again to prove your identity."
	);
	die(json_encode($output));
}

*/


if(!isset($_POST['email']) || $_POST['email'] == ''){
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Email address not set"
	);
	die(json_encode($output));
}

if(!isset($_POST['content']) || $_POST['content'] == ''){
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Empty content"
	);
	die(json_encode($output));
}


require '../clients/zamrud/mail.php';

	$imageflag = false;

	//Upload Photo
	if(isset($_POST['image']) && $_POST['image'] != "" && $_POST['imageName'] != "")
	{
		$data = $_POST['image'];
	
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);
		
		file_put_contents("../clients/zamrud/report_trend_images_repo/".$_POST['imageName'].".png", $data);
	}
	
	
	
$name = $_POST['name'];
$email = $_POST['email'];
$sub = $_POST['title'];
$body = $_POST['content'];

mailer($email, $name, $sub , $body);

$output = array(
	"status" => true,
	"error" => ""
);

echo json_encode($output);

?>
