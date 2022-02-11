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
	$role = " AND (role = '{$_GET['role']}' OR role = 'AGENT')";
}
else{
	$role = "";
}


$query2 = "";
if($_GET['role'] == 'AGENT'){
	$query = "SELECT * FROM `z_deliveryagents` WHERE 1 ".$branch.$role;
	$query2 = "SELECT * FROM `smart_registered_stewards` WHERE 1 ".$branch;
}
else if($_GET['role'] == 'CAPTAIN'){
    $query = "SELECT * FROM `smart_registered_stewards` WHERE 1 ".$branch;
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
		'role' => $role['role'] && $role['role'] != null ? $role['role'] : $_GET['role']
		);
}

if($query2 != ""){
    $all = mysql_query($query2);
    while($role = mysql_fetch_assoc($all))
    {
    	$list [] = array(
    		'code' => $role['code'], 
    		'name' => $role['name'],
    		'role' => "CAPTAIN"
    		);
    }  
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