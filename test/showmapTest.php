<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/23/2017
 * Time: 4:46 PM
 */
include ("startup.php");
include ("FakePageController.php");
include(__DIR__."/../web.root/tops/site_lib/applications/directory/classes/TShowMap.php");
$owner = new FakePageController();
$action  = new TShowMap($owner);
$result = $action->execute();
print "\ndone\n";