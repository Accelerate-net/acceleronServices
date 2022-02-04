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

function errorResponse($error){
    $output = array(
		"status" => false,
		"error" => $error
	);
	die(json_encode($output));
}

$_POST = json_decode(file_get_contents('php://input'), true);

// Encryption Validation
if(!isset($_POST['token'])){
    errorResponse("Access Token is missing");
}


if(!isset($_POST['cart'])){
    errorResponse("Cart is missing");
}

if(!isset($_POST['branchCode']) || !isset($_POST['mode']) || !isset($_POST['qrCodeReference']) || !isset($_POST['tableNumber']) || !isset($_POST['userMobile'])){
	errorResponse("Required values are missing");
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
    errorResponse("User session expired");
}

$userID = "";
$mobile = "";
//Check if the token is tampered
if($tokenid['mobile'] == $_POST['userMobile']){
	$userID = $tokenid['mobile'];
	$mobile = $tokenid['mobile'];
}
else{
    errorResponse("Token is invalid");
}


//Parameters
$cart = $_POST['cart'];
$comments = mysql_real_escape_string($_POST['comments']);
$outlet = $_POST['branchCode'];
$mode = $_POST['mode'];
$qrCodeReference = $_POST['qrCodeReference'];
$tableNumber = $_POST['tableNumber'];
$userMobile = $_POST['userMobile'];
$peerCode = $_POST['peerCode'];

if($userID != $userMobile){
    errorResponse("Invalid user");
}

$previousMasterOrder = "";
if(isset($_POST['masterOrderId'])){
    $previousMasterOrder = $_POST['masterOrderId'];    
} 

//User Blocked Check
$userblockcheck = mysql_fetch_assoc(mysql_query("SELECT * from smart_registered_users WHERE mobile='{$userMobile}'"));
if($userblockcheck['is_blocked'] == 1){
    errorResponse("You are blocked by Zaitoon. Please contact hello@zaitoon.restaurant");
}

//Branch Check
$branchCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM smart_branch_master WHERE branch='{$outlet}'"));
if($branchCheck['branch'] != $outlet){
  	errorResponse("Unknown branch"); 
}

//Check if outlet is closed
// if($branchCheck['is_open'] == 0){
// 	$output = array(
// 		"status" => false,
// 		"error" => "The selected pickup outlet is now closed."
// 	);
// 	die(json_encode($output));
// }


//Do QR Code Validations
$tableData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_table_mapping` WHERE `qr_code` ='{$qrCodeReference}' AND is_active = 1 AND is_maintain_mode = 0"));
if($tableData['qr_code'] != $qrCodeReference || $branchCheck['enable_smart_order'] != 1){
    errorResponse("This QR code is not active");
}


function getMaskedNumber($number){
    for($i = 0; $i < 10; $i++){
        if($i <= 1 || $i >= 7){
            //Nothing
        }
        else {
            $number[$i] = 'X';
        }
    }
    return $number;
}


//Already Order Exists on this QR
$orderCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `qr_code_reference` = '{$qrCodeReference}' AND `order_status` IN (0, 1)"));
if($orderCheck['master_order_id']){ //NOT FREE
    $masterPeerCode = $orderCheck['peer_code'];
    if($orderCheck['order_status'] == 1){ //bill already taken
        errorResponse("Sorry, you can't make any new orders now, bill already taken."); 
    }
    
    if($orderCheck['user_mobile'] == $userID){
        if($previousMasterOrder == ""){ //Trying to place new order (master id not passed)
            errorResponse("Something went wrong, please login again");       
        }
    }
    else {
        $maskedNumber = getMaskedNumber($orderCheck['user_mobile']);
        if($peerCode == 0){
            errorResponse("An order is already in progress on this table. Please enter the peer code from ".$maskedNumber);
        } else if($peerCode != $masterPeerCode){
            errorResponse("Incorrect peer code, get the 4 digit code from ".$maskedNumber);
        }
        
        
        //Fix: Out of 2 customers A and B, both A and B makes order. A places master order while B was making the order. Now when B places the order, he can't pass the master order id.
        if($previousMasterOrder == ""){
            $previousMasterOrder = $orderCheck['master_order_id'];
        }
    }
}


date_default_timezone_set('Asia/Calcutta');
$status = false;
$error = "Failed to create the order";


//Avg serving time
$servingTime = 0;


//mark unavailable items
$GLOBALS['itemsNotAvailable'] = "";
$GLOBALS['availabilityFlag'] = 1;
function updateNotAvailable($itemName){
	if($GLOBALS['itemsNotAvailable'] != ""){
		$GLOBALS['itemsNotAvailable'] = $GLOBALS['itemsNotAvailable'].", ".$itemName;
	}
	else{
		$GLOBALS['itemsNotAvailable'] = $itemName;
	}
	
	$GLOBALS['availabilityFlag'] = 0;	
}

$items = $cart;
$orderid = "";
$carttamper = 0;

$sub_total = 0;

$i = 0;
while($i < sizeof($items)){

    $code = $items[$i]['code'];
	$serverItems = mysql_fetch_assoc(mysql_query("SELECT * FROM smart_menu_branch as mb, smart_menu_master as mm WHERE mm.master_code = mb.fk_master_code AND mb.fk_master_code='{$code}' AND mb.branch_code='{$outlet}'"));
    
    if(!$serverItems['id']) {
		updateNotAvailable($items[$i]['name']);
	}
	else{								
		if($serverItems['is_available'] == 0) //Item is not available
		{
		    updateNotAvailable($serverItems['name']);
		}
		else {
			if($serverItems['price'] != $items[$i]['price']){
				//Do not check price if custom item
				if($serverItems['is_customisable'] == 0){
					$carttamper = 1;
				}
			}

			if($serverItems['is_customisable'] == 1){
			    $serverVariants = json_decode($serverItems['customisation_options'], true);
			    $v = 0;
			    for($v = 0; $v < sizeof($serverVariants); $v++){
			        if($serverVariants[$v]['variant'] == $items[$i]['variant']){
			            $sub_total += $serverVariants[$v]['price'] * $items[$i]['qty'];
			            break;
			        }
			        
			        if($v == sizeof($items)){
			            updateNotAvailable($serverItems['name']." (".$items[$i]['variant'].")");
			        }
			    }
			}
			else{
				$sub_total += $serverItems['price'] * $items[$i]['qty'];
			}
		}
	}

    $i++;
}

//Check for errors and place order.
if($GLOBALS['availabilityFlag'] == 0)
{		
	errorResponse("The following dishes are currently not available - ".$GLOBALS['itemsNotAvailable']);
}
else {
    
	if($carttamper == 1){
	    errorResponse("Something unexpected happened. Clear the cart and add the items again.");
	}
	else
	{
	
		//Cart Maximum
		if($total > 20000){
		    errorResponse("Individual order sum can not exceed Rs. 20,000");
		}
			
			
		$status = true;
		$error = "";
		
		$cartjson = json_encode($cart);
		
		$isFirstOrder = true;
		if($previousMasterOrder != ""){ //Second Order
		    $orderid = $previousMasterOrder;
		    $isFirstOrder = false;
		}
		else { //First Order
		    $masterPeerCode = rand(1001,9999);
		    
		    $mappedSteward = "";
		    $mappedStewardCheck = mysql_fetch_assoc(mysql_query("SELECT `steward_code` FROM `smart_steward_table_mapping` WHERE `table_number` = '{$tableNumber}' AND `branch` = '{$outlet}'"));
		    if($mappedStewardCheck['steward_code'] != ""){
		        $mappedSteward = $mappedStewardCheck['steward_code'];
		    }
		    
		  	$query = "INSERT INTO `smart_master_orders`(`user_mobile`, `table_number`, `branch`, `qr_code_reference`, `peer_code`, `steward_code`) VALUES ('{$userMobile}', '{$tableNumber}', '{$outlet}', '{$qrCodeReference}', '{$masterPeerCode}', '{$mappedSteward}')";
    		mysql_query($query);
    		
    		//Get the order ID
    		$orderInfo = mysql_fetch_assoc(mysql_query("SELECT master_order_id FROM `smart_master_orders` WHERE `user_mobile`='{$userMobile}' ORDER BY master_order_id DESC"));
    		$orderid = $orderInfo['master_order_id'];  
		}

		//Create sub order - if master order is still in status 1 only
		if(!$isFirstOrder){ //Verify if masterOrder is still pending
		    $masterOrderVerification = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `master_order_id`='{$orderid}'"));
		    if($masterOrderVerification['order_status'] == 1){
		        errorResponse("The order has been already billed");
		    }
		    else if($masterOrderVerification['order_status'] == 2){
		        errorResponse("The order has been already completed");
		    }
		    else if($masterOrderVerification['order_status'] == 5){
		        errorResponse("This order has been rejected by Zaitoon");
		    }
		}
		
    	//Proceed to create sub-order	
		$extrasjson = "";
		$suborderquery = "INSERT INTO `smart_orders`(`fk_peer_user_mobile`, `fk_master_order`, `comments`, `total_amount`, `cart`, `extra_charges`) VALUES ('{$userMobile}', '{$orderid}', '{$comments}', '{$sub_total}', '{$cartjson}', '{$extrasjson}')";
		mysql_query($suborderquery);
	}  
}

$output = array(
	"status" => $status,
	"error" => $error,
	"servingTime" => $servingTime,
	"orderid" => $orderid,
	"masterPeerCode" => $masterPeerCode
);

echo json_encode($output);
?>
