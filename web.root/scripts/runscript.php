<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/27/2015
 * Time: 1:21 PM
 *
 * Script command line syntax:
 * /ramdisk/bin/php5 -q /home1/austinqu/public_html/austinquakers.net.root/scripts/runscript.php [script name]  [arguments . . .]
 *
 * script name is the name of a file located in the scripts directory. Omit '.php'
 *
 */
if (PHP_OS == 'Linux') {
    // bluehost site
    $DOCUMENT_ROOT = (isset($_SERVER['HOME']) ? $_SERVER['HOME'] : '/home1/austinqu').'/public_html/austinquakers.net.root';

    // for PEAR Mail
    ini_set("include_path",'.:/usr/lib64/php:/usr/lib/php:/usr/share/pear:/home1/austinqu/php');
}
else {
    // for local testing
    $DOCUMENT_ROOT = realpath(__DIR__.'/..');
}
// add additional include directories
$includesPath = '';
if (!empty($includesPath))
    ini_set('include_path', ini_get('include_path').$includesPath);
unset($includesPath);

date_default_timezone_set( 'America/Chicago' ) ;

require_once("$DOCUMENT_ROOT/tops/tops_lib/sys/TClassLib.php");
TClassLib::Create($DOCUMENT_ROOT.'/tops/tops_lib', $DOCUMENT_ROOT.'/tops/site_lib');

// Set __autoload class search
require_once("tops_lib/sys/TClassPath.php");
TClassPath::Add(TClassLib::GetTopsLib(),'sys','db','model','view','ui','drupal/sys');
TClassPath::Add(TClassLib::GetSiteLib(),'model','view','sys');

// Set up error handling
require_once("$DOCUMENT_ROOT/tops/tops_lib/sys/errorHandling.php");
// error_reporting(E_ERROR | E_WARNING | E_PARSE); // surpress strict warnings from pear mail.

print "\nLoading script\n";
if ($argc < 2)
    TConsole::WriteLine("No script arguments found.");
else {
    include("$DOCUMENT_ROOT/scripts/$argv[1].php");
}

echo "\nAustinQuakers.net jobs complete\n";
