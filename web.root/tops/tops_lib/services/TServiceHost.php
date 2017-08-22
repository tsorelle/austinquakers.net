<?php
/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 2/28/14
 * Time: 6:42 AM
 */

class TServiceHost {
    public static function ExecuteRequest() {
        TTracer::Trace("TServiceHost::ExecuteRequest()");
        if (!isset($_REQUEST['serviceCode']))
            throw new Exception('No service command id was in request');
        $commandId = $_REQUEST['serviceCode'];
        $input = null;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['request'])) {
                $input = json_decode($_POST['request']);
            }
        }
        else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (isset($_GET['request'])) {
                $input = $_GET['request'];
            }
        }
        else
            throw new Exception('Unsupported request method: '.$_SERVER['REQUEST_METHOD']);

        return self::Execute($commandId, $input);
    }

    public static function CreateServiceCommand($serviceId) {
        $className = $serviceId."Command";
        $result = include( TClassLib::GetSiteLib().'/services/'.$className.'.php' );

        if (!$result) {
            TTracer::Trace("no result for '$className'");
            return null;
        }
        eval('$result = new '.$className.'();');
        return $result;
    }



    public static function Execute($commandId,$input) {
        TTracer::Trace("TServiceHost::Execute('$commandId',...)");
        $command = self::CreateServiceCommand($commandId);
        if (empty($command))
            throw new Exception("Unable to create service command '$commandId'");
        return $command->Execute($input);
    }
} 