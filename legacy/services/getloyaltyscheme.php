<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$query= mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE 1 ORDER BY `index`");
$schemesList = [];

while($myscheme = mysql_fetch_assoc($query)){

	$schemesList[] = array(
		"type" => $myscheme['class'],
		"brief" => $myscheme['brief'],
		"coinsNeeded" => $myscheme['coinsVolume'],
		"rewards" => $myscheme['rewardsPerSlab'],
		"redeemLimit" => $myscheme['coinsRedeemableLimit']
	);
}
		
echo json_encode($schemesList);
			
?>