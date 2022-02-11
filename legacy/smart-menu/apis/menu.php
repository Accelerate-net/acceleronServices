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

function errorResponse($error){
    $output = array(
		"status" => false,
		"error" => $error
	);
	die(json_encode($output));
}

// Encryption Validation
if(!isset($_POST['token'])){
    errorResponse("Access Token is missing");
}

if(!isset($_POST['branchCode'])){
    errorResponse("Branch is missing");
}

$branch = $_POST['branchCode'];
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
//Check if the token is tampered
if($tokenid['mobile']){
	$userID = $tokenid['mobile'];
}
else{
    errorResponse("Token is invalid");
}


date_default_timezone_set('Asia/Calcutta');
$date = date("j F, Y");
$time = date("g:i a");


$status = false;
$error = "";


$outletData = [];

//Check branch details
if($branchData = mysql_fetch_assoc(mysql_query("SELECT * from smart_branch_master WHERE branch = '{$branch}'"))){
    
    $chargesList = array();
    $otherChargesQuery = mysql_query("SELECT * from smart_other_charges WHERE 1");
    while($otherCharge = mysql_fetch_assoc($otherChargesQuery)){
        $chargesList[$otherCharge['id']] = $otherCharge;
    }
    
    $billingModes = [];
    $branchModesQuery = mysql_query("SELECT * from smart_billing_modes WHERE fk_branch = '{$branch}' AND is_active = 1");
    while($branchMode = mysql_fetch_assoc($branchModesQuery)){
        
        $otherChargesInOrder = json_decode($branchMode['fk_other_charges'], true);
        
        $taxSlabsList = [];
        $otherChargesList = [];
        
        $m = 0;
        while($m < sizeOf($otherChargesInOrder)){
            
            $otherFound = $chargesList[$otherChargesInOrder[$m]];
            
            if($otherFound['category'] == "TAX"){ //TAX
                $taxSlabsList[] = array(
                    "label" => $otherFound['label'],
                    "type" => $otherFound['type'],
                    "value" => $otherFound['value'],
                    "maxCap" => $otherFound['max_cap']
                );
            }
            else{ //NON-TAX
                $otherChargesList[] = array(
                    "label" => $otherFound['label'],
                    "type" => $otherFound['type'],
                    "value" => $otherFound['value'],
                    "maxCap" => $otherFound['max_cap']
                );
            }
            
            $m++;
        }
        
        $billingModes[] = array(
            "id" => $branchMode['id'],
            "name" => $branchMode['name'],
            "type" => $branchMode['type'],
        	"taxSlabs" => $taxSlabsList,
        	"otherCharges" => $otherChargesList
        );
    }
    
    
    
    
    $openHoursData = [];
    $openHoursObject = json_decode($branchData['operational_hours'], true);
    for($i = 0; $i < sizeof($openHoursObject); $i++){
        $fromTime = date("g:i a", strtotime($openHoursObject[$i]['from']));
        $toTime = date("g:i a", strtotime($openHoursObject[$i]['to']));
        $openHoursData[] = array(
            "rank" => $i + 1,
            "label" => $openHoursObject[$i]['label'],
            "from" => $fromTime,
        	"to" => $toTime
        );
    }
    
    $outletData = array(
        "name" => $branchData['branch_name'],
        "outlet" => $branchData['branch'],
        "city" => $branchData['address_city'],
    	"locationCode" => $branchData['latitude']."_".$branchData['longitude'],
    	"latitude" => $branchData['latitude'],
    	"longitude" => $branchData['longitude'],
    	"addressLine1" => $branchData['address_line_1'],
    	"addressLine2" => $branchData['address_line_2'],
    	"branchContactNumber" => $branchData['contact_number'],
    	"guestRelationsEmail" => $branchData['contact_email'],
    	"managerName" => $branchData['manager_name'],
    	"managerContactNumber" => $branchData['manager_contact_number'],
    	"openHours" => $openHoursData,
    	"pictures" => $branchData['fotos'],
    	"isAcceptingOnlinePayment" => $branchData['is_payment_enabled'] == 1 ? true : false,
    	"razorpayID" => $branchData['razorpay_key'],
    	"isOpen" => $branchData['is_open'] == 1 ? true : false,
    	"isWarning" => false,
    	"warningMessage" => "",
    	"modes" => $billingModes
    );
}
else{
    errorResponse("Invalid Branch");
}


$menuData = [];

$categoryMainRank = 0;
$categoryMainQuery = "SELECT DISTINCT category_main FROM smart_menu_master WHERE 1 ORDER BY master_code";
$categoryMainQueryResult = mysql_query($categoryMainQuery);
while($categoryMainList = mysql_fetch_assoc($categoryMainQueryResult)) {
	$mainCategory = $categoryMainList['category_main'];
	$categoryMainRank++;
	$categorySubRank = 0;
	$subCategories = [];
	
	$categorySubQuery = "SELECT DISTINCT category_sub FROM smart_menu_master WHERE category_main = '{$mainCategory}'";
	$categorySubQueryResult = mysql_query($categorySubQuery);
	while($categorySubList = mysql_fetch_assoc($categorySubQueryResult)){
		$subCategory = $categorySubList['category_sub'];
        $categorySubRank++;
        $items = [];
        
		$itemQuery = "SELECT *, mb.is_available as itemAvailability FROM smart_menu_master as mm, smart_menu_branch as mb WHERE mm.category_main = '{$mainCategory}' AND mm.category_sub = '{$subCategory}' AND mb.fk_master_code = mm.master_code AND mb.branch_code = '{$branch}' AND mm.is_active = 1 AND mb.is_active = 1 ORDER BY mm.master_code";
	    $itemQueryResult = mysql_query($itemQuery);
		
		
		//Put all the items into an array.
		while($item = mysql_fetch_assoc($itemQueryResult)){
			$items[] = array(
				"code" => $item['master_code'],
				"name" => $item['name'],
				"brief" => $item['brief'],
				"price" => $item['price'],
				"serves" => $item['serves_pax'],
				"averagePreparationTime" => $item['preparation_time'],
				"isCustomisable" => $item['is_customisable'] == 1 ? true : false,
				"customOptions" => $item['is_customisable'] == 1 ? json_decode($item['customisation_options']) : "",
				"imageUrl" => $item['is_photo'] == 1 ? $item['photo_url'] : "",
				"isVeg" => $item['is_veg'] == 1 ? true : false,
				"isAvailable" => $item['itemAvailability'] == 1 ? true : false,
				"labels" => $item['labels'] != null ? json_decode($item['labels']) : []
			);
		}
		
		$subCategories[] = array(
		    "rank" => $categorySubRank,
		    "subCategoryName" => $subCategory,
		    "items" => $items
		);
	}
	
	$menuData[] = array(
	    "rank" => $categoryMainRank,
		"categoryName" => $mainCategory,
		"menu" => $subCategories
	);
}

$finalOutput = array(
    "status" => true,
    "outletData" => $outletData,
	"menuData" => $menuData
);

die(json_encode($finalOutput));

?>
