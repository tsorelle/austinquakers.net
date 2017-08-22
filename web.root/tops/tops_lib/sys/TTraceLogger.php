<?php
/*****************************************************************
    `description`
                               11/19/2006 8:44PM
*****************************************************************/
class TTraceLogger extends TLogger
{
    private $logId;
    public function __construct($logId='application')
    {
         $this->logId = $logId;
    }  //  TDisplayLogger

    protected function writeToLog($time, $user, $message, $details='', $stackTrace='')
    {
         TTracer::Trace("TraceLogger($this->logId): $time $user: $message<br/>");
    }  //  writeToLog
}   // finish class TDisplayLog

