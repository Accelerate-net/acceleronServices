<?php
error_reporting(0);

//Job to update Coupon Usage - every 30 mins

//Also, Inactivate Coupon if limit crossed

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';


$query = mysql_query("SELECT `code`, `limit` FROM `z_couponrules` WHERE `isActive`=1");

while($allList = mysql_fetch_assoc($query)){

	$total_use_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`userID`) as total FROM `zaitoon_orderlist` WHERE `isVerified`=1 AND `status` != 5 AND `usedVoucher`='{$allList['code']}'"));
	
	if($allList['limit'] < $total_use_check['total']){ //Inactivate : Limit Reached
		mysql_query("UPDATE `z_couponrules` SET `usage`='{$total_use_check['total']}' WHERE `code`='{$allList['code']}' AND `isActive`=1");
	}
	else{
		mysql_query("UPDATE `z_couponrules` SET `usage`='{$total_use_check['total']}', `isActive`=0 WHERE `code`='{$allList['code']}'");
	}
}


?>