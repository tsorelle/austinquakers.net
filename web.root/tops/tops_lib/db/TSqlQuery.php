<?php
/** Class: TSqlQuery ***************************************/
/// Execute a query statement and return one or more rows..
/**
Prefer TSqlStatement if replaceable parameters are needed.
*****************************************************************/
class TSqlQuery
{
    public static function Execute($sql, $databaseId='default', $failureSeverity = ErrorSeverity::Recoverable) {
        $database = TDatabase::GetDatabase($databaseId='default');
        $result = $database->ExecuteQuery($sql, $failureSeverity);
        return new TSqlQueryResult($result);
    }
} // finish class TSqlQuery

