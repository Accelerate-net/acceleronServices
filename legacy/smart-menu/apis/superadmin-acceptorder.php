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
$master_order_id = $_POST['masterOrderId'];
$sub_order_id = $_POST['subOrderId'];
$cartChanged = $_POST['cartChanged'];
$newCart = $_POST['cartFinal'];


function errorResponse($error){
    $output = array(
		"status" => false,
		"error" => $error
	);
	die(json_encode($output));
}

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


$orderCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `branch` = '{$outlet}' AND is_active = 1 AND order_status != 2 AND master_order_id = '{$master_order_id}'"));
$isFailed = true;
if($orderCheck['master_order_id'] == $master_order_id){
    $subOrderCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$master_order_id}' AND `order_id` = '{$sub_order_id}' AND `is_active` = 1 AND status = 0"));
    if($subOrderCheck['order_id'] == $sub_order_id){
        if($cartChanged == 1){
            //Re-validate cart
            
            $sub_total = 0;
            $carttamper = 0;
      
            $i = 0;
            $items = $newCart;
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
            
            if($GLOBALS['availabilityFlag'] == 0){		
            	errorResponse("The following dishes are currently not available - ".$GLOBALS['itemsNotAvailable']);
            }
            else if($carttamper == 1){
                errorResponse("Something unexpected happened. Manually punch this order.");
            }

            $cartjson = json_encode($newCart);
            mysql_query("UPDATE `smart_orders` SET `status`= 1, `cart`= '{$cartjson}', `total_amount` = '{$sub_total}'  WHERE `order_id` = '{$sub_order_id}'");
            $isFailed = false;
        }
        else {
            mysql_query("UPDATE `smart_orders` SET `status`= 1 WHERE `order_id` = '{$sub_order_id}'");
            $isFailed = false;
        }
    }
}

$finalOutput = array(
    "status" => !$isFailed,
    "data" => $isFailed ? "Error: The order was not accepted" : "The order is successfully accepted"
);

die(json_encode($finalOutput));

?>
