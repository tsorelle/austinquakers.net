<?
/*****************************************************************

                               11/19/2006 8:41PM
*****************************************************************/
class TFileLogger extends TLogger
{
    private $filePath;
    private $logId;

    private function getLogPath($logId)
    {
        $configuration = getDefaultConfiguration();
        $logDir = $configuration->getValue('logging','filelocation','tops/logs');
        //TTracer::Trace('$logDir='.$logDir);
        $logPath = $_SERVER['DOCUMENT_ROOT']."/$logDir";
        //trace('$logPath='.$logPath);
        if (!is_dir($logPath)) {
            trigger_error("Cannot find log file path.",E_USER_NOTICE);
            return NULL;
        }

        return realpath($logPath)."/$logId.csv";
    }  //  getLogPath


    public function __construct($logId)
    {
        $this->logId = $logId;
        $this->filePath = $this->getLogPath($logId);
        //trace('filePath='.$this->filePath);
    }  //  TFileLog

    protected function writeToLog($time, $user, $message, $details='', $stackTrace='')
    {
        //trace("FileLogger: $time, $user, $message, $this->filePath");
        @ $fp = fopen($this->filePath,'a');
        if (!$fp) {
            trigger_error("Cannot open log file for $this->logId at $this->filePath.",E_USER_NOTICE);
            return;
        }
        if (!flock($fp,2)) {
            trigger_error("Cannot acquire lock on log file for $this->logId.",E_USER_NOTICE);
            fclose($fp);
            return;
        }
        $sessionId = session_id();
        if (!empty($details))
            $details = '"'.$details.'"';
        if (!empty($stackTrace))
            $stackTrace = '"'.$stackTrace.'"';
        fwrite($fp,"$time,$user,$sessionId,".'"'.$message.'"'."$details,$stackTrace\r\n");
        flock($fp,3); // release lock
        fclose($fp);
    }  //  writeToLog

}   // finish class TFileLog

