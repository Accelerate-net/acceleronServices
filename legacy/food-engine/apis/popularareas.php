<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

error_reporting(0);

$city = $_GET['city'];

if($city == 'Chennai'){
		$cities = array(
		
			array(
			        "city" => "Chennai",		
				"code"  => 300,
				"name"  => "IIT Madras",
				"originalName"  => "IIT Madras"
			),
			array(
			        "city" => "Chennai",		
				"code"  => 200,
				"name"  => "Bessy",
				"originalName"  => "Bessy"
			)
	
		);
		
}
else if($city == 'Bangalore'){
		$cities = array(
			array(
			        "city" => "Bangalore",		
				"code"  => 1300,
				"name"  => "HAL Road",
				"originalName" => "HAL Road"
			)
		);
		
}
else{
		$cities = array(
			array(
			        "city" => "Chennai",		
				"code"  => "12.98534830000001_80.23629019999998",
				"name"  => "IIT Madras (Chennai)",
				"originalName"  => "IIT Madras Campus"
			)
		);
}


//Final Results
$output = array(
	"response" => $cities,
	"status" => true,
	"error" => ""
	);
echo json_encode($output);
?>
