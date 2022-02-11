<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);

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
	$branch = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


$searchKey = $_POST['key'];

$status = false;
$error = "No user found.";
$resultKey == "";

$count = 0;


$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}


//Search for Mobile Number

	$query = "SELECT * FROM `z_desk_guests` WHERE `mobile`='{$searchKey}'".$limiter;
	$main = mysql_query($query);
	
	while($rows = mysql_fetch_assoc($main))
	{
		$output [] = array(
			"name" => $rows['name'],
			"mobile" => $rows['mobile'],	
			"city" => $rows['city'],
			"email"=> $rows['email'],
			"branch"=> $rows['registeredBranch'],		 
			"isBlocked" => $rows['isBlocked'] == "1" ? true:false,
			"firstVisit" => $rows['firstVisit'],
			"visitCount" => $rows['visitCount'],			
			"memberClass"=> $rows['memberClass'],
			"birthday"=> $rows['birthday'] ? $rows['birthday'] : '',
			"anniversary"=> $rows['anniversary'] ? $rows['anniversary'] : '',
			"gender"=> $rows['gender'] ? $rows['gender'] : '',
			"isSingle"=> $rows['isSingle'] ? ($rows['isSingle'] == "1" ? true:false) : ''
			
		);
		
		$status = true;
		$error = "";
		$count++;
		
		if($resultKey == ""){
			$resultKey = "Registered Mobile number found";
		}
	}
	
	
//Search for Name
if(!$status){

	$count = 0;
	
	$query = "SELECT * FROM `z_desk_guests` WHERE `name` LIKE '%{$searchKey}%'".$limiter;
	$main = mysql_query($query);
	
	while($rows = mysql_fetch_assoc($main))
	{
		$output [] = array(
			"name" => $rows['name'],
			"mobile" => $rows['mobile'],	
			"city" => $rows['city'],
			"email"=> $rows['email'],
			"branch"=> $rows['registeredBranch'],		 
			"isBlocked" => $rows['isBlocked'] == "1" ? true:false,
			"firstVisit" => $rows['firstVisit'],
			"visitCount" => $rows['visitCount'],			
			"memberClass"=> $rows['memberClass'],
			"birthday"=> $rows['birthday'] ? $rows['birthday'] : '',
			"anniversary"=> $rows['anniversary'] ? $rows['anniversary'] : '',
			"gender"=> $rows['gender'] ? $rows['gender'] : '',
			"isSingle"=> $rows['isSingle'] ? ($rows['isSingle'] == "1" ? true:false) : ''
			
		);
		
		$status = true;
		$error = "";
		
		$count++;
		
		if($resultKey == ""){
			$resultKey = "Similar Names Found";
		}
	}
	
}	


$out = array(
	"status" => $status,
	"error" => $error,
	"response" => $output,
	"message" => $resultKey,
	"count" => $count
);

echo json_encode($out);

?>
