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


$status = true;
$error = "";


$id = $_POST['empID'];
$fName = $_POST['fName'];
$lName = $_POST['lName'];
$sex = $_POST['gender'];
$contact = $_POST['regMobile'];
$dob = $_POST['dob'];
$bloodGroup = $_POST['bloodGroup'];
$religion = $_POST['religion'];
$weight = $_POST['weight'];
$height = $_POST['height'];
$native = $_POST['nativePlace'];
$nativeAddress = json_encode($_POST['currentAddress']);

$role = $_POST['designation'];
$joinDate = $_POST['joinDate'];
$joinBranch = $_POST['joinBranch'];
$currentBranch = $_POST['currentBranch'];
$bankDetails = json_encode($_POST['bankInfo']);
$currentAddress = json_encode($_POST['permanentAddress']);
$isActive = 1;

$emergencyContact = $_POST['emergencyContact'];
$emergencyPhone = $_POST['emergencyPhone'];


$photoUrl = '';

//Already Exists Case
$test = mysql_fetch_assoc(mysql_query("SELECT * FROM erp_people WHERE id='{$id}'"));
if($test['id'] != ""){
	$output = array(
		"status" => false,
		"error" => "Person with same Employee ID alredy exists."
	);
	die(json_encode($output));
}


	$query = "INSERT INTO `erp_people`(`id`, `fName`, `lName`, `sex`, `contact`, `dob`, `bloodGroup`, `native`, `nativeAddress`, `photoUrl`, `documentsList`, `role`, `joinDate`, `joinBranch`, `currentBranch`, `bankDetails`, `currentAddress`, `isActive`, `height`, `weight`, `religion`, `emergencyName`, `emergencyContact`) VALUES ('{$id}','{$fName}','{$lName}','{$sex}','{$contact}','{$dob}','{$bloodGroup}','{$native}','{$nativeAddress}','{$photoUrl}','{$documentsList}','{$role}','{$joinDate}','{$joinBranch}','{$currentBranch}','{$bankDetails}','{$currentAddress}','{$isActive}','{$height}','{$weight}','{$religion}','{$emergencyContact}','{$emergencyPhone}')";
	$main = mysql_query($query);



	//Upload Photo
	if(isset($_POST['url']) && $_POST['url'] != "")
	{  
		$data = $_POST['url'];
	
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);
		
		file_put_contents("images/erp/people/".$id.".jpg", $data);
		if(file_exists("images/erp/people/".$id.".jpg")){
			$my_url = "https://zaitoon.online/services/images/erp/people/".$id.".jpg";
			mysql_query("UPDATE `erp_people` SET `photoUrl`='{$my_url}' WHERE `id`='{$id}'");
		}
	}
	


$output = array(
	"response" => $query,
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>