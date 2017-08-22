<?php
/*****************************************//**
Error handling intitialization to be included in
startup scripts.
************************************************/

require_once ("tops_lib/sys/TErrorSettings.php");
require_once ("tops_lib/sys/TExceptionHandler.php");

/// default handler for un-caught exceptions
function tops_exception_handler($ex) {
    if (TExceptionHandler::$ExceptionHandlingEnabled)
        TExceptionHandler::HandleException($ex);
    else
        echo '<p><b>Error in exception handler:</b> ' . $ex->getMessage() . '</p>';
}

/// Error handler passes errors to the excepton handler.
function tops_error_handler($errno, $errstr, $errfile, $errline) {
    if (TExceptionHandler::$ErrorHandlingEnabled)
        TExceptionHandler::HandleError($errno, $errstr, $errfile, $errline);
    else
        echo "<p><b>Error in error handler:</b> $errstr ($errno) in $errfile: $errline</p>";
}

///  Used to disable error handling
function null_error_handler() {
    // ignore all errors
}
//  null_error_handler

set_exception_handler('tops_exception_handler');
// set_error_handler('tops_error_handler');

