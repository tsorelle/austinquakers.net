<?
/*****************************************************************
    `description`
                               11/19/2006 8:44PM
*****************************************************************/
class TDrupalLogger extends TLogger
{
    private $logId;
    public function __construct($logId='application')
    {
         $this->logId = $logId;
    }  //  TDisplayLogger

    protected function writeToLog($time, $user, $message, $details='', $stackTrace='')
    {
        $severity = $logId == 'error' ? WATCHDOG_ERROR : WATCHDOG_NOTICE;
        $format = 'tops entry: user:$user; message: $message;';
        $vars = array(
            '%user' => $user,
            '%message' => $message,
        );

        if (!empty($details)) {
            $format .= ' details: $details;';
            $vars['%details'] = $details;
        }

        if (!empty($stackTrace)) {
            $format .= ' stack trace: $stackTrace';
            $vars['%stackTrace'] = $stackTrace;
        }

        watchdog('php', $format, $vars, $severity);
    }  //  writeToLog
}   // finish class TDisplayLog

