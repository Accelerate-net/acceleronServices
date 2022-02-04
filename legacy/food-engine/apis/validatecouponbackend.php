<?php
//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

function validateCoupon($user, $code, $encoded_cart, $outlet_code){

$cart = json_decode($encoded_cart, true);

$detailed = mysql_query("SELECT * FROM z_couponrules WHERE code='{$code}' AND isActive = 1");
if($info = mysql_fetch_assoc($detailed)){
	$coupon = json_decode($info['rule'], true);
	$rule = $coupon['rule']; //COUPON RULE
	$voucherFlag = false;
	
	/* Outlet wise restriction */
	if($info['list'] != ''){
		$outletsList = explode(',', $info['list']);
		$unique_list = array_unique($outletsList); 
		
		$isValidAcrossOutlet = false;
		foreach ($unique_list as $validOutlet) {
			if($outlet_code == $validOutlet){
				$isValidAcrossOutlet = true;
				break;
			}	    		
		}	
		
		//Not valid case
		if(!$isValidAcrossOutlet){
			return -1;
		}	
		
	}
	
}
else{
	//Check for vouchers
	$voucher_check = mysql_query("SELECT * FROM `z_vouchers` WHERE `code`='{$code}' AND isActive = 1");
	if($voucher_info = mysql_fetch_assoc($voucher_check)){
		if($voucher_info['isRestricted'] == 1 && $voucher_info['userRestriction'] != $user){
			return 0;
		}
		else if($voucher_info['isActive'] == 0){
			return 0;		
		}
		else if($voucher_info['expiry'] < $todayStamp){
			return 0;		
		}
		else{
			$voucherFlag = true;
		}
	}
	else{
		return 0;
	}
}


$discount = 0;

if($voucherFlag){
	$i = 0;
	$total = 0;
	while($cart['items'][$i]['itemCode']){
		$total = $total + ($cart['items'][$i]['itemPrice']*$cart['items'][$i]['qty']);
		$i++;
	}
	
	$voucher_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_vouchers` WHERE `code`='{$code}'"));
	$voucher_value = 0;
	if($voucher_info['value'] != ""){
		$voucher_value = $voucher_info['value'];
	}
	
	if($voucher_info['minAmount'] > $total){
		return 0;	
	}
	
	if($voucher_value < $total){
		$discount = $voucher_value;		
	}
	else{
		$discount = $total;
	}	
}
else{
	//Rules
	switch ($rule){
		case "FIRSTORDER":{
	
			//Ensure the user makes his first order
			$result = mysql_query("SELECT * FROM zaitoon_orderlist WHERE userID='{$user}'");
			if(!$order = mysql_fetch_assoc($result)){
				//Now check if the cart minimum rule applies
				$i = 0;
				$total = 0;
				while($cart['items'][$i]['itemCode']){
					$total = $total + ($cart['items'][$i]['itemPrice']*$cart['items'][$i]['qty']);
					$i++;
				}
					//setting status true if cart value is greater than min. of coupon
					if($total >= $coupon['minimum']){
						$status = true;
						$discount = $coupon['discount'];
					}
					else{
						$status = false;
						$error = "Minimum order value is ".$coupon['minimum'];
					}
	
	
			}
			else{
				$status = false;
				$error = "Coupon applicable only for first order";
			}
			
			break;
		}
	
	
		case "PERCENTAGE":{
	
			$i = 0;
			$total = 0;
			while($cart['items'][$i]['itemCode']){
				$total = $total + ($cart['items'][$i]['itemPrice']*$cart['items'][$i]['qty']);
				$i++;
			}
	
			if($total >= $coupon['minimumCart']){
				$status = true;
				$discount = round(($coupon['percentage']/100)*$total);
				if($discount >= $coupon['maximum'])
					$discount = $coupon['maximum'];
			}
			else{
				$status = false;
				$error = "Minimum order value is ".$coupon['minimumCart'];
			}
	
			break;
	
		}
	
	
		case "DISCOUNTEDCOMBO":{
			$total_discount = 0;
			$combocart = $cart;
			$mycoupon = $coupon;
	
			function getDiscount($coupon){
				global $combocart;
				$isValid = false;
	
				$i = 0;
				$terminate = false;
				while($combocart['items'][$i]['itemCode'] && !$terminate){
					// echo '<br><br><br>Case-'.$i.'<br>________________________';
					// echo '<br>Taking item '.$combocart['items'][$i]['itemCode'].' from CART';
					//Search if the item is present in coupon's required items
					$j = 0;
					while($coupon['items'][$j]['code']){
						// echo '<br>[COUPON]'.$coupon['items'][$j]['code'];
						if($combocart['items'][$i]['itemCode'] == $coupon['items'][$j]['code'])
						{
							// echo '<br>[COUPON] found matching!';
							if($combocart['items'][$i]['qty'] >= $coupon['items'][$j]['count'])
							{
								// echo '<br>[COUPON] Proceed';
								$combocart['items'][$i]['qty'] = $combocart['items'][$i]['qty'] - $coupon['items'][$j]['count'];
								$isValid = true;
							}
							else {
								// echo '<br>[COUPON] TERMINATE!!!!!!!';
								$terminate = true;
								$isValid = false;
								break;
							}
							break;
						}
						$j++;
					}
					$i++;
				}
	
				if($isValid){
					// echo '<br><br>*********************************************SUCCESS!*********************************************';
					return $coupon['discount'];
				}
				else {
					return 0;
				}
			}
	
			//Calculate the grand total of discounts
			while($disc = getDiscount($mycoupon))
			{
				$total_discount = $total_discount + $disc;
			}
	
	
			//Results
			if($total_discount > 0){
				$output = array(
					"status" => true,
					"discount" => $total_discount,
				);
			}
			else{
				$output = array(
					"status" => false,
					"error" => "Your cart does not contain required items."
				);
			}
	
			break;
		}
	}
}

return $discount;

}

?>

