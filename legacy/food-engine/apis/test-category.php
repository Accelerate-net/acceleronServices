<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$fullCategory = [];

$category = mysql_query("SELECT DISTINCT(category) FROM test_menu WHERE 1");
while($mycategory = mysql_fetch_assoc($category)){
    array_push($fullCategory, $mycategory['category']);
}
   
echo json_encode($fullCategory);
		
?>