<?php

define('INCLUDE_CHECK', true);
require '../connect.php';


//SMS Template
$xmlData = '
<SMS>
<Account Name="support@accelerate.net.in" Password="Abhijith*003" Test="1" Info="1" JSON="0">
<Sender From="KKADAI">
<Messages>
';

$query = mysql_query("SELECT * FROM `z_desk_specialCoupons` WHERE `isNotified` = 0  AND `mobile`='9043960876' LIMIT 200");
while($row = mysql_fetch_assoc($query)){
	$msg = "Welcome New Year Offer from Kopper Kadai, just for You! Use voucher ".$row['code']." and get 10% OFF on dine in. Reserve table now on www.kopperkadai.com";
	$xmlData = $xmlData.'<Msg Number="'.$row['mobile'].'"><Text>'.$msg.'</Text></Msg>';
	
	mysql_query("UPDATE `z_desk_specialCoupons` SET `isNotified`=1 WHERE `mobile`='{$row['mobile']}'");
}

//Send SMS here:
$xmlData = $xmlData.'
</Messages>
</Sender>
</Account>
</SMS>'; 


$post = 'data='. urlencode($xmlData); 
$url = "http://api.textlocal.in/xmlapi.php";
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_POST ,1); 
curl_setopt($ch, CURLOPT_POSTFIELDS ,$post); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
$data = curl_exec($ch); 
curl_close($ch);

?>