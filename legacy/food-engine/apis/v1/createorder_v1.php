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

//For extra coupon validation
require_once 'validatecouponbackend.php';


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

if(!isset($_POST['outlet'])){
	$output = array(
		"status" => false,
		"error" => "Outlet is missing"
	);
	die(json_encode($output));
}

$special_menu = "z_menu";

if(isset($_POST['outlet'])){
	$check = mysql_fetch_assoc(mysql_query("SELECT code FROM z_outlets WHERE code='{$_POST['outlet']}'"));
	if($check['code']){
		$outlet = $check['code'];
		//Special Menu for IIT Madras
		if($outlet == "IITMADRAS")
		{
			$special_menu = "z_menu_IITMADRAS";
		}
	}
	else{
		$outlet = "VELACHERY";	
	}
}
else{
	$outlet = "VELACHERY";
}



if(!$_POST['isTakeAway'] && $_POST['address'] == []){
	$output = array(
		"status" => false,
		"error" => "Delivery Address is missing"
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
		"error" =>"User is blocked by Zaitoon. Please contact anasjafry@accelerate.net.in"
	);
	die(json_encode($output));
}


//Parameters
$cart = $_POST['cart'];
$address = $_POST['address'];
$comments = mysql_real_escape_string($_POST['comments']);
$modeOfPayment = $_POST['modeOfPayment'];
$outlet = $_POST['outlet'];
$location = $_POST['location'];
$isTakeAway = $_POST['isTakeAway'];

//Parameters for Analytics
$platform = $_POST['platform'];

//TO lock the order, prevent payment hacks
if($modeOfPayment == 'COD'){
	$isVerified = 1;
}
else {
	$isVerified = 0;
}


//Validate Coupon Applied
$original_coupon = $cart['cartCoupon'];
$original_cart = $cart;	
$coupon_discount = validateCoupon($userID, $original_coupon, json_encode($original_cart));

//Tampered Coupon Claim
if($cart['cartDiscount'] != $coupon_discount){
	$output = array(
		"status" => false,
		"error" => "Coupon is tampered"
	);
	die(json_encode($output));
}


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


	//Outlet Info - for Minimum Order amount validation
	$outletinfo = mysql_fetch_assoc(mysql_query("SELECT * FROM z_locations WHERE `locationCode`='{$location}' AND `outlet`='{$outlet}'"));
		

	$items = $cart['items'];

	$i = 0;
	$not_avail_list = "";
	while($i < sizeof($items)){

		$code = $items[$i]['itemCode'];

			//ITEM IS A COMBO
			//Combo Validations - Check if the combo is available to particular outlet
			if($items[$i]['isCombo']){ 
				$rowscombo = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_combos` WHERE `code`='{$code}'"));
				if($rowscombo['outlet'] != $outlet) //Combo does not applicable at selected outlet
				{
					if($not_avail_list != ""){
						$not_avail_list = $not_avail_list.", ".$rowscombo['name'];
					}
					else{
						$not_avail_list = $rowscombo['name'];
					}
					$availabilityFlag = 0;

				}
				else{
					if($rowscombo['isAvailable'] == 0){ //Item is out of stock
						if($not_avail_list != ""){
							$not_avail_list = $not_avail_list.", ".$rowscombo['name'];
						}
						else{
							$not_avail_list = $rowscombo['name'];
						}
						$availabilityFlag = 0;
					}
				}
				
				$total += $rowscombo['price']*$items[$i]['qty'];
			}


			//ITEM IS NOT A COMBO
			if(!$items[$i]['isCombo']){ 
				$myitems = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$special_menu." WHERE code='{$code}'"));
				
				//Generic Error - Item not found at all!
				if(!$myitems[$outlet])
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
					if($myitems[$outlet] == 0) //Item is not available
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
							
			}
			
		$i++;
	}
	
	//Check for errors and place order.
	if($availabilityFlag == 0)
	{		
		$status = false;
		$error = "The following dishes are currently not available - ".$not_avail_list;
		
		$output = array(
			"status" => $status,
			"error" => $error
		);

		die(json_encode($output));
	}
	else
	{
		if($isTakeAway == false)
		{
				if($cart['cartTotal'] < $outletinfo['minOrder']){
					$status = false;
					$error = "Minimum Order for the outlet is Rs. ".$outletinfo['minOrder'];
				}
				else
				{
					if($carttamper == 1 || $total != $cart['cartTotal']){
						$status = false;
						$error = "Item prices at the selected outlet are different or the cart has been tampered. Clear the cart and add the items again.";
					}
					else{
						//Cart Maximum
						if($total > 10000){
							$output = array(
								"status" => false,
								"error" => "Maximum online order sum can not exceed Rs.10,000",
								"orderid" => "",
								"amount" => ""
							);						
							die(json_encode($output));
						}
						
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
						$addressjson = json_encode($address);
						
						//Check if outlet is closed
						$outletcheck = mysql_fetch_assoc(mysql_query("SELECT `isOpen`, `isAcceptingOnlinePayment` FROM `z_outlets` WHERE `code`='{$outlet}'"));
						if($outletcheck['isOpen'] == 0){
							$output = array(
								"status" => false,
								"error" => "The nearest outlet serves in your area is now closed."
							);
							die(json_encode($output));
						}
						
						$isPrepaidAllowed = $outletcheck['isAcceptingOnlinePayment'] == 1? true: false;
						
						$query = "INSERT INTO `zaitoon_orderlist`(`locationID`, `platform`, `stamp`,`isVerified`,`outlet`, `isTakeAway`,`date`,`timePlace`, `userID`, `status`, `comments`, `cart`, `deliveryAddress`, `modeOfPayment`) VALUES ('$location', '$platform', '$dateStamp','{$isVerified}','{$outlet}','{$isTakeAway}','{$date}','{$time}','{$userID}',0,'{$comments}','{$cartjson}','{$addressjson}','{$modeOfPayment}')";
						mysql_query($query);
						//Get the order ID
						$orderInfo = mysql_fetch_assoc(mysql_query("SELECT orderID FROM `zaitoon_orderlist` WHERE `userID`='{$userID}' ORDER BY orderID DESC"));
						$orderid = $orderInfo['orderID'];
					}
				}
		}
			else
			{
				if($carttamper == 1 || $total!=$cart['cartTotal'])
				{
					$status = false;
					$error = "Item prices at the selected outlet are different or the cart has been tampered. Clear the cart and add the items again.";
				}
				else
				{
				
						//Cart Maximum
						if($total > 10000){
							$output = array(
								"status" => false,
								"error" => "Maximum online order sum can not exceed Rs.10,000",
								"orderid" => "",
								"amount" => ""
							);						
							die(json_encode($output));
						}
						
						
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
					
					$addressjson = json_encode($address);
					
					
						//Check if outlet is closed
						$outletcheck = mysql_fetch_assoc(mysql_query("SELECT `isOpen`, `isAcceptingOnlinePayment` FROM `z_outlets` WHERE `code`='{$outlet}'"));
						if($outletcheck['isOpen'] == 0){
							$output = array(
								"status" => false,
								"error" => "The selected pickup outlet is now closed."
							);
							die(json_encode($output));
						}
						
						$isPrepaidAllowed = $outletcheck['isAcceptingOnlinePayment'] == 1? true: false;
						
						
					$query = "INSERT INTO `zaitoon_orderlist`(`locationID`, `platform`, `stamp`,`isVerified`,`outlet`, `isTakeAway`, `date`,`timePlace`, `userID`, `status`, `comments`, `cart`, `deliveryAddress`, `modeOfPayment`) VALUES ('$location', '$platform', '$dateStamp', '{$isVerified}','{$outlet}','{$isTakeAway}','{$date}','{$time}','{$userID}',0,'{$comments}','{$cartjson}','','{$modeOfPayment}')";
					mysql_query($query);
					//Get the order ID
					$orderInfo = mysql_fetch_assoc(mysql_query("SELECT orderID FROM `zaitoon_orderlist` WHERE `userID`='{$userID}' ORDER BY orderID DESC"));
					$orderid = $orderInfo['orderID'];
				}
			}
		}


//Calculate Grand Sum (Total + TAXES)
$extra_sum = 0;
$taxcheck = mysql_fetch_assoc(mysql_query("SELECT `isTaxCollected`, `taxPercentage`, `isParcelCollected`, `parcelPercentageDelivery`,`parcelPercentagePickup` FROM `z_outlets` WHERE `code`='{$outlet}'"));

if($taxcheck['isTaxCollected']){
	$extra_sum = $extra_sum + ceil($total*$taxcheck['taxPercentage']);
}

if($taxcheck['isParcelCollected']){
	if($isTakeAway){
		$extra_sum = $extra_sum + ceil($total*$taxcheck['parcelPercentagePickup']);
	}
	else{
		$extra_sum = $extra_sum + ceil($total*$taxcheck['parcelPercentageDelivery']);
	}
}

$grand_sum = $total + $extra_sum - $coupon_discount;


$output = array(
		"status" => $status,
		"error" => $error,
		"orderid" => $orderid,
		"amount" => $grand_sum,
		"outletAddress" => $outletData,
		"outletContact" => $outletContact,
		"isPrepaidAllowed" => $isPrepaidAllowed
);

echo json_encode($output);
?>
