<?php
define('INCLUDE_CHECK', true);
require '../connect.php';

die('REMOVE DIE FLAG');
/* TO Migrate Comments from the Feedback to separate col in DB */

$q1 = mysql_query("SELECT * FROM `zaitoon_orderlist` WHERE `feedback`!='NA'");
while($s1 = mysql_fetch_assoc($q1)){
	$review = json_decode($s1['feedback']);
	mysql_query("UPDATE `zaitoon_orderlist` SET `feedComments`='{$review->comment}' WHERE `orderID`='{$s1['orderID']}'"); 
	
	echo'<br>'."UPDATE `zaitoon_orderlist` SET `feedComments`='".$review->rating."' WHERE `orderID`='{$s1['orderID']}'";
}
?>
