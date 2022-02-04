<?php
	if(!defined('INCLUDE_CHECK')) die('Warning: You are not authorized to access this page.');

	$db_host		= 'localhost';
	$db_user		= 'accelerate_admin';
	$db_pass		= 'Jafry@123';
	$db_database		= 'zaitoon'; 

	$link = mysql_connect($db_host, $db_user, $db_pass) or die('<h1 style="color:#FA5858; font-size:25px">SERVER ERROR</h1><p style="color:#fff">Connection to the Server can not be established at this moment. Check Later. Inconvenience is regretted.<br><a href="index.php" style="color:#F7FE2E"> Click Here to go to <strong>Home Page</strong></a></p>');
	mysql_select_db($db_database, $link);
?>