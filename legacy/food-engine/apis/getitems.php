<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//$token = mysql_real_escape_string($_POST['token']);


//validate token



$query = "SELECT * from zaitoon_menu WHERE 1";
$main = mysql_query($query);
$items = [];

while($rows = mysql_fetch_assoc($main)){
	$query1 = "SELECT * from zaitoon_menutypes WHERE subType='{$rows['type']}'";
	$main1 = mysql_query($query1);
	$rows1 = mysql_fetch_assoc($main1);
	$items[]=array(
		"itemCode" => $rows['code'],
	    "itemName" => $rows['name'],
	    "itemVariety" => $rows1['subName'],
	    "itemPrice" => $rows['price']
		);
}
//$list = array('status' => $flag);    
echo json_encode($items);
		
?>