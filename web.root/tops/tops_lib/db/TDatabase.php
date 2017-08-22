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
class TDatabase
{
    private $lastError;
    private $lastErrorMessage;

    private $databasename;
    private $username;
    private $password;
    private $hostname;
    private $sql;
    private $connected = false;

    /**
     * @var mysqli
     */
    private $mysqli;

    private static $instances = Array();

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

    }

    public function GetLink() {
        $this->connect();
        return $this->mysqli;
    }

    private function checkError() {
        $this->lastError = $this->mysqli->errno;
        if (!$this->lastError) {
            $this->lastErrorMessage = '';
            return 0;
        }
        $this->lastErrorMessage = $this->mysqli->error;
        return $this->lastError;
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

    private function connect()  {
        $this->clearErrors();
        if (!$this->connected) {
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
            $this->connected = ($this->lastError == 0);
        }
        return $this->connected;
    }    //  connect

    public function CloseConnection()
    {
        if ($this->connected) {
            $this->mysqli->close();
            unset($this->mysqli);
            $this->connected = false;
        }
    }  //  Close

    public function TestConnection() {
        return $this->connect();
    }

    public static function TestDatabase($databaseId='default') {
        $db = TDataBase::GetDatabase($databaseId);
        return $db->TestConnection();
    }

    public function tableExists($tableName)
    {
        $this->connect();
        $Table = $this->mysqli->query("show tables like '" .$tableName . "'");
         if($Table->fetch_row() == NULL)
            return(false);
         return(true);
    }  //  tableExists

    private function formatErrorMessage($sql = '') {
       $result = "SQL ERROR $this->lastErrorMessage ($this->lastError) ";
       if (!empty($sql))
            $result .= 'Query: '.$sql;
       return $result;
    }

    public function ExecuteCommand($sql, $failureSeverity = ErrorSeverity::Recoverable)
    {
        if (empty($sql))
            throw new NoArgumentException('sql','TDatabase::Execute');

        if ($this->connect()) {
           $this->clearErrors();
           try {
                $this->mysqli->query($sql);
                if ($this->checkError() == 0)
                    return $this->mysqli->affected_rows;
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

    public function ExecuteQuery($sql, $failureSeverity = ErrorSeverity::Recoverable)
    {
        if ($this->connect()) {
            $this->clearErrors();
            try {
                return $this->mysqli->query($sql);
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

    public function ExecuteScalar($sql, $returnValue = 0, $failureSeverity = ErrorSeverity::Recoverable)
    {
        $result = $this->ExecuteQuery($sql);
        try {
            if ($result->num_rows > 0) {
                $row = $result->fetch_array();
                $returnValue = $row[0];
            }
           $result->free();
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

    public function ExecuteInsertCommand($sql, $failureSeverity = ErrorSeverity::Recoverable) {
        $rowCount = $this->ExecuteCommand($sql, $failureSeverity);
        if ($rowCount) {
            return $this->ExecuteScalar("SELECT LAST_INSERT_ID()",-1, $failureSeverity);
        }
        return -1;
    }

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

    /** Static Methods **/

    public static function GetDatabase($databaseId='default')
    {
        global $USE_TEST_DATABASE;
        global $DEFAULT_DATABASE_ID;
        if ($USE_TEST_DATABASE)
            $databaseId = 'test';
        else if ($databaseId == 'default' && isset($DEFAULT_DATABASE_ID))
            $databaseId = $DEFAULT_DATABASE_ID;

        if (isset( TDatabase::$instances[$databaseId] ))
            return TDatabase::$instances[$databaseId];

        $db = TDatabase::CreateDatabase($databaseId, false);
        TDatabase::$instances[$databaseId] = $db;
        return $db;
    }

    public function getDatabaseName() {
        return $this->databasename;
    }

    public static function CreateDatabase($databaseId='default', $checkForTest = true) {
        if ($checkForTest) {
            global $USE_TEST_DATABASE;
            if ($USE_TEST_DATABASE)
                $databaseId = 'test';
        }

        $configuration =  TConfiguration::GetDatabaseSettings();
        $databaseAlias = $configuration->getValue('database',$databaseId);
        if (empty($databaseAlias))
            throw new FatalException("Database for '$databaseId' is not defined in [database] section of configuration file.");
        $databasename = $configuration->getValue($databaseAlias,'database');
        if (empty($databasename)) {
            throw new FatalException("Database name for '$databaseAlias' not found in configuration file.");
        }
        $hostname = $configuration->getValue($databaseAlias,'host','localhost');
        $username = $configuration->getValue($databaseAlias,'username',$databasename);
        $password = $configuration->getValue($databaseAlias,'password');

        return new TDatabase($hostname, $databasename, $username, $password);
    }

    public static function Close($databaseId='default') {
        if (isset( TDatabase::$instances[$databaseId] )) {
            $database =  TDatabase::$instances[$databaseId];
            $database->CloseConnection();
            unset(TDatabase::$instances[$databaseId]);
        }
    }

    public static function ReturnLastInsertId($databaseId='default',$failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        return $database->getLastInsertId();
    }

    public static function DropTable($tableName,$databaseId='default',$failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        if ($database->tableExists($tableName)) {
             $database->ExecuteCommand("DROP TABLE $tableName", $failureSeverity);
        }
    }

}   // finish class TDatabase


