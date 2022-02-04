<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

if(isset($_GET['outlet']) && $_GET['outlet'] != ""){
	$filter = "AND branch = '{$_GET['outlet']}'";
}
else{
	$filter = "";
}


$rows = mysql_fetch_assoc(mysql_query("SELECT * FROM zatar_customers_master WHERE flag = 0 AND mobile != '' ".$filter." LIMIT 1"));
if(!$rows['mobile'] || $rows['mobile'] == null || $rows['mobile'] == ""){
	die("ERROR!");
}

$address = array(
      "id" => 1,
      "name" => $rows['name'] && $rows['name'] != null ? $rows['name'] : "",
      "flatNo" => "",
      "flatName" => $rows['address'] && $rows['address'] != null ? $rows['address'] : "",
      "landmark" => $rows['landmark'] && $rows['landmark'] != null ? $rows['landmark'] : "",
      "area" => "",
      "contact" => $rows['mobile']
);

$addressList = [$address];

$output = array(
  "_id" => $rows['mobile'],
  "name" => $rows['name'] && $rows['name'] != null ? $rows['name'] : "",
  "mobile" => $rows['mobile'],
  "branch" => $rows['branch'],
  "savedAddresses" => $addressList
); 
   
mysql_query("UPDATE `zatar_customers_master` SET `flag`= 1 WHERE `mobile` = '{$rows['mobile']}'");    
echo json_encode($output);
		
?>