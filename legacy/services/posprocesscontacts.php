<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';


$rows = mysql_query("SELECT * FROM `TABLE 48` WHERE `flag` = 0");

while($t = mysql_fetch_assoc($rows)){
	$add = $t['l1'].', '.$t['l2'].', '.$t['l3'].', '.$t['l4'].', '.$t['l5'].', '.$t['l6'].', '.$t['l7'];
	
	mysql_query("UPDATE `TABLE 48` SET `address`= '{$add}', `flag` = 1 WHERE `id` = '{$t['id']}'");    
	
	echo '<br>'.$add ;
}
		
?>