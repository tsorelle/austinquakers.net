<?
/*****************************************************************

                               11/20/2006 4:33PM
*****************************************************************/
class TDatabaseLogger extends TLogger
{
    var $logId;
    var $userName;
    var $database;
    var $hostname;
    var $username;
    var $password;
    var $tablename;


    public function __construct($logId)
    {
        $this->logId = $logId;
        $configuration = TConfiguration::GetDatabaseSettings();
        $databaseId = $configuration->getValue('database','logging','default');
        TTracer::Trace('$databaseId = '.$databaseId);
        $this->database = $configuration->getValue($databaseId,'database');
        if (empty($this->database)) {
            trigger_error('Database name for logging not found in configuration file.',E_USER_NOTICE);
            return NULL;
        }
        $this->hostname = $configuration->getValue($databaseId,'host','localhost');
        $this->username = $configuration->getValue($databaseId,'username',$this->database);
        $this->password = $configuration->getValue($databaseId,'password');

        $configuration = TConfiguration::GetSettings();
        $this->tablename = $configuration->getValue('logging','table','logs');

    }  //  TDatabaseLogger

    private function checkError()
    {
        $lastError = mysql_errno();
        if (!$lastError) {
          return true;
        }
        trigger_error('SQL ERROR ('.$lastError.') '.mysql_error(),E_USER_NOTICE);
        return false;
    }  //  checkError

    private function connect()
    {

        $link = mysql_connect($this->hostname,$this->username,$this->password);

        if (!$this->checkError())
            return NULL;

        mysql_select_db($this->database);
        if (!$this->checkError())
            return NULL;

        return $link;

    }    //  connect


    protected function writeToLog($time, $user, $message, $details='', $stackTrace='')
    {
         TTracer::Trace("DbLogger: $time $user: $message");
         TTracer::Trace("trace: $stackTrace");
         $link = $this->connect();
         $user = $this->getUserId();
         $sessionId = session_id();
         if ($link) {
             $cmdString =
                'INSERT into '.$this->tablename.
                ' (logId, user, sessionId, message, details, stackTrace) values'.
                " ('$this->logId','$user', '$sessionId','".
                addslashes($message)."','".
                addslashes($details)."','".
                addslashes($stackTrace)."')";
              TTracer::Trace($cmdString);
              mysql_query($cmdString);
              if (mysql_errno()) {
                    TTracer::Trace(
                        'Database error('.
                        mysql_errno().
                        ') '.
                        mysql_error());
                }
              mysql_close($link);
         }
    }  //  writeToLog

}   // finish class TDatabaseLogger

