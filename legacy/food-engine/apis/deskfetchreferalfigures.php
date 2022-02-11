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

$figure_total_referers = 0;
$figure_referers = mysql_fetch_assoc(mysql_query("SELECT COUNT(DISTINCT `referrerMobile`) as total FROM `z_desk_referrals` WHERE 1"));
if($figure_referers['total'] != "")
{
	$figure_total_referers = $figure_referers['total'];
}

$figure_total_referees = 0;
$figure_referees = mysql_fetch_assoc(mysql_query("SELECT COUNT(DISTINCT `refereeMobile`) as total FROM `z_desk_referrals` WHERE 1"));
if($figure_referees['total'] != "")
{
	$figure_total_referees = $figure_referees['total'];
}

$figure_converted_referees = 0;
$figure_converted = mysql_fetch_assoc(mysql_query("SELECT COUNT(DISTINCT `refereeMobile`) as total FROM `z_desk_referrals` WHERE `status` = 5"));
if($figure_converted['total'] != "")
{
	$figure_converted_referees = $figure_converted['total'];
}


$result = array(
	'status' => true,
	'error' => "",
	'response' => "",
	'figure_total_referers' => $figure_total_referers,
	'figure_total_referees' => $figure_total_referees,
	'figure_converted_referees' => $figure_converted_referees
);

die(json_encode($result));		
?>