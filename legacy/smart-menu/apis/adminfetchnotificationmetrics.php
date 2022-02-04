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
$steward = "9000090001";

$allRequests = [];

//Version 1: Multiple requests at a time, only consider the latest one achieved in Version 2 below.
//$query = "SELECT * FROM `smart_service_requests` WHERE `is_active` = 1 AND `status` = 0 AND `branch` = '{$outlet}'";

//Version 2
$query = "SELECT a.* FROM smart_service_requests a INNER JOIN (SELECT fk_qr_code, MAX(id) as id FROM smart_service_requests WHERE is_active = 1 AND status = 0 AND branch = '{$outlet}' GROUP BY fk_qr_code ) AS b ON a.fk_qr_code = b.fk_qr_code AND a.id = b.id AND a.is_active = 1 AND a.status = 0 AND a.branch = '{$outlet}'";
$actionRequestQuery = mysql_query($query);
while($request = mysql_fetch_assoc($actionRequestQuery)){
    $allRequests[] = array(
        "id" => $request['id'],
        "table" => $request['table_code'],
        "stewardCode" => $steward,
        "selfRequest" => $request['fk_steward_code'] == $steward ? true : false,
        "date" => $request['date_created']
    );
}

$activeOrdersList = [];
$orderQuery = mysql_query("SELECT * FROM `smart_master_orders` WHERE `branch` = '{$outlet}' AND is_active = 1 AND order_status != 2");
while($activeOrder = mysql_fetch_assoc($orderQuery)){

    $subOrderQuery = mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$activeOrder['master_order_id']}' AND is_active = 1 AND status != 5 ORDER BY order_id");
    $isSomeOrderAlreadyPunched = false;
    $subOrderId = null;
    while($subOrders = mysql_fetch_assoc($subOrderQuery)){
        if($subOrders['status'] == 1){
            $isSomeOrderAlreadyPunched = true;
        }
        else if($subOrders['status'] == 0){ //some order in status 0
            $subOrderId = $subOrders['order_id'];
            break;
        }
    }
    
    if($subOrderId != null){
        $newOrder = array(
    		"table" => $activeOrder['table_number'],
    		"label" => $activeOrder['table_number'],
    		"masterOrderId" => $activeOrder['master_order_id'],
    		"subOrderId" => $subOrderId,
    		"type" => $isSomeOrderAlreadyPunched == true ? "RUNNING" : "NEW"
    	); 
    	
    	if($steward == $activeOrder['steward_code']){
    	    $activeOrdersList[] = $newOrder;
    	}
    }
}

$finalOutput = array(
    "status" => true,
    "serviceRequests" => $allRequests,
    "pendingOrders" => $activeOrdersList
);

die(json_encode($finalOutput));

?>
