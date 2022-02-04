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
		"error" => $vegaError_TokenExpired,
		"errorCode" => 400
	);
	die(json_encode($output));
}

//Check if the token is tampered
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$status = false;
$error = 'No salary slips found';
$bookings = "";
$searchKey = $_POST['key'];
if($searchKey == ""){
	$searchKey = date('F Y');
}

$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}


			
	//Case 1: Search with Name
	if(!$status && $searchKey != ""){		
		
		$name_query = mysql_query("SELECT `id` FROM `erp_people` WHERE `currentBranch`='{$outlet}' AND (`fName` LIKE '%{$searchKey}%' || `lName` LIKE '%{$searchKey}%' || CONCAT(`fName`, ' ', `lName`) LIKE '%{$searchKey}%')");
				
		while($nameInfo = mysql_fetch_assoc($name_query)){
			
			$main = mysql_query("SELECT * FROM `erp_payslips` WHERE `outlet`='{$outlet}' AND `staffCode`='{$nameInfo['id']}' ORDER BY `id`".$limiter);
			while($rows = mysql_fetch_assoc($main)){	
				$status = true;
				$error = "";
				$resultKey = "Similar Names Found";
				
				$user_info = mysql_fetch_assoc(mysql_query("SELECT `fName`, `lName` FROM `erp_people` WHERE `id`='{$rows['staffCode']}'"));
				$admin_info = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$rows['paidAdminCode']}'"));
								
				$bookings[] = array(
					"id" => $rows['id'],
					"staffCode" => $rows['staffCode'],
					"staffName" => $user_info['fName'].' '.$user_info['lName'],
					"amount" => $rows['amount'],
					"modeOfPayment" => $rows['modeOfPayment'],
					"dateOfPayment" => date("d-m-Y", strtotime($rows['dateOfPayment'])),
					"remarks" => $rows['remarks'] != ""? $rows['remarks'] : "-",
					"reference" => $rows['reference'],
					"adminName" => $admin_info['name'],
					"issuedMonth" => date("F Y", strtotime($rows['issuedMonth'].'25'))	
				);	
			}
		
		
		
		}
		
		
	}
	
	//Case 2: Search with Mobile
	if(!$status && $searchKey != ""){		
		
		$main = mysql_query("SELECT * FROM `erp_payslips` WHERE `outlet`='{$outlet}' AND `staffCode` = '{$searchKey}' ORDER BY `id`".$limiter);
		
		$user_info = mysql_fetch_assoc(mysql_query("SELECT `fName`, `lName` FROM `erp_people` WHERE `id`='{$rows['staffCode']}'"));
		
		$error = "";
		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Salary Slips issued to ".$user_info['fName'].' '.$user_info['lName'];
			
				$admin_info = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$rows['paidAdminCode']}'"));
						
				$bookings[] = array(
					"id" => $rows['id'],
					"staffCode" => $rows['staffCode'],
					"staffName" => $user_info['fName'].' '.$user_info['lName'],
					"amount" => $rows['amount'],
					"modeOfPayment" => $rows['modeOfPayment'],
					"dateOfPayment" => date("d-m-Y", strtotime($rows['dateOfPayment'])),
					"remarks" => $rows['remarks'] != ""? $rows['remarks'] : "-",
					"reference" => $rows['reference'],
					"adminName" => $admin_info['name'],
					"issuedMonth" => date("F Y", strtotime($rows['issuedMonth'].'25'))
				);	

		}
	}
	
	
	
	
	//Case 0: By Date (Default - Current Week)  
	if(!$status){		
		$queryDate = date("Ym", strtotime($searchKey));
			
		$main = mysql_query("SELECT * FROM `erp_payslips` WHERE `outlet`='{$outlet}' AND `dateOfPayment` LIKE '{$queryDate}%' ORDER BY `id`".$limiter);
		$error = "";
		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Salary Slips generated in ".$searchKey;
			
			
				$user_info = mysql_fetch_assoc(mysql_query("SELECT `fName`, `lName` FROM `erp_people` WHERE `id`='{$rows['staffCode']}'"));
				$admin_info = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$rows['paidAdminCode']}'"));
								
				$bookings[] = array(
					"id" => $rows['id'],
					"staffCode" => $rows['staffCode'],
					"staffName" => $user_info['fName'].' '.$user_info['lName'],
					"amount" => $rows['amount'],
					"modeOfPayment" => $rows['modeOfPayment'],
					"dateOfPayment" => date("d-m-Y", strtotime($rows['dateOfPayment'])),
					"remarks" => $rows['remarks'] != ""? $rows['remarks'] : "-",
					"reference" => $rows['reference'],
					"adminName" => $admin_info['name'],
					"issuedMonth" => date("F Y", strtotime($rows['issuedMonth'].'25'))	
				);	

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