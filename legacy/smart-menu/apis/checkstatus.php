<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

define('INCLUDE_CHECK', true);
require '../connect.php';

function errorResponse($error){
    $output = array(
		"status" => false,
		"error" => $error
	);
	die(json_encode($output));
}

$status = false;
$error = "";

$branch = "";
$qrcode = "";
$userMobile = "";
$tableNumber = "";
$peerCode = 0;

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

$masterBranchDetails = "";
if(isset($_GET['branchCode'])){
	$branch = $_GET['branchCode'];
	if($branchData = mysql_fetch_assoc(mysql_query("SELECT * from smart_branch_master WHERE branch = '{$branch}'"))){
	   $status = true;
	   $masterBranchDetails = $branchData;
	   if($branchData['enable_smart_order'] != 1){
	       errorResponse("Smart order not enabled at this branch");
	   }
	}
	else{
	    errorResponse("Invalid Branch");
	}
}
else{
    errorResponse("Branch Code missing");
}

if(isset($_GET['qrCodeReference'])){
	$qrcode = $_GET['qrCodeReference'];
}
else{
    errorResponse("QR Code reference missing");
}

if(isset($_GET['userMobile'])){
	$userMobile = $_GET['userMobile'];
}
else{
    errorResponse("User mobile number missing");
}

if(isset($_GET['tableNumber'])){
	$tableNumber = $_GET['tableNumber'];
}

if(isset($_GET['peerCode'])){
	$peerCode = $_GET['peerCode'];
}

$tableData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_table_mapping` WHERE `qr_code` ='{$qrcode}' AND is_active = 1 AND is_maintain_mode = 0"));

if($tableData['qr_code'] != $qrcode){
    errorResponse("This QR code is not active");
}

$masterMetaData = array(
	"branchCode" => $tableData['assigned_branch'],
	"qrCodeReference" => $qrcode,
	"type" => $tableData['assigned_type'],
	"tableNumber" => $tableData['assigned_table'],
	"mode" => "DINEIN",
	"modeCode" => "Dine In"
);

//Final Invoice Data
$final_sub_order_sum = 0;


$orderCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_master_orders` WHERE `qr_code_reference` = '{$qrcode}' AND `order_status` <= 1"));

if(!$orderCheck['master_order_id']){ //FREE
    $userData = array(
    	"name" => "",
    	"mobile" => "",
    	"email" => ""
    );
    
    $activeData = array(
    	"status" => "free",
    	"metaData" => $masterMetaData,
    	"userData" => $userData,
    	"cart" => []
    );
}
else{
    $current_status = $orderCheck['order_status'];
    $masterPeerCode = $orderCheck['peer_code'];
    
    $serverUser = $orderCheck['user_mobile'];
    $maskedNumber = getMaskedNumber($serverUser);
    if($userMobile != $serverUser){
        if($peerCode == 0){
            errorResponse("You can not order on this table. Another order already in progress from ".$maskedNumber);
        } else if($peerCode != $masterPeerCode){
            errorResponse("Incorrect peer code, get the 4 digit code from ".$maskedNumber);
        } else {
            $peerCodeData = array(
            	"maskedNumber" => $maskedNumber,
            	"peerCode" => $masterPeerCode
            );
            $masterMetaData['peerData'] = $peerCodeData;
        }
    } else {
        $peerCodeData = array(
        	"maskedNumber" => $maskedNumber,
        	"peerCode" => $masterPeerCode
        );
        $masterMetaData['peerData'] = $peerCodeData;
    }
    
    $userCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_users` WHERE `mobile` = '{$userMobile}' AND `is_blocked` = 0"));
    if($userCheck['is_blocked'] == 1){
        errorResponse("You are blocked. Reach out to us at hello@zaitoon.restaurant");
    }
    
    $userData = array(
    	"name" => $userCheck['name'],
    	"mobile" => $userCheck['mobile'],
    	"email" => $userCheck['email']
    );
    
    $effective_cart = [];
    $cancelled_cart = [];
    $sub_order = mysql_query("SELECT * FROM `smart_orders` WHERE `fk_master_order` = '{$orderCheck['master_order_id']}' AND `is_active` = 1 AND `status` IN (0, 1, 5)");
    while($individual_cart = mysql_fetch_assoc($sub_order)){
        $singleOrderCart = json_decode($individual_cart['cart'], true);
        $subOrderUserCheck = mysql_fetch_assoc(mysql_query("SELECT `name`, `mobile` FROM `smart_registered_users` WHERE `mobile` = '{$individual_cart['fk_peer_user_mobile']}'"));
        $subOrderUserName = "";
        $subOrderUserCode = "";
        if($subOrderUserCheck['name'] != ""){
            $subOrderUserName = $subOrderUserCheck['name'];
            $subOrderUserCode = $subOrderUserCheck['mobile'];
        }
        
        $final_sub_order_sum += $individual_cart['total_amount'];
        
        $formattedCart = [];
        foreach($singleOrderCart as $item) {
            $item['orderPersonLabel'] = $subOrderUserName;
            $item['orderPersonMobile'] = $subOrderUserCode;
            array_push($formattedCart, $item);
        }
        
        if($individual_cart['status'] == 5)
            $cancelled_cart = array_merge($cancelled_cart, $formattedCart);
        else if($individual_cart['status'] == 0 || $individual_cart['status'] == 1)
            $effective_cart = array_merge($effective_cart, $formattedCart);
    }
    
    if($current_status == 0){ //ORDER PLACED
        $activeData = array(
        	"status" => "active",
        	"metaData" => $masterMetaData,
        	"userData" => $userData,
        	"cart" => $effective_cart,
        	"cancelledCart" => $cancelled_cart,
        	"masterOrderId" => $orderCheck['master_order_id']
        );
    }
    else if($current_status == 1){ //TABLE BILLED
    
        if($orderCheck['system_bill_number'] == "" || $orderCheck['system_bill_number'] == null){
            errorResponse("Invoice was not generated successfully. Please check with the restaurant and pay by Cash or Card.");
        }
    
        //DUMMY DATA
        //$invoiceData = json_decode('{ "subTotal": 1200, "grandTotal": 1450, "discounts": { "type": "COUPON", "label": "New Year Discount", "amount": 1 }, "additionalCharges": { "taxSlabs": [{ "label": "SGST", "type": "PERCENTAGE", "value": 0.025, "amount": 50 }, { "label": "CGST", "type": "PERCENTAGE", "value": 0.025, "amount": 50 }], "otherCharges": [{ "label": "Delivery Charges", "type": "PERCENTAGE", "value": 0.07, "amount": 150 }] } }', true);
        //$paymentData = json_decode('{ "isOnlinePaymentAllowed": true, "amountToPay": 1450, "razorpayKey": "rzp_live_4NeEyLZf2m10Ry", "razorpayLabel": "Zaitoon Velachery", "razorpayDescription": "Invoice #1242", "paymentOrderId": "order_GoxM3ElPQYArG1" }', true);

        //Sample Discount Format (for future integration)
        $discountData = array(
            "type" => "COUPON",
            "label" => "New Year Discount",
            "amount" => 1
        );
        $discountData = null;
        
        
        $taxSlabsData = [];
        
        $taxSlabsData[] = array(
            "label" => "SGST",
            "type" => "PERCENTAGE",
            "value" => 0.025,
            "amount" => round($final_sub_order_sum * 0.025)
        );
        
        $otherChargesData = [];
        
        $otherChargesData[] = array(
            "label" => "CGST",
            "type" => "PERCENTAGE",
            "value" => 0.025,
            "amount" => round($final_sub_order_sum * 0.025)
        );
        
        $additionalCharges = array(
            "taxSlabs" => $taxSlabsData,
            "otherCharges" => $otherChargesData
        );
        
        $invoiceData = array(
        	"subTotal" => $final_sub_order_sum,
        	"grandTotal" => $orderCheck['total_bill_amount'],
        	"discounts" => $discountData,
        	"additionalCharges" => $additionalCharges
        );

        $paymentData = array(
        	"isOnlinePaymentAllowed" => $masterBranchDetails['is_payment_enabled'] == 1 ? true : false,
        	"amountToPay" => $orderCheck['total_bill_amount'],
        	"systemBillNumber" => $orderCheck['system_bill_number'],
        	"razorpayKey" => $masterBranchDetails['razorpay_key'],
        	"razorpayLabel" => "Zaitoon ".$masterBranchDetails['branch_name'],
        	"razorpayDescription" => "Invoice #".$orderCheck['system_bill_number'],
        	"paymentOrderId" => $orderCheck['razorpay_order_id']
        );

        $activeData = array(
        	"status" => "billed",
        	"metaData" => $masterMetaData,
        	"userData" => $userData,
        	"cart" => $effective_cart,
        	"invoiceDetails" => $invoiceData,
        	"paymentData" => $paymentData,
        	"masterOrderId" => $orderCheck['master_order_id']
        );
    }
}

$finalOutput = array(
    "status" => true,
    "data" => $activeData,
    "metaData" => $metaData
);

die(json_encode($finalOutput));

?>
