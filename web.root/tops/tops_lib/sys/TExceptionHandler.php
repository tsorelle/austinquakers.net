<?php
require_once('tops_lib/sys/TErrorSettings.php');
require_once('tops_lib/sys/TopsException.php');
/*****************************************************************
    Helper Class for Error and
    Exception handling and logging               7/1/2007 5:32AM
*****************************************************************/
class TExceptionHandler
{
    public static $ErrorHandlingEnabled = true;
    public static $ExceptionHandlingEnabled = true;
    public static $LastHandledErrorMessage = '';

    public static function ShowStackTrace(Exception $ex) {
        echo "Stack trace:";
        $traceMessages = split('#',$ex->getTraceAsString());
        foreach($traceMessages as $message)
            echo "$message<br/>";
    }

    public static function ShowException(Exception $ex, $count=0, $showDetails=false, $showStackTrace=false) {
        echo 'Exception';
        if ($count)
            echo " $count";
        echo ':  ';
        echo $ex->getMessage().'<br/>';

        if (($ex instanceof IExtendedException) && $showDetails) {
            $details = $ex->getMessageDetail();
            if (!empty($details))
                echo $ex->getMessageDetail().'<br/>';
        }
        echo '<div style="margin-left:30px">';
        if ($showStackTrace)
            TExceptionHandler::ShowStackTrace($ex);
        echo '</div>';
    }

    public static function GetInnerException($ex) {
        if ($ex instanceof INestedException)
            return $ex->getInnerException();
        return NULL;
    }

    public static function ShowAllExceptions(Exception $ex, $showDetails=false, $showStackTrace=false) {
        $inner = TExceptionHandler::GetInnerException($ex);
        if ($inner)
            $count = 1;
        else
            $count = 0;
        TExceptionHandler::ShowException($ex, $count, $showDetails, $showStackTrace);
        echo '<div style="margin-left:50px">';
        while ( $inner  )
        {
            TExceptionHandler::ShowException($inner, ++$count, $showDetails,  $showStackTrace);
            $inner = TExceptionHandler::GetInnerException($inner);
        }
        echo '</div>';
    }

    public static function GetAllExceptions(Exception $ex) {
        $result = array();
        while ( $ex )
        {
            array_push($result,$ex);
            $ex = TExceptionHandler::GetInnerException($ex);
        }
        return $result;
    }

    public static function HandleException($ex)
    {
        TExceptionHandler::$ExceptionHandlingEnabled = false;
        TExceptionHandler::$ErrorHandlingEnabled = false;
        $settings = TErrorSettings::Get();
        if ($settings & ErrorSetting::ShowErrors)
            TExceptionHandler::ShowAllExceptions(
                $ex,
                $settings & ErrorSetting::ShowDetail,
                $settings & ErrorSetting::ShowStackTrace);

        if ($settings & ErrorSetting::LogErrors) {
            $log =  TLog::GetLog('errors');

            foreach(TExceptionHandler::GetAllExceptions($ex) as $e) {
            // echo $e->getMessage();
                $log->write($e->getMessage(),$e->getMessageDetail(),$e->getTraceAsString());
             }
        }
        TExceptionHandler::$ExceptionHandlingEnabled = true;
        TExceptionHandler::$ErrorHandlingEnabled = true;
    }  //  HandleException

    /*****************************************************************
        Error handler which raises exceptions or displays/logs warnings
        on occurance of old syle PHP 4.x errors.

                                    7/1/2007 5:14PM
    *****************************************************************/
    public static function HandleError($errno, $errstr, $errfile, $errline)
    {
        TExceptionHandler::$ErrorHandlingEnabled = false;
        $severity = 0;
        $source = ErrorSource::Runtime;
        switch ($errno) {
            case E_USER_ERROR:
                $severity = ErrorSeverity::Recoverable;
                $source = ErrorSource::User;
                break;
             case E_ERROR :
                $severity = ErrorSeverity::Fatal;
                $source = ErrorSource::Runtime;
                break;
             case E_CORE_ERROR :
                $severity = ErrorSeverity::Fatal;
                $source = ErrorSource::Runtime;
                break;
             case E_RECOVERABLE_ERROR :
                $severity = ErrorSeverity::Recoverable;
                $source = ErrorSource::Runtime;
                break;
             case E_COMPILE_ERROR :
                $severity = ErrorSeverity::Fatal;
                $source = ErrorSource::Compiler;
                break;
             case E_PARSE :
                $severity = ErrorSeverity::Fatal;
                $source = ErrorSource::Parser;
                break;

            case E_STRICT :
                $severity = ErrorSeverity::StrictWarning;
                break;

             default:
                $severity = ErrorSeverity::Warning;

        }

        TExceptionHandler::$LastHandledErrorMessage = "$errstr ($errno)";
//        LAST_HANDLED_ERROR_MESSAGE = "$errstr ($errno)";
        switch($severity) {
            case  ErrorSeverity::Warning :
                $settings = TErrorSettings::Get();
                $showIt = ($settings & ErrorSetting::ShowWarnings);
                $logIt = ($settings & ErrorSetting::LogWarnings);
                break;

            case  ErrorSeverity::StrictWarning :
                $settings = TErrorSettings::Get();
                $showIt = ($settings & ErrorSetting::ShowStrictWarnings);
                $logIt = ($settings & ErrorSetting::LogStrictWarnings);
                break;

            default:
                TExceptionHandler::$ErrorHandlingEnabled = true;
                if (TExceptionHandler::$ExceptionHandlingEnabled)
                    throw new BaseException($errstr,'',0,$errno,$severity, $source);
        }


        $warning =  "WARNING: '$errstr' ($errno)";
        if (!empty($errfile))
            $warning .= " in $errfile ($errline)";
        if ($showIt)
            echo   $warning.'<br/>';
        if ($logIt)
            TLog::WriteWarning($warning);

        TExceptionHandler::$ErrorHandlingEnabled = true;
    }
}   // finish class TExceptionHandler

