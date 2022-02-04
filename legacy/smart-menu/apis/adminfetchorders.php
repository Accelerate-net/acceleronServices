<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Convert Timezones
function convert_time_zone($date_time, $from_tz, $to_tz) {
    $time_object = new DateTime($date_time, new DateTimeZone($from_tz));
    $time_object->setTimezone(new DateTimeZone($to_tz));
    return $time_object->format('Y-m-d H:i:s');
}

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
$steward = "9000090001";

$activeOrdersList = [];
$otherCaptainsOrdersList = [];

$orderQuery = mysql_query("SELECT * FROM `smart_master_orders` WHERE `branch` = '{$outlet}' AND is_active = 1 AND order_status != 2");
while($activeOrder = mysql_fetch_assoc($orderQuery)){
    
    $stewardName = "Unknown";
    $stewardRegisteredCode = "NotRegistered";
    if($activeOrder['steward_code']){
        $stewardData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_stewards` WHERE `code` = '{$activeOrder['steward_code']}'"));
        $stewardName = $stewardData['name'] && $stewardData['name'] != "" ? $stewardData['name'] : "Unknown";
        $stewardRegisteredCode = $stewardData['code'] && $stewardData['code'] != "" ? $stewardData['code'] : "NotRegistered";
    }
    
    $subOrderQuery = mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$activeOrder['master_order_id']}' AND is_active = 1 ORDER BY order_id");
    $orderData = null;
    $isSomeOrderAlreadyPunched = false;
    while($subOrders = mysql_fetch_assoc($subOrderQuery)){
        if($subOrders['status'] == 1){
            $isSomeOrderAlreadyPunched = true;
        }
        else if($subOrders['status'] == 0){ //some order in status 0
            $orderData = array(
                    "subOrderId" => $subOrders['order_id'],
                    "comments" => $subOrders['comments'],
                    "cart" => json_decode($subOrders['cart']),
                    "orderDate" => convert_time_zone($subOrders['date_created'], 'America/Los_Angeles', 'Asia/Kolkata')
                );
            break;
        }
    }
    
    if($orderData != null){
        $guestMobile = $activeOrder['user_mobile'] ? $activeOrder['user_mobile'] : "";
        $guestName = "";
        $guestData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_users` WHERE `mobile` = '{$guestMobile}'"));
        if($guestData['name']){
            $guestName = $guestData['name'];
        }
        
        $newOrder = array(
    		"table" => $activeOrder['table_number'],
    		"label" => $activeOrder['table_number'],
    		"masterOrderId" => $activeOrder['master_order_id'],
    		"stewardName" => $stewardName,
    		"stewardCode" => $steward,
    		"orderData" => $orderData,
    		"orderStatus" => $activeOrder['order_status'],
    		"type" => $isSomeOrderAlreadyPunched == true ? "RUNNING" : "NEW",
    		"guestMobile" => $guestMobile,
    		"guestName" => $guestName
    	); 
    	
    	if($steward == $stewardRegisteredCode){
    	    $activeOrdersList[] = $newOrder;
    	}
    	else {
    	    $otherCaptainsOrdersList[] = $newOrder;
    	}
    }
}

$finalOutput = array(
    "status" => true,
    "myPendingOrders" => $activeOrdersList,
    "otherCaptainsPendingOrders" => $otherCaptainsOrdersList
);

die(json_encode($finalOutput));

?>
