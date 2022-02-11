<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

$status = false;
$error = 'Unable to find an outlet';
$isServed = false;

// Get parameters from URL
$center_lat = mysql_real_escape_string($_GET["lat"]);
$center_lng = mysql_real_escape_string($_GET["lng"]);
$radius = 10; //Distance in KMs

if($center_lat == '12.98534830000001' && $center_lng == '80.23629019999998'){
	$query = "SELECT * FROM `z_outlets` WHERE `code`='IITMADRAS'";
}
else{
	// Search the rows in the markers table
	$query = "SELECT *, ( 3959 * acos( cos( radians('{$center_lat}') ) *  cos( radians( latitude ) ) * cos( radians( longitude ) - radians('{$center_lng}') ) + sin( radians('{$center_lat}') ) * sin( radians( latitude ) ) ) ) AS distance FROM z_outlets WHERE isSpecial = 0 HAVING distance < '{$radius}' ORDER BY distance LIMIT 1";
}

$result = mysql_query($query);

if($info = mysql_fetch_assoc($result)){ //Servicing area

	$isServed = true;

	$status = true;
	$error = '';
	

	/* DELAY AND CLOSURE STATUS */
	
	//Outlet Open/Closed
	if($info['isOpen'] == 1){
		$closeMessage = "";
	}
	else{
		switch ($info['closeStatus']) {
		    case "RAIN":{
		        $closeMessage = "The nearest outlet to you (Zaitoon, ".$info['name'].") has been closed for online orders due to bad weather conditions.";
		        break;
		    }
		    case "RUSH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$info['name'].") is not accepting online orders due to unavailability of delivery boys.";
		    	break;
		    }
		    case "TECH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$info['name'].") is not accepting online orders due to technical issues.";
		    	break;
		    }
		    default:{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$info['name'].") is currently closed.";
		    	if($info['operationalHours'] != ''){
		    		$closeMessage = $closeMessage." Our operational hours is ".$info['operationalHours'];
		    	}

		    }		    		        
		}		
		
	}
	
	
	//Delivery Delayed or Not
	if($info['delayStatus'] == "NONE"){
		$delayed_status = false;
		$delayMessage = "";
	}
	else{
		$delayed_status = true; 
		
		switch ($info['delayStatus']) {
		    case "RAIN":{
		        $delayMessage = "Delivery from the nearest outlet to you (Zaitoon, ".$info['name'].") might be delayed due to bad weather in your area. Sorry for the inconvenience.";
		        break;
		    }
		    case "RUSH":{
		    	$delayMessage = "Delivery from the nearest outlet to you (Zaitoon, ".$info['name'].") might be delayed due to high volume of orders. Sorry for the inconvenience.";
		    	break;
		    }
		    default:{
		    	$delayMessage = "Delivery from the nearest outlet to you (Zaitoon, ".$info['name'].") might be delayed. Sorry for the inconvenience.";
		    }		    		        
		}		
		
	}
	
	/* End */
	
	
	$response = array(
		"outlet" => $info['code'],
		"isSpecial" => $info['isSpecial'] == 1? true: false,
		"city"  => $info['city'],
		"location"  => '',
		"locationCode"  => $center_lat.'_'.$center_lng,
		"name"=> $info['name'],
		"line1"=> $info['line1'],
		"line2"=> $info['line2'],
		"mobile"=> $info['contact'],
		"lat"=> $info['latitude'],
		"lng"=> $info['longitude'],
		"openHours"=> $info['operationalHours'],
		"pictures" => json_decode($info['fotos']),
		"isTaxCollected" => $info['isTaxCollected'] == 1? true: false,
		"taxPercentage"=> $info['taxPercentage'],
		"isStateTaxCollected" => $info['isStateTaxCollected'] == 1? true: false,
		"stateTaxPercentage"=> $info['stateTaxPercentage'],	
		"isCentralTaxCollected" => $info['isCentralTaxCollected'] == 1? true: false,
		"centralTaxPercentage"=> $info['centralTaxPercentage'],				
		"isParcelCollected"=> $info['isParcelCollected'] == 1? true: false,
		"parcelPercentageDelivery"=> $info['parcelPercentageDelivery'],
		"parcelPercentagePickup"=> $info['parcelPercentagePickup'],
		"minTime"=> '50',
		"minAmount"=> 150,
		"isAcceptingOnlinePayment"=> $info['isAcceptingOnlinePayment'] == 1? true: false,
		"razorpayID"=> $info['razorpayID'],
		"isReservationAllowed" => $info['isReservationAllowed'] == 1? true: false,
		"isOpen"=> $info['isOpen'] == 1? true: false,		
		"isDelayed"=> $delayed_status,
		"delayMessage"=> $delayMessage,
		"closureMessage"=> $closeMessage
	);
	
}
else{ //Not servicing area

	$isServed = false;
	
	/* Outlet should not be Special (IIT Madras), Open Currently */
	$random_outlet = mysql_fetch_assoc(mysql_query("SELECT `code` FROM `z_outlets` WHERE `isMasterOutlet` = 1 LIMIT 1"));
	$outletSet = $random_outlet['code'];
	
	$info = mysql_fetch_assoc(mysql_query("SELECT * from z_outlets WHERE code='{$outletSet}'"));


	$status = true;
	$error = '';
	

	/* DELAY AND CLOSURE STATUS */
	
	//Outlet Open/Closed
	if($info['isOpen'] == 1){
		$closeMessage = "";
	}
	else{
		$closeMessage = "Some of the Zaitoon outlets near to you are closed for online orders currently.";
	}
	
	
	//Delivery Delayed or Not
	if($info['delayStatus'] == "NONE"){
		$delayed_status = false;
		$delayMessage = "";
	}
	else{
		$delayed_status = true; 
		$delayMessage = "Delivery from some of the Zaitoon outlet near to you is be delayed due to unavoidable circumstances.";	
		
	}
	
	/* End */
	
	
	$response = array(
		"outlet" => $info['code'],
		"isSpecial" => $info['isSpecial'] == 1? true: false,
		"city"  => $info['city'],
		"location"  => '',
		"locationCode"  => $center_lat.'_'.$center_lng,
		"name"=> $info['name'],
		"line1"=> $info['line1'],
		"line2"=> $info['line2'],
		"mobile"=> $info['contact'],
		"lat"=> $info['latitude'],
		"lng"=> $info['longitude'],
		"openHours"=> $info['operationalHours'],
		"pictures" => json_decode($info['fotos']),
		"isTaxCollected" => $info['isTaxCollected'] == 1? true: false,
		"taxPercentage"=> $info['taxPercentage'],
		"isStateTaxCollected" => $info['isStateTaxCollected'] == 1? true: false,
		"stateTaxPercentage"=> $info['stateTaxPercentage'],	
		"isCentralTaxCollected" => $info['isCentralTaxCollected'] == 1? true: false,
		"centralTaxPercentage"=> $info['centralTaxPercentage'],				
		"isParcelCollected"=> $info['isParcelCollected'] == 1? true: false,
		"parcelPercentageDelivery"=> $info['parcelPercentageDelivery'],
		"parcelPercentagePickup"=> $info['parcelPercentagePickup'],
		"minTime"=> '50',
		"minAmount"=> 150,
		"isAcceptingOnlinePayment"=> $info['isAcceptingOnlinePayment'] == 1? true: false,
		"razorpayID"=> $info['razorpayID'],
		"isReservationAllowed" => $info['isReservationAllowed'] == 1? true: false,
		"isOpen"=> $info['isOpen'] == 1? true: false,		
		"isDelayed"=> $delayed_status,
		"delayMessage"=> $delayMessage,
		"closureMessage"=> $closeMessage
	);
	
}


//Final Results
$output = array(
	"isServed" => $isServed,
	"response" => $response,
	"status" => $status,
	"error" => $error
);
echo json_encode($output);
?>