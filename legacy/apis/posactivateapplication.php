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
if($_POST['code'] == "90000"){

$activation_object = array(
      "licence" => "ACCELERATE_90000",
      "machineUID" => "Z90000",
      "dateInstall" => "01-02-2020",
      "dateExpire"=> "31-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "HSRLAYOUT",
      "branchName" => "HSR Layout",
      "client" => "SNAXBOX",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "77100"){

$activation_object = array(
      "licence" => "ACCELERATE_77100",
      "machineUID" => "Z77100",
      "dateInstall" => "21-02-2020",
      "dateExpire"=> "21-02-2021",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "HSRLAYOUT",
      "branchName" => "HSR Layout",
      "client" => "COALSPARK",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}else if($_POST['code'] == "77500"){

$activation_object = array(
      "licence" => "ACCELERATE_77500",
      "machineUID" => "Z77500",
      "dateInstall" => "09-12-2020",
      "dateExpire"=> "08-12-2021",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "KAMMANAHALLI",
      "branchName" => "Kammanahalli",
      "client" => "CARAVAN",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "77101"){

$activation_object = array(
      "licence" => "ACCELERATE_77101",
      "machineUID" => "Z77101",
      "dateInstall" => "21-02-2020",
      "dateExpire"=> "21-02-2021",
      "isTrial" => false,
      "machineCustomName" => "Restaurant",
      "branch" => "HSRLAYOUT",
      "branchName" => "HSR Layout",
      "client" => "COALSPARK",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "60000"){

$activation_object = array(
      "licence" => "ACCELERATE_60000",
      "machineUID" => "Z60000",
      "dateInstall" => "24-11-2019",
      "dateExpire"=> "23-11-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "ANNANAGAR",
      "branchName" => "Anna Nagar",
      "client" => "CAFEDUBAI",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "60001"){

$activation_object = array(
      "licence" => "ACCELERATE_60001",
      "machineUID" => "Z60001",
      "dateInstall" => "24-11-2019",
      "dateExpire"=> "23-11-2020",
      "isTrial" => false,
      "machineCustomName" => "Counter",
      "branch" => "ANNANAGAR",
      "branchName" => "Anna Nagar",
      "client" => "CAFEDUBAI",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "80000"){

$activation_object = array(
      "licence" => "ACCELERATE_80000",
      "machineUID" => "Z80000",
      "dateInstall" => "30-01-2020",
      "dateExpire"=> "29-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "HEGDENAGAR",
      "branchName" => "Hegde Nagar",
      "client" => "ZAATAR",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "80001"){

$activation_object = array(
      "licence" => "ACCELERATE_80001",
      "machineUID" => "Z80001",
      "dateInstall" => "30-01-2020",
      "dateExpire"=> "29-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Restaurant",
      "branch" => "HEGDENAGAR",
      "branchName" => "Hegde Nagar",
      "client" => "ZAATAR",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "81000"){

$activation_object = array(
      "licence" => "ACCELERATE_81000",
      "machineUID" => "Z81000",
      "dateInstall" => "15-02-2020",
      "dateExpire"=> "14-02-2021",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "BELLANDUR",
      "branchName" => "Bellandur",
      "client" => "ZAATAR",
      "isOnlineEnabled" => false,
      "isActive" => true
);

}
else if($_POST['code'] == "81001"){

$activation_object = array(
      "licence" => "ACCELERATE_81001",
      "machineUID" => "Z81001",
      "dateInstall" => "15-02-2020",
      "dateExpire"=> "14-02-2021",
      "isTrial" => false,
      "machineCustomName" => "Counter",
      "branch" => "BELLANDUR",
      "branchName" => "Bellandur",
      "client" => "ZAATAR",
      "isOnlineEnabled" => false,
      "isActive" => true
);

}
else if($_POST['code'] == "81002"){

$activation_object = array(
      "licence" => "ACCELERATE_81002",
      "machineUID" => "Z81002",
      "dateInstall" => "15-02-2020",
      "dateExpire"=> "14-02-2021",
      "isTrial" => false,
      "machineCustomName" => "Restaurant",
      "branch" => "BELLANDUR",
      "branchName" => "Bellandur",
      "client" => "ZAATAR",
      "isOnlineEnabled" => false,
      "isActive" => true
);

}
else if($_POST['code'] == "3000"){

$activation_object = array(
      "licence" => "ACCELERATE_3000",
      "machineUID" => "Z3000",
      "dateInstall" => "20-09-2019",
      "dateExpire"=> "19-09-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "KUMARASWAMYLAYOUT",
      "branchName" => "Kumaraswamy Layout",
      "client" => "OLIVEERA",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "3100"){

$activation_object = array(
      "licence" => "ACCELERATE_3100",
      "machineUID" => "Z100",
      "dateInstall" => "20-09-2019",
      "dateExpire"=> "19-09-2020",
      "isTrial" => false,
      "machineCustomName" => "Cash Counter",
      "branch" => "KUMARASWAMYLAYOUT",
      "branchName" => "Kumaraswamy Layout",
      "client" => "OLIVEERA",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "100"){

$activation_object = array(
      "licence" => "ACCELERATE_100",
      "machineUID" => "Z100",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "IITMADRAS",
      "branchName" => "IIT Madras",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "101"){

$activation_object = array(
      "licence" => "ACCELERATE_101",
      "machineUID" => "Z101",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "IITMADRAS",
      "branchName" => "IIT Madras",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "200"){

$activation_object = array(
      "licence" => "ACCELERATE_200",
      "machineUID" => "Z200",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NUNGAMBAKKAM",
      "branchName" => "Nungambakkam",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "201"){

$activation_object = array(
      "licence" => "ACCELERATE_201",
      "machineUID" => "Z201",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NUNGAMBAKKAM",
      "branchName" => "Nungambakkam",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "202"){

$activation_object = array(
      "licence" => "ACCELERATE_202",
      "machineUID" => "Z202",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "Office",
      "branch" => "NUNGAMBAKKAM",
      "branchName" => "Nungambakkam",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "300"){

$activation_object = array(
      "licence" => "ACCELERATE_300",
      "machineUID" => "Z300",
      "dateInstall" => "14-03-2019",
      "dateExpire"=> "13-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "SWIGGYRAMAPURAM",
      "branchName" => "Swiggy Kitchen Ramapuram",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "1300"){

$activation_object = array(
      "licence" => "ACCELERATE_1300",
      "machineUID" => "Z1300",
      "dateInstall" => "15-05-2019",
      "dateExpire"=> "14-05-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "SWIGGYTAMBARAM",
      "branchName" => "Swiggy Kitchen Tambaram",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "1400"){

$activation_object = array(
      "licence" => "ACCELERATE_1400",
      "machineUID" => "Z1400",
      "dateInstall" => "18-09-2019",
      "dateExpire"=> "17-09-2021",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "SWIGGYCHROMPET",
      "branchName" => "Swiggy Kitchen Chrompet",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "1600"){

$activation_object = array(
      "licence" => "ACCELERATE_1600",
      "machineUID" => "Z1600",
      "dateInstall" => "24-12-2019",
      "dateExpire"=> "23-12-2022",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "SWIGGYMOGAPPAIR",
      "branchName" => "Swiggy Kitchen Mogappair",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "900"){

$activation_object = array(
      "licence" => "ACCELERATE_900",
      "machineUID" => "Z900",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "AMBUR",
      "branchName" => "Ambur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "901"){

$activation_object = array(
      "licence" => "ACCELERATE_901",
      "machineUID" => "Z901",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "AMBUR",
      "branchName" => "Ambur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);


}
else if($_POST['code'] == "902"){

$activation_object = array(
      "licence" => "ACCELERATE_902",
      "machineUID" => "Z902",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "AMBUR",
      "branchName" => "Ambur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);



}
else if($_POST['code'] == "903"){

$activation_object = array(
      "licence" => "ACCELERATE_903",
      "machineUID" => "Z903",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "AMBUR",
      "branchName" => "Ambur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "800"){

$activation_object = array(
      "licence" => "ACCELERATE_800",
      "machineUID" => "Z800",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "JPNAGAR",
      "branchName" => "JP Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "801"){

$activation_object = array(
      "licence" => "ACCELERATE_801",
      "machineUID" => "Z801",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "JPNAGAR",
      "branchName" => "JP Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "802"){

$activation_object = array(
      "licence" => "ACCELERATE_802",
      "machineUID" => "Z802",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "JPNAGAR",
      "branchName" => "JP Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "803"){

$activation_object = array(
      "licence" => "ACCELERATE_803",
      "machineUID" => "Z803",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "JPNAGAR",
      "branchName" => "JP Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "804"){

$activation_object = array(
      "licence" => "ACCELERATE_804",
      "machineUID" => "Z804",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "JPNAGAR",
      "branchName" => "JP Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "805"){

$activation_object = array(
      "licence" => "ACCELERATE_805",
      "machineUID" => "Z805",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "JPNAGAR",
      "branchName" => "JP Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "400"){

$activation_object = array(
      "licence" => "ACCELERATE_400",
      "machineUID" => "Z400",
      "dateInstall" => "28-01-2019",
      "dateExpire"=> "27-01-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NAVALUR",
      "branchName" => "Navalur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "401"){

$activation_object = array(
      "licence" => "ACCELERATE_401",
      "machineUID" => "Z401",
      "dateInstall" => "28-01-2019",
      "dateExpire"=> "27-01-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NAVALUR",
      "branchName" => "Navalur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}


else if($_POST['code'] == "402"){

$activation_object = array(
      "licence" => "ACCELERATE_402",
      "machineUID" => "Z402",
      "dateInstall" => "28-01-2019",
      "dateExpire"=> "27-01-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NAVALUR",
      "branchName" => "Navalur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "403"){

$activation_object = array(
      "licence" => "ACCELERATE_403",
      "machineUID" => "Z403",
      "dateInstall" => "28-01-2019",
      "dateExpire"=> "27-01-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NAVALUR",
      "branchName" => "Navalur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "404"){

$activation_object = array(
      "licence" => "ACCELERATE_404",
      "machineUID" => "Z404",
      "dateInstall" => "28-01-2019",
      "dateExpire"=> "27-01-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NAVALUR",
      "branchName" => "Navalur",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "500"){

$activation_object = array(
      "licence" => "ACCELERATE_500",
      "machineUID" => "Z500",
      "dateInstall" => "18-03-2019",
      "dateExpire"=> "17-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "ADYAR",
      "branchName" => "Adyar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "501"){

$activation_object = array(
      "licence" => "ACCELERATE_501",
      "machineUID" => "Z501",
      "dateInstall" => "18-03-2019",
      "dateExpire"=> "17-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Swiggy Counter - 1",
      "branch" => "ADYAR",
      "branchName" => "Adyar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "502"){

$activation_object = array(
      "licence" => "ACCELERATE_502",
      "machineUID" => "Z502",
      "dateInstall" => "18-03-2019",
      "dateExpire"=> "17-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Swiggy Counter - 2",
      "branch" => "ADYAR",
      "branchName" => "Adyar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "503"){

$activation_object = array(
      "licence" => "ACCELERATE_503",
      "machineUID" => "Z503",
      "dateInstall" => "18-03-2019",
      "dateExpire"=> "17-03-2020",
      "isTrial" => false,
      "machineCustomName" => "First Floor",
      "branch" => "ADYAR",
      "branchName" => "Adyar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "1100"){

$activation_object = array(
      "licence" => "ACCELERATE_1100",
      "machineUID" => "Z1100",
      "dateInstall" => "02-05-2019",
      "dateExpire"=> "01-05-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "HALROAD",
      "branchName" => "HAL Road",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "1101"){

$activation_object = array(
      "licence" => "ACCELERATE_1101",
      "machineUID" => "Z1101",
      "dateInstall" => "02-05-2019",
      "dateExpire"=> "01-05-2020",
      "isTrial" => false,
      "machineCustomName" => "Cash Counter",
      "branch" => "HALROAD",
      "branchName" => "HAL Road",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "1102"){

$activation_object = array(
      "licence" => "ACCELERATE_1102",
      "machineUID" => "Z1102",
      "dateInstall" => "02-05-2019",
      "dateExpire"=> "01-05-2020",
      "isTrial" => false,
      "machineCustomName" => "Restaurant",
      "branch" => "HALROAD",
      "branchName" => "HAL Road",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "1103"){

$activation_object = array(
      "licence" => "ACCELERATE_1103",
      "machineUID" => "Z1103",
      "dateInstall" => "02-05-2019",
      "dateExpire"=> "01-05-2020",
      "isTrial" => false,
      "machineCustomName" => "Reception Desk",
      "branch" => "HALROAD",
      "branchName" => "HAL Road",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "600"){

$activation_object = array(
      "licence" => "ACCELERATE_600",
      "machineUID" => "Z600",
      "dateInstall" => "20-03-2019",
      "dateExpire"=> "19-03-2022",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "ROYAPETTAH",
      "branchName" => "Royapettah",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "601"){

$activation_object = array(
      "licence" => "ACCELERATE_601",
      "machineUID" => "Z601",
      "dateInstall" => "20-03-2019",
      "dateExpire"=> "19-03-2022",
      "isTrial" => false,
      "machineCustomName" => "First Floor",
      "branch" => "ROYAPETTAH",
      "branchName" => "Royapettah",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "602"){

$activation_object = array(
      "licence" => "ACCELERATE_602",
      "machineUID" => "Z602",
      "dateInstall" => "20-03-2019",
      "dateExpire"=> "19-03-2022",
      "isTrial" => false,
      "machineCustomName" => "Second Floor",
      "branch" => "ROYAPETTAH",
      "branchName" => "Royapettah",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "603"){

$activation_object = array(
      "licence" => "ACCELERATE_603",
      "machineUID" => "Z603",
      "dateInstall" => "20-03-2019",
      "dateExpire"=> "19-03-2022",
      "isTrial" => false,
      "machineCustomName" => "Cash Counter",
      "branch" => "ROYAPETTAH",
      "branchName" => "Royapettah",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "700"){

$activation_object = array(
      "licence" => "ACCELERATE_700",
      "machineUID" => "Z700",
      "dateInstall" => "01-04-2019",
      "dateExpire"=> "31-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "ANNANAGAR",
      "branchName" => "Anna Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "701"){

$activation_object = array(
      "licence" => "ACCELERATE_701",
      "machineUID" => "Z701",
      "dateInstall" => "01-04-2019",
      "dateExpire"=> "31-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Cash Counter - 1",
      "branch" => "ANNANAGAR",
      "branchName" => "Anna Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "702"){

$activation_object = array(
      "licence" => "ACCELERATE_702",
      "machineUID" => "Z702",
      "dateInstall" => "01-04-2019",
      "dateExpire"=> "31-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Cash Counter - 2",
      "branch" => "ANNANAGAR",
      "branchName" => "Anna Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "703"){

$activation_object = array(
      "licence" => "ACCELERATE_703",
      "machineUID" => "Z703",
      "dateInstall" => "01-04-2019",
      "dateExpire"=> "31-03-2020",
      "isTrial" => false,
      "machineCustomName" => "First Floor",
      "branch" => "ANNANAGAR",
      "branchName" => "Anna Nagar",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "1500"){

$activation_object = array(
      "licence" => "ACCELERATE_1500",
      "machineUID" => "Z1500",
      "dateInstall" => "01-08-2019",
      "dateExpire"=> "31-07-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "1501"){

$activation_object = array(
      "licence" => "ACCELERATE_1501",
      "machineUID" => "Z1501",
      "dateInstall" => "01-08-2019",
      "dateExpire"=> "31-07-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "1502"){

$activation_object = array(
      "licence" => "ACCELERATE_1502",
      "machineUID" => "Z1502",
      "dateInstall" => "01-08-2019",
      "dateExpire"=> "31-07-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "1503"){

$activation_object = array(
      "licence" => "ACCELERATE_1503",
      "machineUID" => "Z1503",
      "dateInstall" => "01-08-2019",
      "dateExpire"=> "31-07-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "1504"){

$activation_object = array(
      "licence" => "ACCELERATE_1504",
      "machineUID" => "Z1504",
      "dateInstall" => "01-08-2019",
      "dateExpire"=> "31-07-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}

else if($_POST['code'] == "1505"){

$activation_object = array(
      "licence" => "ACCELERATE_1505",
      "machineUID" => "Z1505",
      "dateInstall" => "01-08-2019",
      "dateExpire"=> "31-07-2020",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}


else if($_POST['code'] == "2000"){

$activation_object = array(
      "licence" => "ACCELERATE_2000",
      "machineUID" => "Z2000",
      "dateInstall" => "28-03-2019",
      "dateExpire"=> "27-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAMRUD",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "2001"){

$activation_object = array(
      "licence" => "ACCELERATE_2001",
      "machineUID" => "Z2001",
      "dateInstall" => "28-03-2019",
      "dateExpire"=> "27-03-2020",
      "isTrial" => false,
      "machineCustomName" => "Main Hall",
      "branch" => "VELACHERY",
      "branchName" => "Velachery",
      "client" => "ZAMRUD",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "2100"){

$activation_object = array(
      "licence" => "ACCELERATE_2100",
      "machineUID" => "Z2100",
      "dateInstall" => "25-01-2020",
      "dateExpire"=> "24-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Server",
      "branch" => "MADURAI",
      "branchName" => "Madurai",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "2101"){

$activation_object = array(
      "licence" => "ACCELERATE_2101",
      "machineUID" => "Z2101",
      "dateInstall" => "25-01-2020",
      "dateExpire"=> "24-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Front Desk",
      "branch" => "MADURAI",
      "branchName" => "Madurai",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "2102"){

$activation_object = array(
      "licence" => "ACCELERATE_2102",
      "machineUID" => "Z2102",
      "dateInstall" => "25-01-2020",
      "dateExpire"=> "24-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Cash Counter",
      "branch" => "MADURAI",
      "branchName" => "Madurai",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "2103"){

$activation_object = array(
      "licence" => "ACCELERATE_2103",
      "machineUID" => "Z2103",
      "dateInstall" => "25-01-2020",
      "dateExpire"=> "24-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Second Floor",
      "branch" => "MADURAI",
      "branchName" => "Madurai",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "2104"){

$activation_object = array(
      "licence" => "ACCELERATE_2104",
      "machineUID" => "Z2104",
      "dateInstall" => "25-01-2020",
      "dateExpire"=> "24-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Third Floor",
      "branch" => "MADURAI",
      "branchName" => "Madurai",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
else if($_POST['code'] == "2105"){

$activation_object = array(
      "licence" => "ACCELERATE_2105",
      "machineUID" => "Z2105",
      "dateInstall" => "25-01-2020",
      "dateExpire"=> "24-01-2021",
      "isTrial" => false,
      "machineCustomName" => "Office",
      "branch" => "MADURAI",
      "branchName" => "Madurai",
      "client" => "ZAITOON",
      "isOnlineEnabled" => true,
      "isActive" => true
);

}
$output = array(
      "status" => true,
      "error" => "",
      "response" => $activation_object    
);

echo json_encode($output);

?>
