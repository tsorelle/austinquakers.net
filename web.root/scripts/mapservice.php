<?php
chdir($_SERVER['DOCUMENT_ROOT']);
include_once('tops/tops_startup.php');
// Tracer::On();


$bounds = $_POST['bounds'];
if (!$bounds)
 $bounds = 'no bounds';

$bounds = $_POST['bounds'];
if (!$bounds)
 $bounds = 'no bounds';

$values = explode(',',$bounds);
$swLat = $values[0];
$neLat = $values[1];
$swLong = $values[2];
$neLong = $values[3];

$instance = new TAddressLocations();
$response = $instance->GetLocationsInBox($swLat, $swLong, $neLat, $neLong );
$result = array();
$result["returnValue"] = $response;

echo json_encode($result);
