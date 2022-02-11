<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0); 

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);


/*
if(!isset($_POST['secret'])){
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Unauthorised Application"
	);
	die(json_encode($output));
}
*/ 

if(!isset($_POST['code'])){
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Enter Activation Code"
	);
	die(json_encode($output));
}

if($_POST['code'] == "801"){
$activation_object = array(
    
      "deviceUID" => "T001",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_001",
      "branch_code" => "JPNAGAR",
      "branch_name"=> "JP Nagar",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "802"){
$activation_object = array(
    
      "deviceUID" => "T002",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_002",
      "branch_code" => "JPNAGAR",
      "branch_name"=> "JP Nagar",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "803"){
$activation_object = array(
    
      "deviceUID" => "T003",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_003",
      "branch_code" => "JPNAGAR",
      "branch_name"=> "JP Nagar",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "804"){
$activation_object = array(
    
      "deviceUID" => "T004",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_004",
      "branch_code" => "JPNAGAR",
      "branch_name"=> "JP Nagar",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "805"){
$activation_object = array(
    
      "deviceUID" => "T005",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_005",
      "branch_code" => "JPNAGAR",
      "branch_name"=> "JP Nagar",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "806"){
$activation_object = array(
    
      "deviceUID" => "T006",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_006",
      "branch_code" => "JPNAGAR",
      "branch_name"=> "JP Nagar",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "807"){
$activation_object = array(
    
      "deviceUID" => "T007",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_007",
      "branch_code" => "JPNAGAR",
      "branch_name"=> "JP Nagar",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1100"){
$activation_object = array(
      "deviceUID" => "T1100",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1100",
      "branch_code" => "AMBUR",
      "branch_name"=> "Ambur",
      "activation_date" => "05-01-2020",
      "expiry_date" => "04-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1101"){
$activation_object = array(
      "deviceUID" => "T1101",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1101",
      "branch_code" => "AMBUR",
      "branch_name"=> "Ambur",
      "activation_date" => "05-01-2020",
      "expiry_date" => "04-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1102"){
$activation_object = array(
      "deviceUID" => "T1102",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1102",
      "branch_code" => "AMBUR",
      "branch_name"=> "Ambur",
      "activation_date" => "05-01-2020",
      "expiry_date" => "04-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1103"){
$activation_object = array(
      "deviceUID" => "T1103",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1103",
      "branch_code" => "AMBUR",
      "branch_name"=> "Ambur",
      "activation_date" => "05-01-2020",
      "expiry_date" => "04-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1104"){
$activation_object = array(
      "deviceUID" => "T1104",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1104",
      "branch_code" => "AMBUR",
      "branch_name"=> "Ambur",
      "activation_date" => "05-01-2020",
      "expiry_date" => "04-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "900"){
$activation_object = array(
    
      "deviceUID" => "T900",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_900",
      "branch_code" => "HALROAD",
      "branch_name"=> "HAL Road",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "901"){
$activation_object = array(
    
      "deviceUID" => "T901",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_901",
      "branch_code" => "HALROAD",
      "branch_name"=> "HAL Road",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "902"){
$activation_object = array(
    
      "deviceUID" => "T902",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_902",
      "branch_code" => "HALROAD",
      "branch_name"=> "HAL Road",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "903"){
$activation_object = array(
    
      "deviceUID" => "T903",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_903",
      "branch_code" => "HALROAD",
      "branch_name"=> "HAL Road",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "904"){
$activation_object = array(
    
      "deviceUID" => "T904",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_904",
      "branch_code" => "HALROAD",
      "branch_name"=> "HAL Road",
      "activation_date" => "27-02-2019",
      "expiry_date" => "26-02-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1500"){
$activation_object = array(
    
      "deviceUID" => "T1500",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1500",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1501"){
$activation_object = array(
    
      "deviceUID" => "T1501",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1501",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1502"){
$activation_object = array(
    
      "deviceUID" => "T1502",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1502",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1503"){
$activation_object = array(
    
      "deviceUID" => "T1503",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1503",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1504"){
$activation_object = array(
    
      "deviceUID" => "T1504",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1504",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1505"){
$activation_object = array(
    
      "deviceUID" => "T1505",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1505",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1505"){
$activation_object = array(
    
      "deviceUID" => "T1505",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1505",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1506"){
$activation_object = array(
    
      "deviceUID" => "T1506",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1506",
      "branch_code" => "VELACHERY",
      "branch_name"=> "Velachery",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "600"){
$activation_object = array(
    
      "deviceUID" => "T600",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_600",
      "branch_code" => "ROYAPETTAH",
      "branch_name"=> "Royapettah",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "601"){
$activation_object = array(
    
      "deviceUID" => "T601",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_601",
      "branch_code" => "ROYAPETTAH",
      "branch_name"=> "Royapettah",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "602"){
$activation_object = array(
    
      "deviceUID" => "T602",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_602",
      "branch_code" => "ROYAPETTAH",
      "branch_name"=> "Royapettah",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "603"){
$activation_object = array(
    
      "deviceUID" => "T603",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_603",
      "branch_code" => "ROYAPETTAH",
      "branch_name"=> "Royapettah",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "604"){
$activation_object = array(
    
      "deviceUID" => "T604",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_604",
      "branch_code" => "ROYAPETTAH",
      "branch_name"=> "Royapettah",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "200"){
$activation_object = array(
    
      "deviceUID" => "T200",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_200",
      "branch_code" => "NUNGAMBAKKAM",
      "branch_name"=> "Nungambakkam",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "201"){
$activation_object = array(
    
      "deviceUID" => "T201",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_201",
      "branch_code" => "NUNGAMBAKKAM",
      "branch_name"=> "Nungambakkam",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "202"){
$activation_object = array(
    
      "deviceUID" => "T202",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_202",
      "branch_code" => "NUNGAMBAKKAM",
      "branch_name"=> "Nungambakkam",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "203"){
$activation_object = array(
    
      "deviceUID" => "T203",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_203",
      "branch_code" => "NUNGAMBAKKAM",
      "branch_name"=> "Nungambakkam",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "204"){
$activation_object = array(
    
      "deviceUID" => "T204",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_204",
      "branch_code" => "NUNGAMBAKKAM",
      "branch_name"=> "Nungambakkam",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "400"){
$activation_object = array(
    
      "deviceUID" => "T400",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_400",
      "branch_code" => "NAVALUR",
      "branch_name"=> "Navalur",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "401"){
$activation_object = array(
    
      "deviceUID" => "T401",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_401",
      "branch_code" => "NAVALUR",
      "branch_name"=> "Navalur",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "402"){
$activation_object = array(
    
      "deviceUID" => "T402",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_402",
      "branch_code" => "NAVALUR",
      "branch_name"=> "Navalur",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "403"){
$activation_object = array(
    
      "deviceUID" => "T403",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_403",
      "branch_code" => "NAVALUR",
      "branch_name"=> "Navalur",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "404"){
$activation_object = array(
    
      "deviceUID" => "T404",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_404",
      "branch_code" => "NAVALUR",
      "branch_name"=> "Navalur",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "405"){
$activation_object = array(
    
      "deviceUID" => "T405",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_405",
      "branch_code" => "NAVALUR",
      "branch_name"=> "Navalur",
      "activation_date" => "01-08-2019",
      "expiry_date" => "31-07-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "500"){
$activation_object = array(
    
      "deviceUID" => "T500",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_500",
      "branch_code" => "ADYAR",
      "branch_name"=> "Adyar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "501"){
$activation_object = array(
    
      "deviceUID" => "T501",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_501",
      "branch_code" => "ADYAR",
      "branch_name"=> "Adyar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "502"){
$activation_object = array(
    
      "deviceUID" => "T502",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_502",
      "branch_code" => "ADYAR",
      "branch_name"=> "Adyar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "503"){
$activation_object = array(
    
      "deviceUID" => "T503",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_503",
      "branch_code" => "ADYAR",
      "branch_name"=> "Adyar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "504"){
$activation_object = array(
    
      "deviceUID" => "T504",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_504",
      "branch_code" => "ADYAR",
      "branch_name"=> "Adyar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "700"){
$activation_object = array(
    
      "deviceUID" => "T700",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_700",
      "branch_code" => "ANNANAGAR",
      "branch_name"=> "Anna Nagar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "701"){
$activation_object = array(
    
      "deviceUID" => "T701",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_701",
      "branch_code" => "ANNANAGAR",
      "branch_name"=> "Anna Nagar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "702"){
$activation_object = array(
    
      "deviceUID" => "T702",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_702",
      "branch_code" => "ANNANAGAR",
      "branch_name"=> "Anna Nagar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "703"){
$activation_object = array(
    
      "deviceUID" => "T703",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_703",
      "branch_code" => "ANNANAGAR",
      "branch_name"=> "Anna Nagar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "704"){
$activation_object = array(
    
      "deviceUID" => "T704",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_704",
      "branch_code" => "ANNANAGAR",
      "branch_name"=> "Anna Nagar",
      "activation_date" => "22-08-2019",
      "expiry_date" => "21-08-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1800"){
$activation_object = array(
    
      "deviceUID" => "T1800",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1800",
      "branch_code" => "MADURAI",
      "branch_name"=> "Madurai",
      "activation_date" => "25-01-2020",
      "expiry_date" => "24-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1804"){
$activation_object = array(
    
      "deviceUID" => "T1804",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1804",
      "branch_code" => "MADURAI",
      "branch_name"=> "Madurai",
      "activation_date" => "25-01-2020",
      "expiry_date" => "24-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1801"){
$activation_object = array(
    
      "deviceUID" => "T1801",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1801",
      "branch_code" => "MADURAI",
      "branch_name"=> "Madurai",
      "activation_date" => "25-01-2020",
      "expiry_date" => "24-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1802"){
$activation_object = array(
    
      "deviceUID" => "T1802",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1802",
      "branch_code" => "MADURAI",
      "branch_name"=> "Madurai",
      "activation_date" => "25-01-2020",
      "expiry_date" => "24-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else if($_POST['code'] == "1803"){
$activation_object = array(
    
      "deviceUID" => "T1803",
      "device_name"=> "",
      "device_license_code" => "ACCELERATE_TAPS_1803",
      "branch_code" => "MADURAI",
      "branch_name"=> "Madurai",
      "activation_date" => "25-01-2020",
      "expiry_date" => "24-01-2022",
      "client" => "ZAITOON",
      "isActive" => true,
      "isTrial" => false
);
}
else{
$output = array(
	"status" => false,
	"error" => "Invalid Code",
	"response" => ""	
);

die(json_encode($output));

}

$output = array(
	"status" => true,
	"error" => "",
	"response" => $activation_object	
);

echo json_encode($output);

?>
