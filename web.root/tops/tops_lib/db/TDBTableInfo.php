<?php
/** Class: TDBTableInfo ***************************************
/// retrieve table schema information for use by code generators
*****************************************************************/
class TDBTableInfo {
    private $fieldCount;
    private $fields;
    private $fieldTypes;
    private $tableName;

    function tableExists() {
        $db = TDatabase::CreateDatabase();
        $dbname = $db->getDatabaseName();
        $qry = TSqlStatement::ExecuteQuery("show tables where tables_in_$dbname = ?", 's', $this->tableName);
        return $qry->Next();
    }

    function setTable($tableName) {
        TTracer::Trace($tableName);
        $this->tableName = $tableName;
        // $this->connect();
        if (!$this->tableExists($tableName)) {
            trigger_error('Table ' . $tableName . ' does not exist.', E_USER_WARNING);
            return false;
        }
        $this->fields = array();
        $this->fieldCount = 0;
        $this->fieldTypes = array();
        $qry = TSqlStatement::ExecuteQuery("show columns from $tableName");
        $qry->instance->bind_result($field, $type, $null, $key, $default
            , $extra );
        while ($qry->Next()) {
            $i = $this->fieldCount;
            $this->fieldCount++;
            $this->fields[$i] = $field;
            if (strstr($type, 'varchar') || strstr($type, 'text'))
                $type = 'string';
            else
                if (strstr($type, 'int'))
                    $type = 'int';
                $this->fields[$i] = $field;
            $this->fieldTypes[$i] = $type;
        }
        return true;
    }

    //  setTable
    function getFieldCount() {
        return $this->fieldCount;
    }

    //  getFieldCount
    function getFields() {
        return $this->fields;
    }

    //  getFieldNames
    function getFieldName($index) {
        return $this->fields[$index];
    }

    //  getFieldName
    function getFieldType($index) {
        return $this->fieldTypes[$index];
    }
    //  getFieldName

}

