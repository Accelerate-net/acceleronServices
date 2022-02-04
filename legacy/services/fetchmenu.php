<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);


$special_menu = "z_menu";

if(isset($_POST['outlet'])){
	
	$special_menu = "z_menu";

	$check = mysql_fetch_assoc(mysql_query("SELECT code, isSpecial FROM z_outlets WHERE code='{$_POST['outlet']}'"));
	if($check['isSpecial'] == 1){
		//Special Menu for IIT Madras etc
		$special_menu = "z_menu_".$check['code'];
	}	
	
	if($check['code']){
		$outlet = $check['code'];
	}
	else{
		$outlet = "VELACHERY";
	}
}
else{
	$outlet = "VELACHERY";
}


//validate token

$type = $_POST['cuisine'];

$filterFlag = $_POST['isFilter'];


if($filterFlag)
{

$sampleFilter = json_decode($_POST['filter'], true);

//Sample Filter Object

// $vegtype = array(
// 		"showVeg" => true,
// 		"showNonVeg" => true
// 	);
// $contains = array(
// 		"skip" => false,
// 		"chicken" => true,
// 		"mutton" => false,
// 		"fish" => false,
// 		"prawns" => false,
// 		"egg" => false
// 	);
// $avoid = array(
// 		"chicken" => false,
// 		"mutton" => false,
// 		"fish" => false,
// 		"prawns" => false,
// 		"egg" => false
// 	);
// $spicelevel = array(
// 		"skip" => true,
// 		"spicy" => true,
// 		"sweetened" => false,
// 		"non" => false
// 	);
// $frytype = array(
// 		"skip" => true,
// 		"oilfry" => true,
// 		"tawafry" => false
// 	);
// $cookingtype = array(
// 		"skip" => true,
// 		"gravy" => true,
// 		"semi" => false,
// 		"dry" => false,
// 		"deep" => false
// 	);

// $boneless = array(
// 		"skip" => false,
// 		"bone" => false,
// 		"boneless" => true
// 	);

// $sampleFilter = array(
// 				"vegtype" => $vegtype,
// 				"contains" => $contains,
// 				"avoid" => $avoid,
// 				"spicelevel" => $spicelevel,
// 				"frytype" => $frytype,
// 				"cookingtype" => $cookingtype,
// 				"boneless" => $boneless
// 			);


//Filter 1 : VEG or NON VEG
if($sampleFilter['vegtype']['showNonVeg'] && $sampleFilter['vegtype']['showVeg'])
	$vegtypeFilter = "";
else if($sampleFilter['vegtype']['showNonVeg'])
	$vegtypeFilter = "AND isVeg='0'";	
else if($sampleFilter['vegtype']['showVeg'])
	$vegtypeFilter = "AND isVeg='1'";


//Filter 2 & 3 : CONTAINS & AVOID
$containsFilterCount = 0;
$containsFilter = "";

if(!$sampleFilter['contains']['skip']){
	if($sampleFilter['contains']['chicken']){
		$containsFilter = $containsFilter."1";
		$containsFilterCount++;
	}
	if($sampleFilter['contains']['mutton']){
		if(!$containsFilterCount)
			$containsFilter = "2";
		else
			$containsFilter = $containsFilter.", 2";
		$containsFilterCount++;
	}
	if($sampleFilter['contains']['fish']){
		if(!$containsFilterCount)
			$containsFilter = "3";
		else
			$containsFilter = $containsFilter.", 3";
		$containsFilterCount++;
	}
	if($sampleFilter['contains']['prawns']){
		if(!$containsFilterCount)
			$containsFilter = "4";
		else
			$containsFilter = $containsFilter.", 4";
		$containsFilterCount++;
	}
	if($sampleFilter['contains']['egg']){
		if(!$containsFilterCount)
			$containsFilter = "5";
		else
			$containsFilter = $containsFilter.", 5";
		$containsFilterCount++;
	}

	//Non Veg Mix Case
	if($sampleFilter['contains']['chicken'] || $sampleFilter['contains']['mutton'] || $sampleFilter['contains']['egg'])
	{
		if(!$containsFilterCount)
			$containsFilter = "6";
		else
			$containsFilter = $containsFilter.", 6";
		$containsFilterCount++;
	}

	//Sea Food Mix Case
	if($sampleFilter['contains']['fish'] || $sampleFilter['contains']['prawns'])
	{
		if(!$containsFilterCount)
			$containsFilter = "7";
		else
			$containsFilter = $containsFilter.", 7";
		$containsFilterCount++;
	}	

	if($containsFilterCount) //Making it in a proper format
		$containsFilter = " AND nonvegContent in (".$containsFilter.")";
}




//Filter 4: SPICE LEVEL
$spicelevelFilter = "";
$spicelevelFilterCount = 0;
if(!$sampleFilter['spicelevel']['skip']){
	if($sampleFilter['spicelevel']['spicy']){
		if(!$spicelevelFilterCount)
			$spicelevelFilter = "1";
		else
			$spicelevelFilter = $spicelevelFilter.", 1";
		$spicelevelFilterCount++;
	}
	if($sampleFilter['spicelevel']['sweetened']){
		if(!$spicelevelFilterCount)
			$spicelevelFilter = "3";
		else
			$spicelevelFilter = $spicelevelFilter.", 3";
		$spicelevelFilterCount++;
	}
	if($sampleFilter['spicelevel']['non']){
		if(!$spicelevelFilterCount)
			$spicelevelFilter = "2";
		else
			$spicelevelFilter = $spicelevelFilter.", 2";
		$spicelevelFilterCount++;
	}

	if($spicelevelFilterCount) //Making it in a proper format
		$spicelevelFilter = " AND isSpicy in (".$spicelevelFilter.")";
}


//Filter 5: FRY TYPE
$frytypeFilter = "";
$frytypeFilterCount = 0;
if(!$sampleFilter['frytype']['skip']){
	if($sampleFilter['frytype']['oilfry']){
		if(!$frytypeFilterCount)
			$frytypeFilter = "1";
		else
			$frytypeFilter = $frytypeFilter.", 1";
		$frytypeFilterCount++;
	}
	if($sampleFilter['frytype']['tawafry']){
		if(!$frytypeFilterCount)
			$frytypeFilter = "2";
		else
			$frytypeFilter = $frytypeFilter.", 2";
		$frytypeFilterCount++;
	}

	if($frytypeFilterCount) //Making it in a proper format
		$frytypeFilter = " AND fryType in (".$frytypeFilter.")";
}



//Filter 6: COOKING TYPE
$cookingtypeFilter = "";
$cookingtypeFilterCount = 0;
if(!$sampleFilter['cookingtype']['skip']){
	if($sampleFilter['cookingtype']['deep']){
		if(!$cookingtypeFilterCount)
			$cookingtypeFilter = "4";
		else
			$cookingtypeFilter = $cookingtypeFilter.", 4";
		$cookingtypeFilterCount++;
	}
	if($sampleFilter['cookingtype']['dry']){
		if(!$cookingtypeFilterCount)
			$cookingtypeFilter = "3";
		else
			$cookingtypeFilter = $cookingtypeFilter.", 3";
		$cookingtypeFilterCount++;
	}
	if($sampleFilter['cookingtype']['semi']){
		if(!$cookingtypeFilterCount)
			$cookingtypeFilter = "2";
		else
			$cookingtypeFilter = $cookingtypeFilter.", 2";
		$cookingtypeFilterCount++;
	}
	if($sampleFilter['cookingtype']['gravy']){
		if(!$cookingtypeFilterCount)
			$cookingtypeFilter = "1";
		else
			$cookingtypeFilter = $cookingtypeFilter.", 1";
		$cookingtypeFilterCount++;
	}

	if($cookingtypeFilterCount) //Making it in a proper format
		$cookingtypeFilter = " AND cookingType in (".$cookingtypeFilter.")";
}



//Filter 5: BONE or BONELESS
$bonelessFilter = "";
$bonelessFilterCount = 0;
if(!$sampleFilter['boneless']['skip']){
	if($sampleFilter['boneless']['boneless']){
		if(!$bonelessFilterCount)
			$bonelessFilter = "2";
		else
			$bonelessFilter = $bonelessFilter.", 2";
		$bonelessFilterCount++;
	}
	if($sampleFilter['boneless']['bone']){
		if(!$bonelessFilterCount)
			$bonelessFilter = "1";
		else
			$bonelessFilter = $bonelessFilter.", 1";
		$bonelessFilterCount++;
	}

	if($bonelessFilterCount) //Making it in a proper format
		$bonelessFilter = " AND isBoneless in (".$bonelessFilter.")";
}

} //End If $filterFlag

$query = "SELECT DISTINCT mainType FROM ".$special_menu." WHERE mainType='{$type}'"; //mainType='{$type}'
$main = mysql_query($query);

$output = [];

while($rows = mysql_fetch_assoc($main))
{

	$mainType = $rows['mainType'];
	$submenuQuery = "SELECT DISTINCT subType FROM ".$special_menu." WHERE mainType='{$mainType}' ORDER BY sortIndex";
	$sub = mysql_query($submenuQuery);

	$subCategories=[];
	
	while($subrows = mysql_fetch_assoc($sub)){
		$subType = $subrows['subType'];

		//Check if filter is applied or not.
		if($filterFlag)
			$itemQuery = "SELECT * FROM ".$special_menu." WHERE mainType='{$mainType}' AND subType='{$subType}' ".$vegtypeFilter.$containsFilter.$spicelevelFilter.$frytypeFilter.$cookingtypeFilter.$bonelessFilter;
		else
			$itemQuery = "SELECT * FROM ".$special_menu." WHERE mainType='{$mainType}' AND subType='{$subType}' ORDER BY name";

		$allitems = mysql_query($itemQuery);

		//Put all the items into an array.
		$items=[];
		while($item = mysql_fetch_assoc($allitems)){		
			$items[] = array(
				"itemCode" => $item['code'],
				"itemName" => $item['name'],
				"itemPrice" => $item['price'],
				"isVeg" => $item['isVeg']? true : false,
				"isCustom" => $item['isCustomisable']? true : false,
				"custom" => json_decode($item['customisation']),
				"isAvailable" => $item[$outlet]? true : false
			); 
		}


		
		//Create the subCategory with it's name and items array just created. (ONLY IF "ITEMS" not empty.)
		if(count($items)){
			$subNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$subrows['subType']}'"));		
			$subCategories[] = array(
			    "subType" => $subrows['subType'],
			    "subName" => $subNameInfo['name'],
			    "items" => $items
			);
		}
	}


	//Add to final Output (only if subCategories NOT EMPTY)
	if(count($subCategories)){
		$mainNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$rows['mainType']}'"));
		$output[] =array(
			"mainType" => $rows['mainType'],
			"mainName"=> $mainNameInfo['name'],
			"subCategories" => $subCategories
		);
	}
}
   
echo json_encode($output);
		
?>