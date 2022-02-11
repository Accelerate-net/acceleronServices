<?php
define('INCLUDE_CHECK', true);
require '../connect.php';

die('REMOVE DIE FLAG');
/* TO Migrate Address from the Orders as Saved Address */

$users = mysql_query("SELECT `mobile` FROM `z_users` WHERE `savedAddresses` = '[]'");

$n = 0;
while($user = mysql_fetch_assoc($users)){

	$lastAddress = mysql_fetch_assoc(mysql_query("SELECT `deliveryAddress` FROM `zaitoon_orderlist` WHERE `isVerified`=1 AND `userID`='{$user['mobile']}'  AND `isTakeaway`=0 ORDER BY `orderID` DESC LIMIT 1"));
	$newAddress = "";	
	
	if($lastAddress){

		$originalAddress = json_decode($lastAddress['deliveryAddress']);
	
		$newAddress[] = array(
			"id" => 1,
			"isDefault" => null,
			"name" => $originalAddress->name,
			"flatNo" => $originalAddress->flatNo,
			"flatName" => $originalAddress->flatName,
			"landmark" => $originalAddress->landmark,
			"area" => $originalAddress->area,
			"contact" => $originalAddress->contact
		);
		
		$save = json_encode($newAddress);		
			
		mysql_query("UPDATE `z_users` SET `savedAddresses` = '{$save}' WHERE `mobile`='{$user['mobile']}'");
	
	}
}


?>

