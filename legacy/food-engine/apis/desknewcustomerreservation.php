<?php

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//SMS Credentials
define('SMS_INCLUDE_CHECK', true);
require 'smsblackbox.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST['details'])){
	$output = array(
		"status" => false,
		"error" => "Reservation Details are missing"
	);
	die(json_encode($output));
}


date_default_timezone_set('Asia/Calcutta');
$dateStamp = date("dmY");

$details = $_POST['details'];

//Maximum Allowed Count
if($details['count'] > 10){
	$output = array(
		"status" => false,
		"error" => "Maximum seats we can reserve at a time is 10. Contact us for Party Arrangements."
	);
	
	die(json_encode($output));
}

	$status = true;
	$error = "";
	
	$details['outlet'] = "IITMADRAS";
	
	$query = "INSERT INTO `z_reservations`(`channel`, `stamp`, `userID`, `userName`, `userEmail`, `outlet`, `date`, `time`, `count`, `comments`, `isBirthday`, `isAnniversary`) VALUES ('WEBSITE', '{$dateStamp}','{$details['mobile']}','{$details['name']}','{$details['email']}','{$details['outlet']}','{$details['date']}','{$details['time']}', '{$details['count']}','{$details['comments']}','{$details['birthday']}','{$details['anniversary']}')";
	
	$main = mysql_query($query);
	
	$formatTime = date('g:i a', strtotime($details['time']));
	$myname = substr($details['name'], 0, 14);
	
	$outlet_check = mysql_fetch_assoc(mysql_query("SELECT `name`,`contact` FROM `z_outlets` WHERE `code`='{$details['outlet']}'"));
	
	//Confirmation SMS to customer
	$message = "Your table reservation is confirmed for ".$details['count']." at Zaitoon, ".$outlet_check['name']." at ".$formatTime." on ".$details['date'].". Call ".$outlet_check['contact'].". www.zaitoon.online";
	//vegaSendSMS($details['mobile'], $message);

	
	//Message to Manager
	$message = "New Reservation at ".$formatTime." on ".$details['date'].". COUNT: ".$details['count'].", NAME: ".$myname.", MOBILE: ".$details['mobile'];
	//vegaSendSMS($outlet_check['contact'], $message);		
			
			
	$details['outlet'] = $outlet_check['name'];
	$details['time'] = date('g:i a', strtotime($details['time']));
	$details['date'] = date("F j, Y", strtotime($details['date']));

$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $details
);

echo json_encode($output);
?>
