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


if(!isset($_POST['secret'])){
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Unauthorised Application"
	);
	die(json_encode($output));
}


if(!isset($_POST['code'])){
	$output = array(
		"status" => false,
		"errorCode" => 404,
		"error" => "Enter Activation Code"
	);
	die(json_encode($output));
}



if($_POST['code'] == "200"){

$activation_object = array(
      "licence" => "ACCELERATE_200",
      "machineUID" => "Z200",
      "dateInstall" => "08-09-2018",
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "NUNGAMBAKKAM",
      "branchName" => "Nungambakkam",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
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
      "dateExpire"=> "15-09-2019",
      "isTrial" => false,
      "machineCustomName" => "",
      "branch" => "JPNAGAR",
      "branchName" => "JP Nagar",
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
