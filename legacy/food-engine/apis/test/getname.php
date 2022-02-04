<?php

	$db_host		= 'localhost';
	$db_user		= 'accelerate_admin';
	$db_pass		= 'Jafry@123';
	$db_database		= 'zaitoon'; 



	$link = mysql_connect($db_host, $db_user, $db_pass) or die('failed to connect to db');
	mysql_select_db($db_database, $link);
	
	
	$name = $_GET['name'];
	
	$query = mysql_query("select name, email from test where name like '{$name}%'");
	
	$status = false;
	
	while($queryResult = mysql_fetch_assoc($query)){
	    $status = true;
	    $person[] = array(
		    "name" => $queryResult['name'],
		    "email" => $queryResult['email']);
	}

$output = array(
	"data" => $status ? $person : [],
	"status" => $status,
	"message" => $status ? "results found" : "no results"
	);
echo json_encode($output);	
	
?>