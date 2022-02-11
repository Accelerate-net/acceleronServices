<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$status = false;

if(isset($_GET['outlet'])){
	$check = mysql_fetch_assoc(mysql_query("SELECT isOpen, name, closeStatus, delayStatus, operationalHours FROM z_outlets WHERE code='{$_GET['outlet']}'"));
	
	
	//Outlet Open/Closed
	if($check['isOpen'] == 1){
		$status = true;
		$closeMessage = "";
	}
	else{
		switch ($check['closeStatus']) {
		    case "RAIN":{
		        $closeMessage = "The nearest outlet to you (Zaitoon, ".$check['name'].") has been closed for online orders due to bad weather conditions.";
		        break;
		    }
		    case "RUSH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$check['name'].") is not accepting online orders due to unavailability of delivery boys.";
		    	break;
		    }
		    case "TECH":{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$check['name'].") is not accepting online orders due to technical issues.";
		    	break;
		    }
		    default:{
		    	$closeMessage = "The nearest outlet to you (Zaitoon, ".$check['name'].") is currently closed.";
		    	if($check['operationalHours'] != ''){
		    		$closeMessage = $closeMessage." Our operational hours is ".$check['operationalHours'];
		    	}

		    }		    		        
		}		
		
	}
	
	
	//Delivery Delayed or Not
	if($check['delayStatus'] == "NONE"){
		$delayed_status = false;
		$delayMessage = "";
	}
	else{
		$delayed_status = true; 
		
		switch ($check['delayStatus']) {
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
}

	/*
	if(!$delayed_status){
		$delayMessage = "Warning! As we have recently upgraded our servers. Please force refresh the window (Ctrl + Shift + R on browsers) before you place an order. If you want to place an order at IIT Madras outlet, specifically choose IIT Madras (Chennai) option from the Popular Areas while setting the location. Please note that, IIT Madras outlet is a special outlet and the system will not choose IIT Madras outlet if you are manually entering the location.";	
	}
	$delayed_status = true;
	*/
	

	$output = array(
		"status" => $status,
		"message" => $closeMessage,
		"isDelay" => $delayed_status,
		"delayMessage" => $delayMessage
	);

echo json_encode($output);

?>
