<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';


$query = "SELECT * FROM `item_test` WHERE 1";
$all = mysql_query($query);
while($order = mysql_fetch_assoc($all)){
    
    $mappedName = trim($order['name']);
    
    if($order['customisation'] != ""){
        $mappedName = $mappedName." [".$order['customisation']."]";
    }
    
    
    
	$list[] = array(
		'mappedCode' => "", 
		'mappedName' => $mappedName,
		'mappedVariant' => "", 
		'mappedPrice' => (int)$order['price'], 
		'systemCode' => $order['code'],
		'systemVariant' => $order['variant']                            
	);
}


echo json_encode($list);
		
?>