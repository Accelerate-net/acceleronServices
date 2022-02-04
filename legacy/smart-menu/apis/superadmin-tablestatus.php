<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require '../secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);
/*
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
$outlet = "";
// if($tokenid['outlet']){
// 	$outlet = $tokenid['outlet'];
// }
// else{
// 	$output = array(
// 		"status" => false,
// 		"error" => "Token is tampered"
// 	);
// 	die(json_encode($output));
// }

*/
date_default_timezone_set('Asia/Calcutta');

$outlet = "JPNAGAR";

$tablesActive = [];

$tableQuery = mysql_query("SELECT * FROM `smart_table_mapping` WHERE `assigned_branch` = '{$outlet}' AND `assigned_type` = 'TABLE' AND `is_active` = 1 ORDER BY CAST(assigned_table as SIGNED INTEGER) ASC");
while($listedTable = mysql_fetch_assoc($tableQuery)){
    $tableStatus = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `qr_code_reference` = '{$listedTable['qr_code']}' AND is_active = 1 AND order_status IN (0, 1)"));
    
    $stewardName = "Unknown";
    $stewardCode = "Unregistered";
    if($tableStatus['steward_code']){
        $stewardData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_stewards` WHERE `code` = '{$tableStatus['steward_code']}'"));
        $stewardName = $stewardData['name'] && $stewardData['name'] != "" ? $stewardData['name'] : "Unknown";
        $stewardCode = $tableStatus['steward_code'];
    }
    
    $guestMobile = $tableStatus['user_mobile'] ? $tableStatus['user_mobile'] : "";
    $guestName = "";
    $guestData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_users` WHERE `mobile` = '{$guestMobile}'"));
    if($guestData['name']){
        $guestName = $guestData['name'];
    }
    
    $hasNewOrder = false;
    $lastPunchTime = $tableStatus['date_created'];
    $alreadyPunchedCount = 0;
    if($tableStatus['master_order_id']){
        //Check if sub orders in status 0
        $subOrderQuery = mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$tableStatus['master_order_id']}' AND is_active = 1 AND status IN (0, 1) ORDER BY order_id DESC");
        $lastTime = "";
        while($subOrders = mysql_fetch_assoc($subOrderQuery)){
            if($subOrders['status'] == 0){ //some order in status 0
                $hasNewOrder = true;
            }
            else{ //status = 1 already punched orders
                $lastTime = $subOrders['date_created'];
                $alreadyPunchedCount++;
            }
        }
        
        if($lastTime != ""){
            $lastPunchTime = $lastTime;
        }
    }
    
    $activeRequest = "";
    $activeRequestId = "";
    $activeServiceRequests = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_service_requests` WHERE `fk_qr_code` = '{$listedTable['qr_code']}' AND `is_active` = 1 AND `status` = 0 ORDER BY id DESC LIMIT 1"));
    if($activeServiceRequests['id']){
        $activeRequest = $activeServiceRequests['request'];
        $activeRequestId = $activeServiceRequests['id'];
    }
    
    
    $isTableFree = $tableStatus['master_order_id'] ? false : true;
    
	$tablesActive[] = array(
		"table" => $listedTable['assigned_table'],
		"label" => $listedTable['assigned_table'],
		"isTableFree" => $isTableFree,
		"hasNewOrder" => $hasNewOrder,
		"orderStatus" => $tableStatus['order_status'],
		"masterOrderId" => $tableStatus['master_order_id'],
		"stewardName" => $stewardName,
		"stewardCode" => $stewardCode,
		"billAmount" => $tableStatus['total_bill_amount'],
		"systemBillNumber" => $tableStatus['system_bill_number'],
		"activeServiceRequest" => $activeRequest,
		"activeServiceRequestId" => $activeRequestId,
		"firstPunchTime" => $isTableFree ? "" : $tableStatus['date_created'],
		"lastPunchTime" => $isTableFree ? "" : $lastPunchTime,
		"previousPunchedCount" => $alreadyPunchedCount,
		"guestMobile" => $guestMobile,
		"guestName" => $guestName
	); 
}

$finalOutput = array(
    "status" => true,
    "data" => $tablesActive
);

die(json_encode($finalOutput));

?>
