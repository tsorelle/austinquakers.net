<?php

/** Class: TPdoDatabase ***************************************/
///     TDatabase manages MySql connections, executes requests and handles errors.
/**
*****************************************************************/
class TPdoDatabase extends TAbstractDatabase
{
    private static $instances = Array();

    private $pdoInstance;
    private $lastRowCount;
    private $lastInsertedId;

    private function setPdoError($errorInfo) {
        if ($errorInfo) {
TTracer::ShowArray($errorInfo);
            $this->lastError = $errorInfo[1];
            $this->lastErrorMessage = $errorInfo[2];
        }
        else {
            TTracer::Trace('NO error info');
            $this->lastError = 0;
            $this->lastErrorMessage = '';
        }
        return (empty($this->lastError));
    }

      public function GetPdoInstance() {
          $this->connect();
          return $this->pdoInstance();
      }

      protected function doConnect() {
        try {
            $this->pdoInstance = new PDO(
                'mysql:host='.$this->hostname.';dbname='.$this->databasename
                , $this->username,$this->password);
            $this->lastError = 0;
            $this->lastErrorMessage = '';
            return true;
        }
        catch (PDOException $e) {
            $this->setPdoError($e->errorInfo);
            throw new DatabaseException(
                'Cannot connect to database.', $this->lastError,
                 $this->formatErrorMessage().
                " Connection parameters: $this->hostname; $this->databasename; $this->username; $this->password",
                ErrorSeverity::Fatal);
        }
      }

    protected function getSingleObject($sql, $args=null) {
            $stmt = $this->executePdoQuery($sql,$args);
            if ($stmt === false)
                return false;
          return $stmt->fetch(PDO::FETCH_OBJ);
      }


      protected function getSingleRow($sql,$args = false) {
        if ($args && sizeof($args) > 0) {
            $stmt = $this->executePdoQuery($sql,$args);
            if ($stmt === false)
                return false;
          }
          else
            $stmt = $this->pdoInstance->query($sql);
          if ($stmt === false)
            return false;
          return $stmt->fetch();
      }

      protected function checkTableExists($tableName) {
          $result = $this->getSingleRow("show tables like '" .$tableName . "'");
          if ($result === false)
            return false;
          return true;
        }


        protected function doExecuteCommand($sql, $args=false) {
            $this->connect();
          if ($args && sizeof($args) > 0) {
                $stmt = $this->pdoInstance->prepare($sql);
                if ($stmt->execute($args)) {
                    $this->lastRowCount = $stmt->rowCount();
                    $this->lastInsertedId = $this->pdoInstance->lastInsertId();
                    $count = $this->lastRowCount;
                    // TTracer::Trace('Affected: '.$count);
                    return $count;
                }
                else {
                    $this->setQueryFailedError($sql);
                    return 0;
                }

          }
          else {
                $count = $this->pdoInstance->exec($sql);
                if ($count === false) {
                      $this->setQueryFailedError($sql);
                      return -1;
                }
            }
        }

        private function setQueryFailedError($sql) {
              $this->lastError = 1;
              $this->lastErrorMessage = "Query failed: $sql";
              $this->lastRowCount = 0;
              $this->lastInsertedId = 0;
        }


        private function executePdoQuery($sql, $args = false) {
            $stmt = $this->pdoInstance->prepare($sql);
            if (empty($args))
                $result = $stmt->execute();
            else
                $result = $stmt->execute($args);
            if ($result)
                return $stmt;
            $this->setQueryFailedError($sql);
            return false;
        }

        protected function getInsertedId() {
            return $this->lastInsertedId;
        }

        protected function selectObjects($sql, $args=false) {
            $stmt = $this->executePdoQuery($sql,$args);
            if ($stmt === false)
                return array();

            $result = $stmt->fetchAll(PDO::FETCH_CLASS, 'stdClass');

            if ($result !== false)
                return $result;

            setQueryFailedError($sql);
            return array();

        }


        protected function doExecuteQuery($sql, $args = false) {

          if ($args && sizeof($args) > 0) {
            $stmt = $this->executePdoQuery($sql,$args);
            if ($stmt === false)
                return array();

            $result = $stmt->fetchAll();
            if ($result !== false)
                return $result;

            setQueryFailedError($sql);
            return array();
          }

          $stmt = $this->pdoInstance->query($sql);
          if ($stmt === false) {
              $this->setQueryFailedError($sql);
              return array();
          }
          $this->lastRowCount = $stmt->rowCount();
          return $stmt;
        }

        protected function getSingleValue($sql,$default,$args) {

            $row = $this->getSingleRow($sql,$args);
            if ($row === false)
                return $default;
            return $row[0];
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

        if (isset( TPdoDatabase::$instances[$databaseId] ))
            return TPdoDatabase::$instances[$databaseId];

        $db = self::CreateDatabase($databaseId, false);
        self::$instances[$databaseId] = $db;
        return $db;
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

        return new TPdoDatabase($hostname, $databasename, $username, $password);
    }

    public static function CreatePDO($databaseId='default', $checkForTest = true) {
        $database = self::CreateDatabase();
        return $database->getPdoInstance();
    }

    public static function Close($databaseId='default') {
        if (isset( self::$instances[$databaseId] )) {
            $database =  self::$instances[$databaseId];
            $database->CloseConnection();
            unset(self::$instances[$databaseId]);
        }
    }

    public static function ReturnLastInsertId($databaseId='default',$failureSeverity = ErrorSeverity::Recoverable) {
        $database = self::GetDatabase($databaseId);
        return $database->getLastInsertId();
    }

    public static function DropTable($tableName,$databaseId='default',$failureSeverity = ErrorSeverity::Recoverable) {
        $database = self::GetDatabase($databaseId);
        if ($database->tableExists($tableName)) {
             $database->ExecuteCommand("DROP TABLE $tableName", $failureSeverity);
        }
    }




}   // finish class TPdoDatabase


