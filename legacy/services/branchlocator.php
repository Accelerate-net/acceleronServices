<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';
// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];


// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Search the rows in the markers table
$query =  sprintf("SELECT id, name, code, fotos, line1, line2, city, latitude, contact, operationalHours, longitude, ( 3959 * acos( cos( radians('%s') ) *  cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM z_outlets HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20",
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($center_lng),
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($radius));
$result = mysql_query($query);
$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}
header("Content-type: text/xml");
// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){

  $photo_url = "";
  $photos = json_decode($row['fotos']);
  $photo_url = $photos[0];

  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("id", $row['id']);
  $newnode->setAttribute("name", $row['name']);
  $newnode->setAttribute("code", $row['code']);
  $newnode->setAttribute("photo", $photo_url);
  $newnode->setAttribute("contact", $row['contact']); 
    $newnode->setAttribute("operationalHours", $row['operationalHours']);  
  $newnode->setAttribute("address", $row['line1'].", ".$row['line2'].", ".$row['city']);
  $newnode->setAttribute("lat", $row['latitude']);
  $newnode->setAttribute("lng", $row['longitude']);
  $newnode->setAttribute("distance", $row['distance']);
}
echo $dom->saveXML();
?>