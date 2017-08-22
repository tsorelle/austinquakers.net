<?php
/** Class: TSqlCommand ***************************************/
/// Executes sql text against database selected by id. Uses mysqli
/**
For SQL with replaceable parameters, prefer TSqlStatement.
*****************************************************************/
class TSqlCommand
{
    /// Execute a query that does not return a result
    public static function Execute($sql, $databaseId='default', $failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        $database->ExecuteCommand($sql, $failureSeverity);
    }

    /// Excecute and insert statement
    public static function ExecuteInsert($sql, $databaseId='default', $failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        return $database->ExecuteInsertCommand($sql, $failureSeverity);
    }

    /// Execute a query that returns a single value.  Zero return as default.
    public static function GetValue($sql, $databaseId='default', $failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        return $database->ExecuteScalar($sql, 0, $failureSeverity);
    }

    /// Excecute a query that returns a single string value. Empty string is the default.
    public static function GetString($sql, $databaseId='default', $failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId);
        $database->ExecuteScalar($sql, '', $failureSeverity);
    }

}   // finish class TSqlCommand


