<?php
print "Tops Initialzing...";
$fileRoot = realpath(__DIR__.'/../web.root');
chdir($fileRoot);
$topslib = realpath('tops/tops_lib');
$sitelib = realpath('tops/site_lib');
$traceEnabled = false;
// get tops startup parameters
// Store library paths and update PHP includes path
require_once("$topslib/sys/TClassLib.php");
// TClassLib::Create($topslib, $sitelib);
TClassLib::Create();

// Set __autoload class search
require_once('tops_lib/sys/TClassPath.php');
//TClassPath::Add($topslib,'sys','db','model','view','ui','drupal/sys');
// TClassPath::Add($sitelib,'model','view','sys');

// Set __autoload class search
TClassPath::Add(TClassLib::GetTopsLib(),'sys','db','model','view','ui','drupal/sys');
TClassPath::Add(TClassLib::GetSiteLib(),'model','view','sys');

// enable tops error handling
require_once ('tops_lib/sys/errorHandling.php');
print "\ninitialized\n";
// fake user??