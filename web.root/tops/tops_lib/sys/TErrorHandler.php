<?php
/*****************************************************************
 DEPRECATED.
                               11/17/2006 7:52PM
*****************************************************************/
require_once('tops_lib/sys/TTracer.php');
define('ERROR_LEVEL_NONE',0);
define('ERROR_LEVEL_NOTICE',1);
define('ERROR_LEVEL_WARNING',2);
define('ERROR_LEVEL_ERROR',3);
define('E_ALL_NOTICES',E_USER_NOTICE | E_NOTICE);
define('E_ALL_WARNINGS',E_USER_WARNING | E_WARNING);
define('E_ALL_ERRORS',E_USER_ERROR | E_ERROR);

class TErrorHandler
{
    private $messages = array();
    private $errorLevel=ERROR_LEVEL_NONE;
    private $saveErrors = 0;
    private $loggedErrors = 0;
    private $throwException = true;

    function errnoToErrorLevel($errno)
    {
        if ($errno == E_USER_NOTICE)
            return ERROR_LEVEL_NOTICE;
        if ($errno == E_USER_WARNING)
            return ERROR_LEVEL_WARNING;
        else
            return ERROR_LEVEL_ERROR;
    }  //  errnoToErrorLevel

    function logErrors($mask=E_ALL_ERRORS)
    {
        $this->loggedErrors = $mask;
    }  //  setLoggingLevel

    function getErrorMessageText($errstr)
    {
    }  //  handleFatal

    static function dumpException($ex) {
        echo "Exception caught.<br/>";
        echo $ex->getMessage()."<br/>";
        echo "<br/>STACK TRACE:<br/>";
        $traceMessages = split('#',$ex->getTraceAsString());
        foreach($traceMessages as $message)
            echo "$message<br/>";
        echo "<br/>";
    }

    function processError($errno,$errstr)
    {
        if (($errno & (E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE | $this->saveErrors)) != 0) {
            array_push($this->messages,$errstr);
            $newLevel = $this->errnoToErrorLevel($errno);
            if ($newLevel > $this->errorLevel)
                $this->errorLevel = $newLevel;

            TTracer::Trace("Error occurred: $errstr [$errno] ");
            if ((($errno & ($this->loggedErrors)) != 0) && empty($errorLoggingDisabled))
                logError("Error ($errno): $errstr");
            return true;
        }
        return false;

    }  //  processError

    function handleError($errno,$errstr)
    {
        if ($this->throwException)
            throw new Exception($errstr,$errno);
        if (!($this->processError($errstr,$errno)))
            exit("<strong>Error:</strong>$errstr.");

    }  //  handleError

    function handleException($ex)
    {
        $errorstr = $ex->getMessage().' Stack trace: '.$ex->getTraceAsString();
        if (!($this->processError($errstr,$ex->getCode()))) {
            $this->dumpException($ex);
            exit("Terminated.");
        }
    }  //  handleException

    function getMessages()
    {
        return $this->messages;
    }  //  getMessages

    function getErrorLevel()
    {
        return $this->errorLevel;
    }  //  getErrorLevel

    function setSaveErrors($errorMask)
    {
        $this->saveErrors = $errorMask;
    }  //  setSaveError

    function setThrowException($value)
    {
        $this->throwException = $value;
    }  //  setThrowException

    function reset()
    {
        $this->messages = array();
        $this->ErrorLevel = ERROR_LEVEL_NONE;
    }  //  reset

}   // finish class TErrorHandler

$errorHandler=NULL;

function handleError($errno,$errstr)
{
    global $errorHandler;
    $errorHandler->handleError($errno,$errstr);
}

function errorHandlingOn($saveWarnings=false)
{
    global $errorHandler;
    $errorHandler = new TErrorHandler();
    if ($saveWarnings)
        $errorHandler->setSaveErrors(E_NOTICE | E_WARNING);

    set_error_handler('handleError');
}  //  errorHandlingOn

function handleErrorsAndWarnings()
{
    errorHandlingOn(true);
}  //  handleErrorsAndWarnings

function formatMessage($message,$file,$line)
{
    if (!empty($file)) {
        if (!empty($line))
            $message .= "  on line $line";
        $message .= " in file '$file'.";
    }
    return $message;
}  //  formatMessage

function raiseFatalError($message,$file='',$line=0)
{
     trigger_error(formatMessage($message,$file,$line),E_USER_ERROR);
     exit('Fatal error. Terminating script');
}  //  raiseError

function raiseError($message,$file='',$line=0)
{
     trigger_error(formatMessage($message,$file,$line),E_USER_ERROR);
}  //  raiseError
function raiseNotice($message,$file='',$line=0)
{
     trigger_error(formatMessage($message,$file,$line),E_USER_NOTICE);
}  //  raiseError
function raiseWarning($message,$file='',$line=0)
{
     trigger_error(formatMessage($message,$file,$line),E_USER_WARNING);
}  //  raiseError

function getErrorLevel()
{
    global $errorHandler;
    if (!isset($errorHandler))
        return 0;
   return $errorHandler->getErrorLevel();
}

function resetErrorHandler()
{
    global $errorHandler;
    if (isset($errorHandler))
        $errorHandler->reset();
    else
        errorHandlingOn(true);
}  //  resetErrorHandler


