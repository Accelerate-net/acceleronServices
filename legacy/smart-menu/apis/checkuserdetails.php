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

$userData = "";
$mobile = "";
if(isset($_GET['mobile'])){
	$mobile = $_GET['mobile'];
	
	$userCheck = mysql_fetch_assoc(mysql_query("SELECT * from smart_registered_users WHERE mobile='{$mobile}'"));
    if($userCheck['is_blocked'] == 1){
	    errorResponse("User is blocked by Zaitoon. Please contact hello@zaitoon.restaurant");
    }
    else if($userCheck['mobile'] == $mobile) {
        $userData = array(
            "name" => $userCheck['name'],
            "mobile" => $userCheck['mobile']
        );
        
        $finalOutput = array(
            "status" => true,
            "data" => $userData
        );
        
        die(json_encode($finalOutput));
    }
    else{
        errorResponse("No record found");
    }
}
else{
    errorResponse("Mobile number missing");
}
?>
