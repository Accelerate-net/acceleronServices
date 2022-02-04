<?php
error_reporting(0);

//Job to update Member Class of User - every day midnight

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

date_default_timezone_set('Asia/Calcutta');
$today = date("d");
$onemonth = date('Ymd', strtotime(' -30 day'));

$query = mysql_query("SELECT `mobile`, `memberType` FROM `z_users` WHERE `isBlocked`=0 AND `isRewardEnabled`=1 AND `syncDay`!='{$today}'");

while($allList = mysql_fetch_assoc($query)){

	$plus = 0;
	$plusQuery = mysql_fetch_assoc(mysql_query("SELECT SUM(coins) AS plusPoints FROM `z_rewards` WHERE `userID`='{$allList['mobile']}' AND `isApproved` = 1 AND `isCredit` = 1 AND `time`>'{$onemonth}'"));
	if($plusQuery['plusPoints']) {$plus = $plusQuery['plusPoints'];}
	
	$minus = 0;
	$minusQuery = mysql_fetch_assoc(mysql_query("SELECT SUM(coins) AS minusPoints FROM `z_rewards` WHERE `userID`='{$allList['mobile']}' AND `isApproved` = 1 AND `isCredit` = 0 AND `time`>'{$onemonth}'"));
	if($minusQuery['minusPoints']) {$minus = $minusQuery['minusPoints'];}
	
	$schemeCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE `coinsVolume`<='{$plus}' ORDER BY `index` DESC LIMIT 1"));
	
	//Update only if Class has changed
	if($allList['memberType'] != $schemeCheck['class']){
		mysql_query("UPDATE `z_users` SET `memberType`='{$schemeCheck['class']}', `syncDay`='{$today}' WHERE `mobile` ='{$allList['mobile']}'");
	}
	
}


?>