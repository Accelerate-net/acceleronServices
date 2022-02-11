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
	$outlet = $tokenid['outlet'];
	$admin_mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$referrerName = $_POST['name'];
$referrerMobile = $_POST['mobile'];
$refereeList = $_POST['list'];
$date = date("Ymd", strtotime($_POST['date']));;


$status = true;
$error = "";

foreach($refereeList as $referee) { //foreach element in $arr

	$refereeName = $referee['name']; 
	$refereeMobile = $referee['mobile'];
	    
        //Already Exists Case
	$test = mysql_fetch_assoc(mysql_query("SELECT * FROM z_desk_referrals WHERE referrerMobile='{$referrerMobile}' AND refereeMobile='{$refereeMobile}'"));
	if($test['id'] != ""){
		//Skip
	}
	else{
	
		$query = "INSERT INTO z_desk_referrals (`referrerName`, `referrerMobile`, `refereeName`, `refereeMobile`, `date`, `branch`) VALUES 
		('{$referrerName}','{$referrerMobile}','{$refereeName}','{$refereeMobile}','{$date}','{$outlet}')";
		$main = mysql_query($query);
		
		//Send invite SMS
	}
}

$output = array(
	"response" => "",
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>