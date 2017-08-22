<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/13/14
 * Time: 5:02 PM
 */
/*
$_SERVER['DOCUMENT_ROOT'] = realpath('.'); // startup directory set in configuration
$docRoot = $_SERVER['DOCUMENT_ROOT'];
require($docRoot.'/tops/tools/starttools.php');
*/
$a = array();

$b = array_key_exists("test",$a);
if ($b)
    print 'failed'."\n";
else
    print 'ok'."\n";

$c='value';
$a['test'] =$c;
$b = $a['test'];
if ($b)
    print 'ok'."\n";
else
    print 'failed'."\n";
