<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

/* IMPORTANT WARNING:
Ensure that the Deals added to the cart
are ALL from A SINGLE OUTLET. All passes has to be
from a single outlet because, RazorPay Key will be
issues back to client app, based on the items in the
deal cart. If different outlet passes are added to cart,
it is impossible to split the total amount.
*/

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';


//Razorpay APIs
require 'razorpay/Razorpay.php';


$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['cart'])){
	$output = array(
		"status" => false,
		"error" => "Cart Object is missing"
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
if($tokenid['mobile']){
	$userID = $tokenid['mobile'];
	$mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

//User Blocked Check
$userblockcheck = mysql_fetch_assoc(mysql_query("SELECT isBlocked from z_users WHERE mobile='{$userID}'"));
if($userblockcheck['isBlocked'] == 1){
	$output = array(	
		"status" => false,
		"error" =>"User is blocked by Zaitoon. Please contact care@zaitoon.online"
	);
	die(json_encode($output));
}


//Parameters
$cart = $_POST['cart'];

//Parameters for Analytics
$platform = $_POST['platform'];


date_default_timezone_set('Asia/Calcutta');
$date = date("j F, Y");
$time = date("g:i a");

$dateStamp = date("dmY");

$status = false;
$error = "Failed to create the order";
$orderid = "";
$outletData = "";
$outletContact = "";


$carttamper = 0;
$total = 0;
$grand_sum = 0;
$availabilityFlag = 1;

$isPrepaidAllowed = false;
$razorKey == '';

	$outlet = '';
	
	$items = $cart['items'];

	$i = 0;
	$not_avail_list = "";
	while($i < sizeof($items)){

				$code = $items[$i]['itemCode'];

				$myitems = mysql_fetch_assoc(mysql_query("SELECT * FROM z_deals WHERE id='{$code}'"));
				
				if($outlet == ''){
					$outlet = $myitems['outlet'];
				}
				else if($outlet != $myitems['outlet']){
					$output = array(	
						"status" => false,
						"error" => "Offers can not be purchased from multiple outlets in the same transaction. Purchase them separately."
					);
					die(json_encode($output));					 
				}
				
				//Generic Error - Item not found at all!
				if(!$myitems['id'])
				{
					if($not_avail_list != ""){
						$not_avail_list = $not_avail_list.", ".$items[$i]['itemName'];
					}
					else{
						$not_avail_list = $items[$i]['itemName'];
					}
					
					$availabilityFlag = 0;				
				}
				else{								
					if($myitems['isActive'] == 0) //Item is not available
					{
						if($not_avail_list != ""){
							$not_avail_list = $not_avail_list.", ".$myitems['name'];
						}
						else{
							$not_avail_list = $myitems['name'];
						}
						
						$availabilityFlag = 0;
					}
					else
					{
						if($myitems['price'] != $items[$i]['itemPrice']){
							//Do not check price if custom item
							if(!$items[$i]['isCustom']){
								$carttamper = 1;
							}
						}
		
						if(!$items[$i]['isCustom']){
							$total += $myitems['price']*$items[$i]['qty'];
						}
						else{
							$total += $items[$i]['itemPrice']*$items[$i]['qty'];
						}
					}
				}							
			
		$i++;
	}
	
	//Check for errors and place order.
	if($availabilityFlag == 0)
	{		
		$status = false;
		$error = "The following offers are currently not available - ".$not_avail_list;
		
		$output = array(
			"status" => $status,
			"error" => $error
		);

		die(json_encode($output));
	}
	else
	{

				if($cart['cartTotal'] < 1){
					$status = false;
					$error = "Minimum sum of Rs. 10 to Checkout";
				}
				else
				{
					if($carttamper == 1 || $total != $cart['cartTotal']){
						$status = false;
						$error = "The cart has been tampered. Clear the cart and add the offers again.";
					}
					else{
						
						$status = true;
						$error = "";
						$final_cart = $_POST['cart'];
						$final_cart['cartTotal'] = $final_cart['cartTotal'] - $coupon_discount;
						
						//Process Cart - Remove unwanted stuff.	
						$n = 0;
						while($final_cart['items'][$n]){				
							unset($final_cart['items'][$n]['isVeg']);
							unset($final_cart['items'][$n]['isAvailable']);							
							unset($final_cart['items'][$n]['custom']);														
							$n++;
						}
					
						$cartjson = json_encode($final_cart);
						
						//Check if outlet is closed
						$outletcheck = mysql_fetch_assoc(mysql_query("SELECT `isAcceptingOnlinePayment`, `razorpayID` FROM `z_outlets` WHERE `code`='{$outlet}'"));
						$razorKey = $outletcheck['razorpayID'];
						$isPrepaidAllowed = $outletcheck['isAcceptingOnlinePayment'] == 1? true: false;
						
						if($razorKey == '' || !$isPrepaidAllowed){
							$output = array(	
								"status" => false,
								"error" => "Pass issuing outlet does not accept online payment currently. Try later."
							);
							die(json_encode($output));						
						}
						
						
						
						$query = "INSERT INTO `zaitoon_passeslist`(`platform`, `stamp`,`isVerified`,`outlet`,`date`,`timePlace`, `userID`, `status`, `cart`, `modeOfPayment`) VALUES ('$platform', '$dateStamp', 0, '{$outlet}','{$date}','{$time}','{$userID}',0,'{$cartjson}', 'PRE')";
						mysql_query($query);
						
						//Get the order ID
						$orderInfo = mysql_fetch_assoc(mysql_query("SELECT orderID FROM `zaitoon_passeslist` WHERE `userID`='{$userID}' ORDER BY orderID DESC"));
						$orderid = $orderInfo['orderID'];
						
					}
				}

	}
	
/* Create Actual Passes */
$items = $cart['items'];
$i = 0;
while($i < sizeof($items)){
	mysql_query("INSERT INTO `zaitoon_passmasterlist`(`orderID`, `userID`, `dealID`, `choice`, `amount`) VALUES ('{$orderid}', '{$userID}', '{$items[$i]['itemCode']}', '{$items[$i]['variant']}', '{$items[$i]['itemPrice']}')");
	$i++;
}


//Calculate Grand Sum (Total + TAXES)
$extra_sum = 0;

$grand_sum = $total + $extra_sum;



/* Razorpay ID Creation */
use Razorpay\Api\Api;
$razor_order_id = '';

$api = new Api('rzp_live_4NeEyLZf2m10Ry', 'bLIuGcYQavAQad1idI8FNyXC');
$razor_order  = $api->order->create(array('receipt' => 'R#'.$orderid, 'amount' => $grand_sum*100, 'currency' => 'INR', 'payment_capture' => 1));
$razor_order_id = $razor_order->id;

//Update Paid to be paid:
mysql_query("UPDATE `zaitoon_passeslist` SET `paidAmount`='{$grand_sum}', `razorpay_order_id` = '{$razor_order_id}' WHERE `orderID`='{$orderid}'");

$output = array(
		"status" => $status,
		"error" => $error,
		"reference" => $razor_order_id,
		"orderid" => $orderid,
		"amount" => $grand_sum,
		"isPrepaidAllowed" => $isPrepaidAllowed,
		"accountKey"=> $razorKey
);

echo json_encode($output);
?>
