<?php

/** Class: TSqlQueryResult ***************************************
/// A collection of rows returned by TSqlQuery
*****************************************************************/
class TSqlQueryResult {
    private $result;
    private $ptr = - 1;
    private $row;
    private $rowCount = 0;

    public function __construct($result) {
        $this->result = $result;
        $this->rowCount = $this->result->num_rows;
    }

    public function next() {
        if ($this->rowCount == 0)
            return 0;
        $this->ptr++;
        if ($this->ptr >= ($this->rowCount)) {
            $this->freeResult();
            return 0;
        }
        else {
            $this->row = $this->result->fetch_array();
            return 1;
        }
    }
    //  next

    public function getRowCount() {
        return $this->rowCount;
    }

    public function getRow() {
        return $this->row;
    }

    //  getRow
    function get($fieldName) {
        return $this->row[$fieldName];
    }

    //  get
    function getFieldType($i) {
        return mysql_field_type($this->result, $i);
    }

    //  getFieldType
    function getFieldLength($i) {
        return mysql_field_len($this->result, $i);
    }

    //  getFieldLength
    function getFieldName($i) {
        return mysql_field_name($this->result, $i);
    }

    //  getFieldName
    function getFieldFlags($i) {
        return mysql_field_flags($this->result, $i);
    }

    //  getFieldName
    function getFieldCount() {
        return mysql_num_fields($this->result);
    }

    //  getFieldCount
    function noResult() {
        $this->ptr = - 1;
        $this->rowCount = 0;
    }

    //  noResult
    function freeResult() {
        if (isset ($this->result)) {
            $this->result->free();
            $this->result = null;
        }
    }

    //  release
    function hasColumn($fieldName) {
        return isset ($this->row[$fieldName]);
    }

    //  hasColumn
    function close() {
        $this->freeResult();
    }
    //  close

}

