<?php
/** Class: TLookupTableManager ***************************************/
///
/**
*****************************************************************/
class TLookupTableManager
{
    public function __construct() {
    }

    public function __toString() {
        return 'TLookupTableManager';
    }

    /**
     * Deprected use getLookupList
     *
     * @param $tableName
     * @param $valueField
     * @param $textField
     * @param string $titleField
     * @param bool $filterActive
     * @param null $orderBy
     * @return array
     * @throws DatabaseException
     */
    protected function getList($tableName, $valueField, $textField, $titleField = 'null' , $filterActive = false, $orderBy = null) {
        $result = array();
        $sql  = "select $valueField as value, $textField as displayText, $titleField as title";
        $sql .= " from $tableName ";
        if (!empty($filterActive))
            $sql .= ' where active = 1';
        if (!empty($orderBy))
            $sql .= ' ORDER BY '.$orderBy;
        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($value,$text,$title);
        while($statement->next()) {
            $item = new stdclass();
            $item->text = $text;
            $item->value = $value;
            if (!empty($title))
                $item->title = $title;
            array_push($result,$item);
        }
        return $result;
    }

    /**
     * Same as above but returns a typed DTO.
     *
     * @param $tableName
     * @param $valueField
     * @param $textField
     * @param string $titleField
     * @param bool $filterActive
     * @param null $orderBy
     * @return LookupListItem[]
     * @throws DatabaseException
     */
    protected function getLookupList($tableName, $valueField, $textField, $titleField = 'null' , $filterActive = false, $orderBy = null) {
        $result = array();
        $sql  = "select $valueField as value, $textField as displayText, $titleField as title";
        $sql .= " from $tableName ";
        if (!empty($filterActive))
            $sql .= ' where active = 1';
        if (!empty($orderBy))
            $sql .= ' ORDER BY '.$orderBy;
        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($value,$text,$title);
        while($statement->next()) {
            $item = new LookupListItem();
            $item->text = $text;
            $item->value = $value;
            if (!empty($title))
                $item->title = $title;
            array_push($result,$item);
        }
        return $result;
    }
}
// end TLookupTableManager