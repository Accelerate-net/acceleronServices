<?php

	$db_host		= 'localhost';
	$db_user		= 'accelerate_admin';
	$db_pass		= 'Jafry@123';
	$db_database		= 'zaitoon'; 



	$link = mysql_connect($db_host, $db_user, $db_pass) or die('failed to connect to db');
	mysql_select_db($db_database, $link);
	
	
	$name = $_GET['name'];
	$mobile = $_GET['mobile'];
	$email = $_GET['email'];
	
	$query = mysql_query("INSERT INTO `test` (`mobile`, `name`, `email`) VALUES ('{$mobile}', '{$name}', '{$email}')");
	

$output = array(
	"status" => true,
	"message" => "success"
	);
echo json_encode($output);	
	
?>