<?php
include ("startup.php");
$settings = TMappingApi::GetCurrentProvider();
print "Using $settings->providerName\n";
$p = TMappingApi::Create($settings->providerName);
$address1 =  '904 E Meadowmere';
$address2 = '';
$city = 'Austin';
$province = 'TX';
$postalCode='78758';
$country = 'US';

$actual = $p->GetLocation($address1,$address2,$city,$province,$postalCode,'US');
print 'done.';