<?php
/*****************************************************************
Gateway class and class factory for TLogger objects.

                               9/29/2007 3:07PM
*****************************************************************/
class TLog
{
    private static $logs = array();

    public static function GetLog($logId='application')  {
        global $errorLoggingDisabled;
        $errorLoggingDisabled = true;
        if (isset(TLog::$logs[$logId])) {
            $log = TLog::$logs[$logId];
        }
        else {
            $configuration = TConfiguration::GetSettings();
            $logType = $configuration->getValue('logs',$logId,'file');
            $instantiation = '$log = new T'.ucfirst($logType).'Logger($logId);';
            eval($instantiation);
            TLog::$logs[$logId] = $log;
        }
        return $log;
       // return null; // new TDisplayLog();
    }

    public static function Write($message,$logId='application',$details='', $stackTrace='')
    {

        $log = TLog::GetLog($logId);
        $log->write($message, $details, $stackTrace);
    }  //  writeLog


    public static function WriteError($message, $details='', $stackTrace='')
    {
    TTracer::Trace("Writing ERROR to log: $message");
         TTracer::Trace("Write, Stack: $stackTrace");
        TLog::Write($message,'errors', $details, $stackTrace);
    }  //  logError

    public static function WriteWarning($message, $details='')
    {
        // TTracer::Trace("Writing WARNING to log: $message");

        TLog::Write($message,'warnings',$details);
    }  //  logError

}   // finish class TLog
