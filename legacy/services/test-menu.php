<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$fullMenu = [];

$category = mysql_query("SELECT DISTINCT(category) FROM test_menu WHERE 1");
while($mycategory = mysql_fetch_assoc($category)){

    $items = mysql_query("SELECT * FROM test_menu WHERE category = '{$mycategory['category']}'");
    
    $itemsList = [];
    
    while($myitem = mysql_fetch_assoc($items)){
    
        $found_item = array(
              "name" => $myitem['name'],
              "price" => $myitem['price'], 
              "code" => $myitem['id'], 
              "vegFlag" => "0",
              "isCustom" =>  $myitem['is_custom'] == 1 ? true : false,
              "customOptions" => $myitem['is_custom'] == 1 ? json_decode($myitem['customString']) : [],
              "isAvailable" => true,
              "isPackaged" => false, 
              "shortCode" => $myitem['code_text'], 
              "shortNumber" => $myitem['code_number']
        );
        
        array_push($itemsList, $found_item);
    }

    $menuSection = array(
        "category" => $mycategory['category'],
        "items" => $itemsList
    );
        
    array_push($fullMenu, $menuSection);
}
   
echo json_encode($fullMenu);
		
?>