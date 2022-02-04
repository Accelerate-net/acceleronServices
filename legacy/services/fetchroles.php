<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_GET['branch'])){
	$branch = " AND branch = '{$_GET['branch']}'";
}
else
{
	$branch = "";
}

if(isset($_GET['role'])){
	$role = " AND role = '{$_GET['role']}'";
}
else{
	$role = "";
}


if($_GET['role'] == 'AGENT'){
	$query = "SELECT * FROM `z_deliveryagents` WHERE 1 ".$branch.$role;
}
else{
	$query = "SELECT * FROM `z_roles` WHERE 1 ".$branch.$role;
}


$all = mysql_query($query);

$list = [];

while($role = mysql_fetch_assoc($all))
{
	$list [] = array(
		'code' => $role['code'], 
		'name' => $role['name'],
		'role' => $role['role']
		);
}

if(!$list){
	$output = array(
		'isFound' => false
	);
}
else
{
	$output = array(
		'isFound' => true,
		'results' => $list
		);
}

echo json_encode($output);
		
?>