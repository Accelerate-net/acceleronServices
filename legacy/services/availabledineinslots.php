<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

if(!isset($_GET['outlet'])){
	$output = array(
		"response" => "",
		"status" => false,
		"error" => 'Outlet Code not set'
	);
	   
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');
$now = date("Hi");

$status = false;
$error = "No slots available. Please opt for Takeaway.";


$query = "SELECT `openFrom`, `openTo` FROM `z_outlets` WHERE `code`='{$_GET['outlet']}' AND `isPrepaidDineInAllowed`=1";
$main = mysql_fetch_assoc(mysql_query($query));

if($main['openFrom'] != ''){

	$open_from = $main['openFrom'];
	$open_to = strtotime($main['openTo']) - 1800; //30 mins prior to closing time
	
	
	if(strtotime($now) >= strtotime($open_from)){
		$slot = $now;
	}
	else{
		$slot = $open_from;
		$diff = strtotime($open_from) - strtotime($now);
		
		echo $diff;
		
		//greater than the 1 hour window - time less than 11 AM
		if($diff > 60){ 
			$response [] = array(
				"name" => date("h:i a",  $open_from),
				"slot" => date("Hi",  $open_from)
			);
			
			$output = array(
				"response" => $response,
				"status" => true,
				"error" => "Only 1 Slot"
			);
			   
			die(json_encode($output));			
		}
	}
	
	$slot = strtotime($slot);
	
	while($slot <= $open_to){
		
		$response [] = array(
			"name" => date("h:i a",  $slot),
			"slot" => date("Hi",  $slot)
		);
		
		$slot = strtotime('+ 10 minutes', $slot); 
		
		$status = true;
		$error = "";
		
	}
}
else{
	$output = array(
		"response" => "",
		"status" => false,
		"error" => 'Feature is not available'
	);
	   
	die(json_encode($output));
}

	$output = array(
		"response" => $response,
		"status" => $status,
		"error" => ""
	);
	   
	echo json_encode($output);

		
?>

