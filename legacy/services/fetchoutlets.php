<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Headers: X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

error_reporting(0);

$status = false;
$error = '';

if(isset($_GET['outletcode'])){

	if($rows1 = mysql_fetch_assoc(mysql_query("SELECT * from z_outlets WHERE code='{$_GET['outletcode']}'"))){
	$status = true;
	
	
	/* DELAY AND CLOSURE STATUS */
	
	
	//Outlet Open/Closed
	if($rows1['isOpen'] == 1){
		$closeMessage = "";
	}
	else{
		switch ($rows1['closeStatus']) {
		    case "RAIN":{
		        $closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") has been closed for online orders due to bad weather conditions.";
		        break;
		    }
		    case "RUSH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") is not accepting online orders due to unavailability of delivery boys.";
		    	break;
		    }
		    case "TECH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") is not accepting online orders due to technical issues.";
		    	break;
		    }
		    default:{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") is currently closed.";
		    	if($rows1['operationalHours'] != ''){
		    		$closeMessage = $closeMessage." Our operational hours is ".$rows1['operationalHours'];
		    	}

		    }		    		        
		}		
		
	}
	
	
	//Delivery Delayed or Not
	if($rows1['delayStatus'] == "NONE"){
		$delayed_status = false;
		$delayMessage = "";
	}
	else{
		$delayed_status = true; 
		
		switch ($rows1['delayStatus']) {
		    case "RAIN":{
		        $delayMessage = "Delivery might be delayed due to bad weather in your area. Sorry for the inconvenience.";
		        break;
		    }
		    case "RUSH":{
		    	$delayMessage = "Delivery might be delayed due to high volume of orders. Sorry for the inconvenience.";
		    	break;
		    }
		    default:{
		    	$delayMessage = "Delivery might be delayed. Sorry for the inconvenience.";
		    }		    		        
		}		
		
	}
	
	/* End */
	
	
	$delayMessage = "Unsupported App: You are currently using an old version of the App. Please update to the latest version (1.5) or you might not be able to place an Order from Zaitoon after 7th September, 2018.";
	
		
	//Get location specific details

	$rows2 = mysql_fetch_assoc(mysql_query("SELECT * from z_locations WHERE outlet='{$_GET['outletcode']}' AND locationCode='{$_GET['locationCode']}'"));

	$response = array(
		"outlet" => $rows1['code'],
		"isSpecial" => $rows1['isSpecial'] == 1? true: false,
		"city"  => $rows1['city'],
		"location"  => $rows2['location'],
		"locationCode"  => $rows2['locationCode'],
		"name"=> $rows1['name'],
		"line1"=> $rows1['line1'],
		"line2"=> $rows1['line2'],
		"mobile"=> $rows1['contact'],
		"lat"=> $rows1['latitude'],
		"lng"=> $rows1['longitude'],
		"openHours"=> $rows1['operationalHours'],
		"pictures" => json_decode($rows1['fotos']),
		"isTaxCollected" => $rows1['isTaxCollected'] == 1? true: false,
		"taxPercentage"=> $rows1['taxPercentage'],
		"isStateTaxCollected" => $rows1['isStateTaxCollected'] == 1? true: false,
		"stateTaxPercentage"=> $rows1['stateTaxPercentage'],	
		"isCentralTaxCollected" => $rows1['isCentralTaxCollected'] == 1? true: false,
		"centralTaxPercentage"=> $rows1['centralTaxPercentage'],				
		"isParcelCollected"=> $rows1['isParcelCollected'] == 1? true: false,
		"parcelPercentageDelivery"=> $rows1['parcelPercentageDelivery'],
		"parcelPercentagePickup"=> $rows1['parcelPercentagePickup'],
		"minTime"=> $rows2['minTime'],
		"minAmount"=> $rows2['minOrder'],
		"isAcceptingOnlinePayment"=> $rows1['isAcceptingOnlinePayment'] == 1? true: false,
		"razorpayID"=> $rows1['razorpayID'],
		"isReservationAllowed" => $rows1['isReservationAllowed'] == 1? true: false,
		"isOpen"=> true, //$rows1['isOpen'] == 1? true: false,		
		"isDelayed"=> true, //$delayed_status,
		"delayMessage"=> $delayMessage,
		"closureMessage"=> $closeMessage
	);
	}
	else{
		$status = false;
		$error = "Invalid Code";
	}

}
else if(isset($_GET['locationCode'])){

$outletInfo = mysql_fetch_assoc(mysql_query("SELECT * from z_locations WHERE locationCode='{$_GET['locationCode']}'"));
$outletSet = $outletInfo['outlet'];

//NOT SERVICABLE CASE
if($outletInfo['isServed'] == 0){
	$isServed = false;
	/* Outlet should not be Special (IIT Madras), Open Currently */
	$random_outlet = mysql_fetch_assoc(mysql_query("SELECT `code` FROM `z_outlets` WHERE `city` = '{$outletInfo['city']}' AND `isMasterOutlet` = 1 LIMIT 1"));
	$outletSet = $random_outlet['code'];
}
else{
	$isServed = true;
}



if($rows1 = mysql_fetch_assoc(mysql_query("SELECT * from z_outlets WHERE code='{$outletSet}'"))){
$status = true;

	/* DELAY AND CLOSURE STATUS */
	
	
	//Outlet Open/Closed
	if($rows1['isOpen'] == 1){
		$closeMessage = "";
	}
	else{
		switch ($rows1['closeStatus']) {
		    case "RAIN":{
		        $closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") has been closed for online orders due to bad weather conditions.";
		        break;
		    }
		    case "RUSH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") is not accepting online orders due to unavailability of delivery boys.";
		    	break;
		    }
		    case "TECH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") is not accepting online orders due to technical issues.";
		    	break;
		    }
		    default:{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$rows1['name'].") is currently closed.";
		    	if($rows1['operationalHours'] != ''){
		    		$closeMessage = $closeMessage." Our operational hours is ".$rows1['operationalHours'];
		    	}

		    }		    		        
		}		
		
	}
	
	
	//Delivery Delayed or Not
	if($rows1['delayStatus'] == "NONE"){
		$delayed_status = false;
		$delayMessage = "";
	}
	else{
		$delayed_status = true; 
		
		switch ($rows1['delayStatus']) {
		    case "RAIN":{
		        $delayMessage = "Delivery might be delayed due to bad weather in your area. Sorry for the inconvenience.";
		        break;
		    }
		    case "RUSH":{
		    	$delayMessage = "Delivery might be delayed due to high volume of orders. Sorry for the inconvenience.";
		    	break;
		    }
		    default:{
		    	$delayMessage = "Delivery might be delayed. Sorry for the inconvenience.";
		    }		    		        
		}		
		
	}
	
	/* End */
	
	$delayMessage = "Unsupported App: You are currently using an old version of the App. Please update to the latest version (1.5) or you might not be able to place an Order from Zaitoon after 7th September, 2018";

$response = array(
	"outlet" => $rows1['code'],
	"isSpecial" => $rows1['isSpecial'] == 1? true: false,
	"city"  => $rows1['city'],
	"location"  => $outletInfo['location'],
	"locationCode"  => $outletInfo['locationCode'],
	"name"=> $rows1['name'],
	"line1"=> $rows1['line1'],
	"line2"=> $rows1['line2'],
	"mobile"=> $rows1['contact'],
	"lat"=> $rows1['latitude'],
	"lng"=> $rows1['longitude'],
	"openHours"=> $rows1['operationalHours'],
	"pictures" => json_decode($rows1['fotos']),
	"isTaxCollected" => $rows1['isTaxCollected'] == 1? true: false,
	"taxPercentage"=> $rows1['taxPercentage'],
	"isStateTaxCollected" => $rows1['isStateTaxCollected'] == 1? true: false,
	"stateTaxPercentage"=> $rows1['stateTaxPercentage'],	
	"isCentralTaxCollected" => $rows1['isCentralTaxCollected'] == 1? true: false,
	"centralTaxPercentage"=> $rows1['centralTaxPercentage'],	
	"isParcelCollected"=> $rows1['isParcelCollected'] == 1? true: false,
	"parcelPercentageDelivery"=> $rows1['parcelPercentageDelivery'],
	"parcelPercentagePickup"=> $rows1['parcelPercentagePickup'],
	"minTime"=> $outletInfo['minTime'],
	"minAmount"=> $outletInfo['minOrder'],
	"isAcceptingOnlinePayment"=> $rows1['isAcceptingOnlinePayment'] == 1? true: false,
	"razorpayID"=> $rows1['razorpayID'],
	"isReservationAllowed" => $rows1['isReservationAllowed'] == 1? true: false,
	"isOpen"=> true, //$rows1['isOpen'] == 1? true: false,
	"isDelayed"=> true, //$delayed_status,
	"delayMessage"=> $delayMessage,
	"closureMessage"=> $closeMessage	

);
}

//Results
$output = array(
	"isServed" => $isServed,
	"response" => $response,
	"status" => $status,
	"error" => $error
	);
die(json_encode($output));

}
else{
$query = "SELECT DISTINCT city from z_outlets";
$main = mysql_query($query);

while($rows = mysql_fetch_assoc($main)){
	$query1 = "SELECT * from z_outlets WHERE city='{$rows['city']}'";
	$main1 = mysql_query($query1);
	$outlets = [];

	while($rows1 = mysql_fetch_assoc($main1)){
		$outlets[]=array(
			"name"=> $rows1['name'],
			"code"=> $rows1['code'],
			"line1"=> $rows1['line1'],
			"line2"=> $rows1['line2'],
			"isReservationAllowed" => $rows1['isReservationAllowed'] == 1? true: false,
			"mobile"=> $rows1['contact'],
			"lat"=> $rows1['latitude'],
			"lng"=> $rows1['longitude'],
			"pictures" => json_decode($rows1['fotos'])
		);
		$status = true;
	}

	$response[]= array(
		"city" => $rows['city'],
		"outlets" => $outlets
	);
}

}


//Final Results
$output = array(
	"response" => $response,
	"status" => $status,
	"error" => $error
	);
echo json_encode($output);
?>
