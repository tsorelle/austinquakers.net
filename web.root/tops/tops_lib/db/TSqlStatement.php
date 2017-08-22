<?php
/** Class: TSqlStatement ***************************************/
/// Manages creation, binding and excecution of prepared Sql statements (mysqli)
/**
Prefer this class for any queries that require replaceable parameters.

All execute method such as ExecuteScaler, ExecuteQuery and ExecuteNotQuery
take optional parameters including the data type list followed by query
parameter values.

Example:<pre>
   $sql =
     'select c.categoryId, c.categoryName, c.categoryDescription '.
     'from categories c '.
     'join entitycategories e on e.categoryId = c.categoryId '.
     'and (e.entityId = ? and e.entityTypeId = ?) '.
     'where c.appliesTo = 0 or c.appliesTo = ? '.
     'order by c.categoryName';

   $statement = TSqlStatement::ExecuteQuery(
        [ query text ]
        $sql,
        [ parameter data types where i=integer, d=double, b-blob, s=other ]
        'iii',
        [ Parameter values ]
        $entityId, $this->entityTypeId, $this->entityTypeId);
</pre>

*****************************************************************/
class TSqlStatement
{

    /// $instance is a reference to the mysqli stmt object
    /**
    Direct access to the stmt instance is required for operations such
    as result binding, as in this example: <pre>

     $statement->instance->bind_result(
         $personId,
         $username,
         $firstName,
         $lastName);

    </pre>
    */

    /**
     * @var $instance mysqli
     */
    public $instance;
    private $lastErrorCode = 0;
    private $lastErrorMessage = '';

    public function __construct($statement)
    {
        // if ($statement == null)  trace('statement null');
        $this->instance = $statement;
    }  //  _construct

    private function checkError($result = null) {
        if ($result === true)
            $this->lastErrorCode = 0;
        else
            $this->lastErrorCode = $this->instance->errno;
        if ($this->lastErrorCode == 0) {
            $this->lastErrorMessage = '';
            return true;
        }
        $this->lastErrorMessage = $this->instance->error;
        return false;
    }

    public static function Init($databaseId='default') {
        $database = TDatabase::GetDatabase($databaseId);
        return $database->initStatement();
    }

    public static function Prepare($sql, $databaseId='default', $failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        return $database->PrepareSqlStatement($sql, $failureSeverity);
    }

    public static function Create($sql, $databaseId='default', $failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        $statement = $database->PrepareSqlStatement($sql, $failureSeverity);
        return new TSqlStatement($statement);
    }

    public function Execute($failureSeverity = ErrorSeverity::Recoverable) {
       $statement = $this->instance;
       TSqlStatement::ExecuteStatement($statement, $failureSeverity);
    }

    public function getRowCount() {
        return $this->instance->num_rows;
    }

    public function Next($failureSeverity = ErrorSeverity::Recoverable) {
        try {
            $result = $this->instance->fetch();
        }
        catch (Exception $ex) {
            throw new DatabaseException(
                'SQL Query statement failed.',
                0, $ex.GetMessage,
                $failureSeverity, $ex);
        }
        if ($result === false)
            throw new DatabaseException(
                'SQL statement Fetch command failed.',
                0, $this->instance->error,
                $failureSeverity);
        return ($result != null);
    }

    public static function ExecuteStatement($statement, $failureSeverity = ErrorSeverity::Recoverable) {

        try {
            $result = $statement->execute();
            return $result;
        }
        catch (Exception $ex) {
            throw new DatabaseException(
                'SQL Query statement failed.',
                0, $ex.GetMessage,
                $failureSeverity, $ex);
        }
        $error = $statement->errno;
        if ($error)
            throw new DatabaseException(
                'SQL Query statement failed.',
                $error,
                $statement->error,
                $failureSeverity);
    }

    public static function BindArgs($statement, $argCount, $paramtypes, $args) {
        $offset =  $argCount - strlen($paramtypes);
        if ($offset > 0) {
            for ( $i=0; $i<$offset; $i++ )
                array_shift($args);
            $argCount -= $offset;
        }

        if ($argCount == 1) {
            $statement->bind_param($paramtypes,$args[0]);
        }
        else if ($argCount == 2) {
            $statement->bind_param($paramtypes,$args[0],$args[1]);
        }
        else if ($argCount == 3) {
            $statement->bind_param($paramtypes,$args[0],$args[1],$args[2]);
        }
        else if ($argCount == 4) {
            $statement->bind_param($paramtypes,$args[0],$args[1],$args[2],$args[3]);
        }
        else if ($argCount == 5) {
            $statement->bind_param($paramtypes,$args[0],$args[1],$args[2],$args[3],$args[4]);
        }
        else if ($argCount > 6) {
            $argStr = '';
            foreach($args as $arg) {
                $argStr .= ',$args['.$i.']';
            }
            $s = '$statement->bind_param('."'$paramtypes'$argStr);";
            eval($s);
        }
    }


    /// Execeute sql and return a Statement object.
    /**
    Example: <pre>
        $sql =
               'select c.categoryId, c.categoryName, c.categoryDescription '.
               'from categories c where c.appliesTo = 0 or c.appliesTo = ? '.
               'order by c.categoryName';

       $statement = TSqlStatement::ExecuteQuery($sql,'i',$entityTypeId);

       $statement->instance->bind_result(
             $catId,
             $name,
             $description);

        while($statement->next()) {
             [do somenting with returned row values bound to variables:
              $catId,  $name, $description]
        }

    </pre>
    **/

    public static function ExecuteDrupalQuery($sql) {
        $database = TDatabase::GetDatabase('drupal');
        $statement = $database->PrepareSqlStatement($sql);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }
        TSqlStatement::ExecuteStatement($statement);
        $statement->store_result();

        return new TSqlStatement($statement);
    }

    public static function ExecuteQuery($sql)  {

        $database = TDatabase::GetDatabase();
        $statement = $database->PrepareSqlStatement($sql);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }
        TSqlStatement::ExecuteStatement($statement);
        $statement->store_result();

        return new TSqlStatement($statement);
    }


    public static function ExecuteScaler($sql) { // , $paramtypes)  {
        $database = TDatabase::GetDatabase();
        $statement = $database->PrepareSqlStatement($sql);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }
        TSqlStatement::ExecuteStatement($statement);
        $statement->bind_result($resultValue);
        if (!$statement->fetch())
            return false;
        return $resultValue;
   }

    public static function ExecuteScalerForDrupal($sql) { // , $paramtypes)  {
        $database = TDatabase::GetDatabase('drupal');
        $statement = $database->PrepareSqlStatement($sql);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }
        TSqlStatement::ExecuteStatement($statement);
        $statement->bind_result($resultValue);
        if (!$statement->fetch())
            return false;
        return $resultValue;
    }



    public static function VerifyDatabase($dbName, $testTableName) {
       $sql = "select count(*) from $testTableName";
        $database = TDatabase::GetDatabase($dbName);
        $statement = $database->PrepareSqlStatement($sql);
        TSqlStatement::ExecuteStatement($statement);
        $statement->bind_result($resultValue);
        if (!$statement->fetch())
            return false;
        return ($resultValue > -1);
   }



    public static function ExecuteNonQuery($sql)  {
        $database = TDatabase::GetDatabase();
        $statement = $database->PrepareSqlStatement($sql);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }
        TSqlStatement::ExecuteStatement($statement);
        return $statement->affected_rows;
   }

       public static function ExecuteNonQueryToDrupal($sql)  {
        $database = TDatabase::GetDatabase('drupal');
        $statement = $database->PrepareSqlStatement($sql);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }
        TSqlStatement::ExecuteStatement($statement);
        return $statement->affected_rows;
   }

 /* not working
    public static function GetObject($sql)  {

        $database = TDatabase::GetDatabase();
        $statement = $database->PrepareSqlStatement($sql);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }
        TSqlStatement::ExecuteStatement($statement);

        $result = $statement->store_result();
      //  return $statement->fetch();
//        return $result;
        return mysqli_fetch_object($result);
    }
*/


}   // finish class TSqlStatement


