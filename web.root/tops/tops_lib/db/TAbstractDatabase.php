<?php
/** Class: TDatabase ***************************************/
///     TDatabase manages MySql connections, executes requests and handles errors.
/**
* Used by all data access classes in tops_lib/db
* Depends on database.ini configuration file in site_lib/config. Example: <pre>
[database]
default=quip
test=quip_test
logging=quip

[quip]
host=localhost
database=quiporg
username=quiporg
password=p@ssw0rd
</pre>
*****************************************************************/
abstract class TAbstractDatabase
{
    protected $lastError;
    protected $lastErrorMessage;

    protected $databasename;
    protected $username;
    protected $password;
    protected $hostname;
    protected $sql;
    protected $connected = false;

    abstract protected function doConnect();
    abstract protected function getSingleRow($sql, $args=null);
    abstract protected function getSingleObject($sql, $args=null);
    abstract protected function getSingleValue($sql, $defaultValue, $args);
    abstract protected function checkTableExists($tableName);
    abstract protected function doExecuteCommand($sql, $args = false);
    abstract protected function doExecuteQuery($sql, $args=false);
    abstract protected function selectObjects($sql, $args=false);
    abstract protected function getInsertedId();

    public function __construct(
        $hostname='localhost', $databasename, $username, $password) {

        if (empty($databasename))
            throw new TNoArgumentException('databasename','TDatabase constructor');
        if (empty($username))
            throw new TNoArgumentException('username','TDatabase constructor');
        if (empty($password))
            throw new TNoArgumentException('password','TDatabase constructor');

        $this->databasename = $databasename;
        $this->username =     $username;
        $this->password =     $password;
        $this->hostname =     $hostname;
        $this->connect();

    }

    public function failed() {
        return ($this->lastError > 0);
    }

    public function getLastErrorMessage() {
        return $this->lastErrorMessage;
    }

    private function clearErrors() {
        $this->lastError = 0;
        $this->lastErrorMessage = '';
    }

    protected function connect()  {
        $this->clearErrors();
        if (!$this->connected) {
            $this->connected = ($this->doConnect());
            /*
            $this->mysqli = @mysqli_connect($this->hostname,$this->username,$this->password);

            if ($this->mysqli) {
                $this->mysqli->select_db($this->databasename);
                $this->checkError();
                if ($this->lastError != 0)
                    throw new DatabaseException(
                        "Cannot select database", $this->lastError,
                        $this->formatErrorMessage().
                        " Database name: $this->databasename on $this->hostname",
                        ErrorSeverity::Fatal);
            }
            else {
                $this->lastError = mysqli_connect_errno();
                $this->lastErrorMessage = mysqli_connect_error();
                throw new DatabaseException(
                    'Cannot connect to database.', $this->lastError,
                     $this->formatErrorMessage().
                    " Connection parameters: $this->hostname; $this->databasename; $this->username; $this->password",
                    ErrorSeverity::Fatal);
            }

            */
        }
        return $this->connected;
    }    //  connect

    public function CloseConnection()
    {
        if ($this->connected) {
            $this->connected = false;
        }
    }  //  Close

    public function TestConnection() {
        $this->connect();
        return $this->connected;
    }

    public static function TestDatabase($databaseId='default') {
        $db = TDataBase::GetDatabase($databaseId);
        return $db->TestConnection();
    }

    public function tableExists($tableName)
    {
        $this->connect();
        /*
        $Table = $this->mysqli->query("show tables like '" .$tableName . "'");
         if($Table->fetch_row() == NULL)
            return(false);
         */
         return($this->checkTableExists($tableName));
    }  //  tableExists

    public function formatErrorMessage($sql = '') {
       $result = "SQL ERROR $this->lastErrorMessage ($this->lastError) ";
       if (!empty($sql))
            $result .= 'Query: '.$sql;
       return $result;
    }


    public function ExecuteCommand($sql)
    {
        $failureSeverity = ErrorSeverity::Recoverable;
        if (empty($sql))
            throw new NoArgumentException('sql','TDatabase::Execute');

        if ($this->connect()) {
           $this->clearErrors();
           try {
                $args = false;
                if (func_num_args() > 1) {
                    $args = func_get_args();
                    array_shift($args);// skip sql
                }
               return $this->doExecuteCommand($sql, $args);
               /*
                $this->mysqli->query($sql);
                if ($this->checkError() == 0)
                    return $this->mysqli->affected_rows;
                */
            }
            catch (Exception $ex) {
                throw new DatabaseException(
                    'SQL Command failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity, $ex);
            }
            if ($this->lastError != 0)
                throw new DatabaseException(
                    'SQL Command failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity);
        }
        return 0;
    }

    public function ExecuteQuery($sql)
    {
        $failureSeverity = ErrorSeverity::Recoverable;
        if ($this->connect()) {
            $this->clearErrors();
            try {
                $args = false;
                if (func_num_args() > 1) {
                    $args = func_get_args();
                    array_shift($args);// skip sql
                }
                return $this->doExecuteQuery($sql, $args);
                // return $this->mysqli->query($sql);
            }
            catch (Exception $ex) {
                throw new DatabaseException(
                    'SQL Query failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity, $ex);
            }
            if ($this->lastError != 0)
                throw new DatabaseException(
                    'SQL Query failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity);
        }
    }

    public function Find($sql) {

        $failureSeverity = ErrorSeverity::Recoverable;
        if ($this->connect()) {
            $this->clearErrors();
            try {
                $args = false;
                if (func_num_args() > 1) {
                    $args = func_get_args();
                    array_shift($args);// skip sql
                }
                return $this->getSingleObject($sql, $args);
                // return $this->mysqli->query($sql);
            }
            catch (Exception $ex) {
                throw new DatabaseException(
                    'SQL Query failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity, $ex);
            }
            if ($this->lastError != 0)
                throw new DatabaseException(
                    'SQL Query failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity);
        }
    }

    public function Select($sql)
    {
        $failureSeverity = ErrorSeverity::Recoverable;
        if ($this->connect()) {
            $this->clearErrors();
            try {
                $args = false;
                if (func_num_args() > 1) {
                    $args = func_get_args();
                    array_shift($args);// skip sql
                }
                return $this->selectObjects($sql, $args);
                // return $this->mysqli->query($sql);
            }
            catch (Exception $ex) {
                throw new DatabaseException(
                    'SQL Query failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity, $ex);
            }
            if ($this->lastError != 0)
                throw new DatabaseException(
                    'SQL Query failed.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    $failureSeverity);
        }
    }

    public function FindRow($sql, $args) {
        try {
            $failureSeverity = ErrorSeverity::Recoverable;
                $args = false;
                if (func_num_args() > 1) {
                    $args = func_get_args();
                    array_shift($args);// skip sql
                }

            $returnValue = $this->getSingleRow($sql,$args);
        }
        catch (Exception $ex) {
            throw new DatabaseException(
                'SQL Query failed.',
                $this->lastError,
                $this->formatErrorMessage($sql),
                $failureSeverity, $ex);
        }
        if ($this->lastError != 0)
            throw new DatabaseException(
                'SQL Query failed.',
                $this->lastError,
                $this->formatErrorMessage($sql),
                $failureSeverity);

        return $returnValue;

    }

    // renamed for backward compatibility
    public function ExecuteScalar($sql, $defaultValue) {
        return self::FindValue($sql,$defaultValue);
    }

    public function FindValue($sql, $defaultValue = false)
    {
        try {
            $failureSeverity = ErrorSeverity::Recoverable;
                $args = false;
                if (func_num_args() > 2) {
                    $args = func_get_args();
                    array_shift($args);// skip sql
                    array_shift($args);// skip default
                }

            $returnValue = $this->getSingleValue($sql,$defaultValue,$args);
            /*
            if ($result->num_rows > 0) {
                $row = $result->fetch_array();
                $returnValue = $row[0];
            }
           $result->free();
           */
        }
        catch (Exception $ex) {
            throw new DatabaseException(
                'SQL Query failed.',
                $this->lastError,
                $this->formatErrorMessage($sql),
                $failureSeverity, $ex);
        }
        if ($this->lastError != 0)
            throw new DatabaseException(
                'SQL Query failed.',
                $this->lastError,
                $this->formatErrorMessage($sql),
                $failureSeverity);

        return $returnValue;

    }  //  returnValue

    public function getLastInsertId($failureSeverity = ErrorSeverity::Recoverable)
    {
        return $this->ExecuteScalar("SELECT LAST_INSERT_ID()",-1, $failureSeverity);
    }  //  getLastInsertId

    public function ExecuteInsertCommand($sql, $args = null) {
        $rowCount = $this->ExecuteCommand($sql, $args);
        if ($rowCount) {
            return $this->getInsertedId();
            //$this->ExecuteScalar("SELECT LAST_INSERT_ID()",-1, $failureSeverity);
        }
        return -1;
    }

    /*
    public function InitStatement($failureSeverity = ErrorSeverity::Recoverable)
    {
        if ($this->connect())
            return $this->mysqli->stmt_init();
    }  //  getStatement

    public function PrepareSqlStatement($sql)
    {
        // trace($sql);
        if ($this->connect())  {
            $this->clearErrors();
            $statement = $this->mysqli->prepare($sql);
            if ($this->checkError())
                throw new DatabaseException(
                    'Cannot prepare query.',
                    $this->lastError,
                    $this->formatErrorMessage($sql),
                    ErrorSeverity::Fatal);
        }
        return $statement;
    }
    */

    public function getDatabaseName() {
        return $this->databasename;
    }



}   // finish class TDatabase


