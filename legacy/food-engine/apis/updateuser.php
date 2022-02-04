<?php

/*** YET TO ADD "NO RESULTS" SCENARIO ***/

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$name = $_GET['name'];//VALIDATION pending
$email = $_GET['email'];
$id = $_GET['id'];

$query = "UPDATE z_users SET name = '{$name}', email = '{$email}' WHERE mobile='{$id}'";


mysql_query($query);

$msg = array(
	'status' => 'success'
);

echo json_encode($msg);
		
?>