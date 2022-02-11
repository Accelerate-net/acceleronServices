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
	$outlet= $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


$status = false;
$error = 'No upcoming reservations found';
$bookings = "";
$searchKey = $_POST['key'];
if($searchKey == ""){
	$searchKey = date('d-m-Y');
}

$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}
		
	//Case 1: Search with Name
	if(!$status && $searchKey != ""){		
		
		$main = mysql_query("SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `userName` LIKE '%{$searchKey}%' ORDER BY `id`".$limiter);
		$error = "SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `userName` LIKE %'{$searchKey}'% ORDER BY `id`";
		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Similar Names Found";
			
			if($rows['status'] == 0){
				$my_status = "Received";
			}
			else if($rows['status'] == 1){
				$my_status = "Seated";
			}
			else if($rows['status'] == 2){
				$my_status = "Completed";
			}
			else if($rows['status'] == 5){
				$my_status = "Cancelled";
			}
			else if($rows['status'] == 6){
				$my_status = "No Show";
			}
			else{
				$my_status = "-";
			}
			
			$bookings[] = array(
				"date" => $rows['date'],
				"time" => date("g:i a", strtotime($rows['time'])),
				"id" => $rows['id'],
				"count" => $rows['count'],
				"user" => $rows['userName'],
				"mobile" => $rows['userID'],
				"email" => $rows['userEmail'] != ""? $rows['userEmail'] : "-",
				"isBirthday" => $rows['isBirthday'] == 1? true: false,
				"isAnniversary" => $rows['isAnniversary'] == 1? true: false,
				"statusCode" => $rows['status'],
				"status" => $my_status,
				"statusCode" => $rows['status'],
				"comments" => $rows['comments'] != ""? $rows['comments'] : "-"	
			);	
		}
	}
	
	//Case 2: Search with Mobile
	if(!$status && $searchKey != ""){		
		
		$main = mysql_query("SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `userID` = '{$searchKey}' ORDER BY `id`".$limiter);
		$error = "SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `userName` LIKE %'{$searchKey}'% ORDER BY `id`";
		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Results found for ".$searchKey;
			
			if($rows['status'] == 0){
				$my_status = "Received";
			}
			else if($rows['status'] == 1){
				$my_status = "Seated";
			}
			else if($rows['status'] == 2){
				$my_status = "Completed";
			}
			else if($rows['status'] == 5){
				$my_status = "Cancelled";
			}
			else if($rows['status'] == 6){
				$my_status = "No Show";
			}
			else{
				$my_status = "-";
			}
			
			$bookings[] = array(
				"date" => $rows['date'],
				"time" => date("g:i a", strtotime($rows['time'])),
				"id" => $rows['id'],
				"count" => $rows['count'],
				"user" => $rows['userName'],
				"mobile" => $rows['userID'],
				"email" => $rows['userEmail'] != ""? $rows['userEmail'] : "-",
				"isBirthday" => $rows['isBirthday'] == 1? true: false,
				"isAnniversary" => $rows['isAnniversary'] == 1? true: false,
				"statusCode" => $rows['status'],
				"status" => $my_status,
				"statusCode" => $rows['status'],
				"comments" => $rows['comments'] != ""? $rows['comments'] : "-"	
			);	
		}
	}
	
	
	
	
	//Case 0: By Date (Default - Current Week)
	if(!$status){		
		$queryDate = $searchKey; 	
		
		$n = 0;
		while($n < 7){
				
			$main = mysql_query($query = "SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `date`='{$queryDate}' ORDER BY time");
			while($rows = mysql_fetch_assoc($main)){	
				$status = true;
				$error = "";
				$resultKey = "Result Found in the week";
				
				if($rows['status'] == 0){
					$my_status = "Received";
				}
				else if($rows['status'] == 1){
					$my_status = "Seated";
				}
				else if($rows['status'] == 2){
					$my_status = "Completed";
				}
				else if($rows['status'] == 5){
					$my_status = "Cancelled";
				}
				else if($rows['status'] == 6){
					$my_status = "No Show";
				}
				else{
					$my_status = "-";
				}
				
				$bookings[] = array(
					"date" => $rows['date'],
					"time" => date("g:i a", strtotime($rows['time'])),
					"id" => $rows['id'],
					"count" => $rows['count'],
					"user" => $rows['userName'],
					"mobile" => $rows['userID'],
					"email" => $rows['userEmail'] != ""? $rows['userEmail'] : "-",
					"isBirthday" => $rows['isBirthday'] == 1? true: false,
					"isAnniversary" => $rows['isAnniversary'] == 1? true: false,
					"status" => $my_status,
					"statusCode" => $rows['status'],
					"comments" => $rows['comments'] != ""? $rows['comments'] : "-"	
				);
		
			}
				
			$queryDate = date('d-m-Y', strtotime($queryDate.' +1 day'));
			$n++;
		}			
	}


$output = array(
	"status" => $status,
	"error" => $error,
	"message" => $resultKey,
	"response" => $bookings
);

echo json_encode($output);

?>
