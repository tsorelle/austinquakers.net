<?php
interface INestedException {
    function getInnerException();
}

interface IExtendedException {
    function getTypeCode();
    function getSeverity();
    function getErrorSource();
    function getMessageDetail();
}

/*****************************************************************
    `description`
                                7/1/2007 2:42PM
*****************************************************************/
class ErrorSeverity
{
    const Fatal = 4;
    const Recoverable = 3;
    const Warning =   2;
    const StrictWarning = 1;
    const Notice = 0;
}   // finish class ErrrorSeverity


/*****************************************************************
    `description`
                                7/1/2007 2:46PM
*****************************************************************/
class ErrorSource
{
    const Parser = 1;
    const Compiler = 2;
    const Runtime = 3;
    const User = 4;
}   // finish class ErrorSource


/*****************************************************************
    `description`
                                7/1/2007 5:24AM
*****************************************************************/
class BaseException extends Exception implements INestedException, IExtendedException
{
    private $innerException;
    private $typeCode;
    private $severity;
    private $errorSource;
    private $messageDetail;

    public function __construct($message, $messageDetail, $code=0, $typeCode=0,
        $severity = 0, $source = 0, $innerException = NULL) {

        $this->_typeCode =      $typeCode;
        $this->severity =       $severity;
        $this->source =         $source;
        $this->innerException = $innerException;
        $this->messageDetail  = $messageDetail;
        parent::__construct($message, $code);
    }

    public function getInnerException() {
        return $this->innerException;
    }

    public function getTypeCode() {
        return $this->typeCode;
    }

    public function getSeverity() {
        return $this->severity;
    }

    public function getErrorSource() {
        return $this->source;
    }

    public function getMessageDetail() {
        return $this->messageDetail;
    }


}   // finish class NestedException


/*****************************************************************
    `description`
                                7/1/2007 5:24AM
*****************************************************************/
class ApplicationException extends BaseException
{
    public function __construct($message, $inner = NULL) {
        parent::__construct($message,'',0,
            E_USER_ERROR,
            ErrorSeverity::Recoverable,
            ErrorSource::User,
            $inner);
    }
}   // finish class TopsException

/*****************************************************************
    `description`
                                7/1/2007 5:24AM
*****************************************************************/
class FatalException extends BaseException
{
    public function __construct($message, $inner = NULL) {
        parent::__construct($message,'', 0,
            E_USER_ERROR,
            ErrorSeverity::Fatal,
            ErrorSource::User,
            $inner);
    }
}   // finish class TopsException

/*****************************************************************
    `description`
                                7/1/2007 9:41PM
*****************************************************************/
class DatabaseException extends BaseException
{
    public function __construct(
        $message, $sqlErrorNo=0, $sqlStatement='',
        $severity=ErrorSeverity::Recoverable, $inner = NULL) {

        parent::__construct(
            $message,
            $sqlStatement,
            $sqlErrorNo,
            E_USER_ERROR,
            $severity,
            ErrorSource::User,
            $inner);
    }
}   // finish class DatabaseException

/*****************************************************************
    `description`
                                7/4/2007 9:02AM
*****************************************************************/
class TNoArgumentExeption extends BaseException
{
    public function __construct(
        $argName, $functionName,
        $severity=ErrorSeverity::Fatal,
        $inner = NULL) {

        parent::__construct(
            "Missing argument '$argName' in function $functionName.",
            '',
            0,
            E_USER_ERROR,
            $severity,
            ErrorSource::User,
            $inner);
    }

}   // finish class TNoArgumentExeption





