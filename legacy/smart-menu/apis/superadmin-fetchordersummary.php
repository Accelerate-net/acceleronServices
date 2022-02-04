<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

/*
	Version 	: 1.0
	Author  	: Abhijith C S
	Last Modified 	: 27.11.2017
	Description	: To list out the orders given a status code
*/

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require '../secure.php';

require '../errorlist.php';


$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => $vegaError_TokenMissing,
		"errorCode" => 400
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
		"error" => $vegaError_TokenInvalid,
		"errorCode" => 400
	);
	die(json_encode($output));
}


$orderstatus = $_POST['status'];
$key = $_POST['key'];

$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}

$customstatus = "1";
if(isset($_POST['status'])){
	$customstatus = "`status`='{$_POST['status']}'";	
}


$status = false;
$error = 'No orders found';
$result = "Not Found";

$list = [];

//Case N: Search against timestamp
$orderQuery = mysql_query("SELECT * FROM `smart_master_orders` WHERE `branch` = '{$outlet}' AND is_active = 1 AND `order_status` != 5"); //order_status = '{$orderstatus}'
while($activeOrder = mysql_fetch_assoc($orderQuery)){
    $stewardName = "";
    $stewardRegisteredCode = "";
    if($activeOrder['steward_code']){
        $stewardData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_stewards` WHERE `code` = '{$activeOrder['steward_code']}'"));
        $stewardName = $stewardData['name'] && $stewardData['name'] != "" ? $stewardData['name'] : "Unknown";
        $stewardRegisteredCode = $stewardData['code'] && $stewardData['code'] != "" ? $stewardData['code'] : "Unregistered";
    }
    
    $subOrderQuery = mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$activeOrder['master_order_id']}' AND is_active = 1 ORDER BY order_id");
    $orderData = [];
    $isSomeOrderAlreadyPunched = false;
    while($subOrders = mysql_fetch_assoc($subOrderQuery)){
        $orderData[] = array(
            "status" => $subOrders['status'],
            "subOrderId" => $subOrders['order_id'],
            "comments" => $subOrders['comments'],
            "cart" => json_decode($subOrders['cart']),
            "orderDate" => $subOrders['date_created'],
            "updateDate" => $subOrders['date_updated']
        );
        if($subOrders['status'] == 1){
            $isSomeOrderAlreadyPunched = true;
        }
    }
    
    $timestampCreated = strtotime($activeOrder['date_created']);
    if($orderData != []){
        $guestMobile = $activeOrder['user_mobile'] ? $activeOrder['user_mobile'] : "";
        $guestName = "";
        $guestData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_users` WHERE `mobile` = '{$guestMobile}'"));
        if($guestData['name']){
            $guestName = $guestData['name'];
        }
        
        $list[] = array(
    		"table" => $activeOrder['table_number'],
    		"label" => $activeOrder['table_number'],
    		"date" => date("d-m-Y", $timestampCreated),
    		"masterOrderId" => $activeOrder['master_order_id'],
    		"stewardName" => $stewardName,
    		"stewardCode" => $stewardRegisteredCode,
    		"orderData" => $orderData,
    		"orderStatus" => $activeOrder['order_status'],
    		"type" => $isSomeOrderAlreadyPunched == true ? "RUNNING" : "NEW",
    		"billAmount" => $activeOrder['total_bill_amount'],
    		"paymentMode" => $activeOrder['mode_of_payment'] == 1 ? "Online" : "Offline",
    		"paymentReference" => $activeOrder['mode_of_payment'] == 1 ? $activeOrder['razorpay_transaction_id'] : "",
    		"guestMobile" => $guestMobile,
    		"guestName" => $guestName,
    		"orderDate" => $activeOrder['date_created'],
    		"updateDate" => $activeOrder['date_updated']
    	);
    }
    
    $status = true;
	$error = '';
	$result = "Orders placed on ".date("d-m-Y", $timestampCreated);
}





// //Case 1: Search for Mobile Number

// $query = "SELECT * FROM `zaitoon_orderlist` WHERE ".$customstatus." AND `outlet`='{$outlet}' AND `userID`='{$key}' AND `isVerified`='1' ORDER BY `orderID` DESC".$limiter;
// $all = mysql_query($query);
// while($order = mysql_fetch_assoc($all)){

// 	$userinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_users WHERE mobile='{$order['userID']}'"));
// 	$agentinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_deliveryagents WHERE code='{$order['agentMobile']}'")); 	
	
// 	$status = true;
// 	$error = '';
// 	$result = "Order placed by ".$userinfo['name'].' ('.$order['userID'].')';
	
// 	$cart = json_decode($order['cart']);
// 	$list[] = array(
// 		'orderID' => $order['orderID'], 
// 		'userID' => $order['userID'],
// 		'userName' => $userinfo['name'],
// 		'status' => $order['status'],  
// 		'cart' => $cart,
// 		'deliveryAddress' => json_decode($order['deliveryAddress']),
// 		'date' => $order['date'], 
// 		'isTakeaway' => $order['isTakeaway']==1?true:false,
// 		'timePlace' => $order['timePlace'], 
// 		'timeConfirm' => $order['timeConfirm'], 
// 		'timeDispatch' => $order['timeDispatch'], 		
// 		'timeDeliver' => $order['timeDeliver'],
// 		'comments' => $order['comments'],
// 		'agentCode' => $order['agentMobile'],
// 		'amountPaid' => $order['paidAmount'],
// 		'platform' => $order['platform'],
// 		'agentName' => $agentinfo['name'],		
// 		'isPrepaid' => $order['modeOfPayment'] == "PRE"?true:false,		
// 		'feedback' => json_decode($order['feedback'])                                
// 	);
// }

// //Case 2: Search for Order ID
// if(!$status){
// $query = "SELECT * FROM `zaitoon_orderlist` WHERE ".$customstatus." AND `outlet`='{$outlet}' AND `orderID`='{$key}' AND `isVerified`='1' ORDER BY `orderID` DESC".$limiter;
// $all = mysql_query($query);
// while($order = mysql_fetch_assoc($all)){

// 	$userinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_users WHERE mobile='{$order['userID']}'"));
// 	$agentinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_deliveryagents WHERE code='{$order['agentMobile']}'")); 
		
// 	$status = true;
// 	$error = '';
// 	$result = "Order #".$order['orderID'];
	
// 	$cart = json_decode($order['cart']);
// 	$list[] = array(
// 		'orderID' => $order['orderID'], 
// 		'userID' => $order['userID'],
// 		'userName' => $userinfo['name'],
// 		'status' => $order['status'],  
// 		'cart' => $cart,
// 		'deliveryAddress' => json_decode($order['deliveryAddress']),
// 		'date' => $order['date'], 
// 		'isTakeaway' => $order['isTakeaway']==1?true:false,
// 		'timePlace' => $order['timePlace'], 
// 		'timeConfirm' => $order['timeConfirm'], 
// 		'timeDispatch' => $order['timeDispatch'], 			
// 		'timeDeliver' => $order['timeDeliver'],
// 		'comments' => $order['comments'],
// 		'agentCode' => $order['agentMobile'],
// 		'amountPaid' => $order['paidAmount'],
// 		'platform' => $order['platform'],				
// 		'agentName' => $agentinfo['name'],		
// 		'isPrepaid' => $order['modeOfPayment'] == "PRE"?true:false,		
// 		'feedback' => json_decode($order['feedback'])                                
// 	);
// }
// }

// //Case 3: Search for Date Stamp
// if(!$status){
// $query = "SELECT * FROM `zaitoon_orderlist` WHERE ".$customstatus." AND `outlet`='{$outlet}' AND `stamp`='{$key}' AND `isVerified`='1' ORDER BY `orderID` DESC".$limiter;
// $all = mysql_query($query);
// while($order = mysql_fetch_assoc($all)){

// 	$userinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_users WHERE mobile='{$order['userID']}'"));
// 	$agentinfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_deliveryagents WHERE code='{$order['agentMobile']}'")); 
	
// 	$status = true;
// 	$error = '';
// 	$result = "Orders placed on ".$order['date'];
	
// 	$cart = json_decode($order['cart']);
// 	$list[] = array(
// 		'orderID' => $order['orderID'], 
// 		'userID' => $order['userID'],
// 		'userName' => $userinfo['name'],
// 		'status' => $order['status'],  
// 		'cart' => $cart,
// 		'deliveryAddress' => json_decode($order['deliveryAddress']),
// 		'date' => $order['date'], 
// 		'isTakeaway' => $order['isTakeaway']==1?true:false,
// 		'timePlace' => $order['timePlace'], 
// 		'timeConfirm' => $order['timeConfirm'], 
// 		'timeDispatch' => $order['timeDispatch'], 			
// 		'timeDeliver' => $order['timeDeliver'],
// 		'comments' => $order['comments'],
// 		'isPrepaid' => $order['modeOfPayment'] == "PRE"?true:false,
// 		'agentCode' => $order['agentMobile'],
// 		'amountPaid' => $order['paidAmount'],
// 		'platform' => $order['platform'],				
// 		'agentName' => $agentinfo['name'],
// 		'feedback' => json_decode($order['feedback'])                                
// 	);
// }
// }


$output = array(
	"status" => $status,
	"error" => $error,
	"errorCode" => '',
	"result" => $result,
	"response" => $list
);

echo json_encode($output);
		
?>