<?php
require_once("tops_lib/sys/TDateTime.php");
require_once('tops_lib/db/TDatabase.php');

define ("INT_FIELD",        'i');
define ("DOUBLE_FIELD",     'd');
define ("BLOB_FIELD",       'b');
define ("OTHER_FIELD",      's');

define ("ID_FIELD",         'id');
define ("DATESTAMP_FIELD",  'ds');
define ("USERSTAMP_FIELD",  'us');
define ("CREATEDATE_FIELD", 'cd');
define ("CREATEUSER_FIELD", 'cu');

// for backward compatibility
define ("BOOLEAN_FIELD",    'i');
define ("STRING_FIELD",     's');
define ("DATE_FIELD",       's');
define ("DATETIME_FIELD",   's');

TTracer::On('entity');

class TEntityField {
    public $fieldName;
    public $value;
    public $modified = false;
    public $assigned = false;
    public $fieldType;

    public function __construct($name, $type) {
        $this->fieldName = $name;
        $this->fieldType = $type;
    }

    public function __toString() {
        return $this->$value;
    }


    public function assign($value)
    {
        $this->value = $value;
        $this->assigned = true;
        $this->modified = false;
    }  //  assign

    public function setValue($value)
    {
        if ($this->value != $value || !$this->assigned) {
            $this->value = $value;
            $this->modified = true;
            $this->assigned = true;
        }
    }  //  setValue

    public function getValue() {
      return $this->value;
    }

    public function setModified()
    {
        $this->modified = true;
        $this->assigned = true;
    }  //  setModified

    public function setFieldName($value) {
      $this->fieldName = $value;
    }
}

class TEntityObject {
    private $fields = Array();
    private $id;
    protected $isValid = true;
    protected $tableName;
    protected $idFieldName;

    public function setId($value)
    {
        $this->id = $value;
    }  //  setId

    public function getId()
    {
        return $this->id;
    }  //  setId


    public function addField($fieldName,$fieldType) {
        //TTracer::Trace("Adding $fieldName");
        $this->fields[$fieldName] = new TEntityField($fieldName,$fieldType);
    }

    protected function getFieldValue($aName) {
        return $this->fields[$aName]->getValue();
    }

    protected function setFieldValue($aName,$value) {
        $this->fields[$aName]->setValue($value);
    }

    protected function setDateFieldValue($aName,$value) {
        if (empty($value)) {
           $value = null;
        }
        else if (is_string($value)) {
            $time = strtotime($value);
            $value = date('Y-m-d',$time);
        }
        $this->fields[$aName]->setValue($value);
    }

    protected function formatDate($value, $format = 'm/d/Y') {
        if (empty($value))
            return '';
        return date($format,strtotime($value));
    }

    protected function getDate($aName, $format = 'm/d/Y') {
        $f = $this->fields[$aName];
        return $this->formatDate($f->value, $format);
    }

    protected function initFieldValue($aName,$value) {
        $this->fields[$aName]->assign($value);
    }

    private function getSelectResultBindings() {
        $result = '$statement->bind_result(';
        $count = 0;
        foreach($this->fields as $f) {
            if (++$count > 1)
                $result .= ',';
            $result .= '$this->fields['."'$f->fieldName']->value";
        }
        return $result;
    }

    private function getBindingString($fieldName)
    {
        return '$this->fields['."'$fieldName']->value";
    }  //  getBindingString

    private function getSelectStrings(&$selectQuery, &$resultBindings)
    {
        $fieldList = $this->idFieldName;
        $resultBindings = '$this->id';
        foreach($this->fields as $f) {
            $fieldList .= ',';
            $resultBindings .= ',';
            $fieldList .= $f->fieldName;
            $resultBindings .= $this->getBindingString($f->fieldName);
        }
        $selectQuery = "SELECT $fieldList FROM $this->tableName WHERE ";
    }  //  getSelectStrings


    private function executeSelection($statement, $resultBindings) {
        TSqlStatement::ExecuteStatement($statement);
        eval('$statement->bind_result('.$resultBindings.');');
        $statement->fetch();
        $statement->close();
    }

    private function executeCommand($sql, $bindingTypes, $paramBindings, $returnInsertId = false) {
        $username = TAuthentication::GetCurrentUserName();
        $test = 'testuser';
        $statement = TSqlStatement::Prepare($sql);
        $bindStatement = '$statement->bind_param('."'$bindingTypes',".$paramBindings.');';
//        TTracer::Trace("bindStatemwnt = $bindStatement");
        eval($bindStatement);
        TSqlStatement::ExecuteStatement($statement);
        if ($returnInsertId)
            $result = ($statement->insert_id);
        else
            $result = ($statement->affected_rows > 0);

        $statement->close();
        return ($result);

    }

    public function select($idValue)
    {
//        $this->id = $idValue;
        $selectQuery = '';
        $resultBindings = '';
        $this->getSelectStrings($selectQuery, $resultBindings);
        $selectQuery .= " $this->idFieldName = ?";
        if (false) {
        // (isset($this->identity) && $this->identity == 'TEmailQueue') {
                             echo("<p>id value ".$idValue);
                             echo("</P><pre>::$selectQuery::</pre>");
                             return 0;
        //echo("<p>Query:</p><pre>");
        //    echo $selectQuery;
        //    echo("</pre>")

        }

        else {


        $statement = TSqlStatement::Prepare($selectQuery);
        $statement->bind_param('i',$idValue);


        $this->executeSelection($statement, $resultBindings);
        foreach($this->fields as $f)
            $f->assigned = true;
        }
        return ($this->getId() > 0);
    }  //  selectById

    public function search($whereClause) {
        $selectQuery = '';
        $resultBindings = '';
        $this->getSelectStrings($selectQuery, $resultBindings);
        $selectQuery .= $whereClause;
        $statement = TSqlStatement::Prepare($selectQuery);
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount > 2) {
            TSqlStatement::BindArgs($statement, $argCount, $args[1], $args);
        }

        $this->executeSelection($statement, $resultBindings);
    }

    public function update() {
        $fieldCount = 0;
        $setStatement = '';
        $paramBindings = '';
        $bindingTypes = '';
        $username = TAuthentication::GetCurrentUserName();

        foreach($this->fields as $f) {
            $type = $f->fieldType;
            $isDateStamp = ($type == DATESTAMP_FIELD);
            $isUserStamp = ($type == USERSTAMP_FIELD);

            if (
                $type !=  ID_FIELD &&
                $type !=  CREATEDATE_FIELD &&
                $type !=  CREATEUSER_FIELD &&
                ($f->modified || ($isDateStamp || $isUserStamp))) {

                if (!empty($setStatement))
                    $setStatement .= ',';
                if ($isDateStamp) {
                    $setStatement .= " $f->fieldName = CURRENT_DATE() ";
                }
                else {
                    if ($f->value === null && !$isUserStamp) {
                        $setStatement .= " $f->fieldName = null ";
                    }
                    else {
                        $setStatement .= " $f->fieldName = ? ";
                        if (!empty($paramBindings))
                            $paramBindings .= ',';
                        if ($isUserStamp) {
                            $paramBindings .= '$username';
                            $bindingTypes .= 's';
                        }
                        else {
                            $paramBindings .= $this->getBindingString($f->fieldName);
                            $bindingTypes .= $type;
                        }
                    }
                }

                $this->fields[$f->fieldName]->modified = false;
                ++$fieldCount;
            }
        }
        if ($fieldCount == 0)
            return false;
        $sql = "UPDATE $this->tableName SET $setStatement WHERE $this->idFieldName = ?";
        $paramBindings .= ',$this->id';
        $bindingTypes .= 'i';
 //       TTracer::ShowArray($this);
//        TTracer::Trace("Update: $sql; $bindingTypes; $paramBindings;")   ;
        return $this->executeCommand($sql,$bindingTypes,$paramBindings);
    }

    public function add() {
        $fieldCount = 0;
        $this->id = -1;
        $fieldList = '';
        $valueList = '';

        $paramBindings = '';
        $bindingTypes = '';

        foreach($this->fields as $f) {
//TTracer::Trace($f->fieldName);
//TTracer::Assert(($f->assigned),'Assigned');
            $type = $f->fieldType;
            $isDateStamp = ($type == DATESTAMP_FIELD || $type == CREATEDATE_FIELD);
            $isUserStamp = ($type == USERSTAMP_FIELD || $type == CREATEUSER_FIELD);
            $autoAssigned = ($isDateStamp || $isUserStamp);
            if ($f->value == null && !$autoAssigned)
                $f->assigned = false; // Avoid inserting nulls
            if ($type !=  ID_FIELD && ($f->assigned || $autoAssigned)) {
                if ($fieldList)
                    $fieldList .= ', ';
                $fieldList .= $f->fieldName;
                if ($valueList)
                    $valueList .= ', ';

                if ($isDateStamp ) {
                    $valueList .= " CURRENT_DATE() ";
                }
                else {
                    $valueList .= ' ? ';
                    if (!empty($paramBindings))
                        $paramBindings .= ',';

                    if ($isUserStamp) {
                        $paramBindings .= '$username';
                        $bindingTypes .= 's';
                    }
                    else {
                        $paramBindings .= $this->getBindingString($f->fieldName);
                        $bindingTypes .= $type;
                    }
                }
                ++$fieldCount;
            }
        }

        if ($fieldCount == 0)
            return false;
        $sql = "INSERT INTO $this->tableName ($fieldList) VALUES ($valueList) ";
//        TTracer::Trace('INSERT STATEMENT = '.$sql);
        $this->id = $this->executeCommand($sql,$bindingTypes,$paramBindings, true);
        return (!empty($this->id));
    }

    public function delete() {
        $deleteSql = "DELETE FROM $this->tableName WHERE $this->idFieldName = ?";
        TTracer::Trace($deleteSql);
        $statement = TSqlStatement::Prepare($deleteSql);
        $statement->bind_param('i',$this->id);
        TSqlStatement::ExecuteStatement($statement);
        $result = ($statement->affected_rows > 0);
        $statement->close();
    }

    public function getModificationStamps() {
        $result = new stdClass();
        foreach($this->fields as $f) {
            $type = $f->fieldType;
            switch($type) {
                case  DATESTAMP_FIELD:
                    $result->modifiedDate = $this->formatDate($f->value);
                    break;
                case USERSTAMP_FIELD:
                    $result->modifiedUser = $f->value;
                    break;
                case  CREATEDATE_FIELD :
                    $result->createdDate = $this->formatDate($f->value);
                    break;
                case CREATEUSER_FIELD :
                    $result->createdUser = $f->value;
                    break;

            }
        }
//        TTracer::ShowArray($result);
        return $result;
    }

    private function getUpdateStampFields(&$userField, &$dateField) {
        $userField = '';
        $dateField = '';
        foreach($this->fields as $f) {
            $type = $f->fieldType;
            if ($type == DATESTAMP_FIELD)
                $dateField = $f->fieldName;
            else if ($type == USERSTAMP_FIELD)
                $userField = $f->fieldName;

            if (!(empty($userField) && empty($dateField)))
                return;
        }
    }

    public function deActivate()
    {
        $this->updateField('active',0);
    }  //  deActivate

    public function updateField($aFieldName, $value) {
        $userField = '';
        $dateField = '';
        $this->getUpdateStampFields($userField, $dateField);
        $this->initFieldValue($aFieldName, $value);
        $f = $this->fields[$aFieldName];
        if ($f->value == null)
            $valueExpression = 'null';
        else if ($f->type == INT_FIELD || $f->type == DOUBLE_FIELD)
            $valueExpression = $f->value;
        else
            $valueExpression = "'$f->value'";

        $sql = "UPDATE $this->tableName SET $aFieldName = $valueExpression ";

        if (!empty($dateField))
            $sql .= ",$dateField = CURRENT_DATE()";
        if (!empty($userField)) {
            $sql .= ",$userField = ?";
        }
        $sql .= " WHERE $this->idFieldName = ?";
        $statement = TSqlStatement::Prepare($sql);
        if (empty($userField)) {
            $username = TAuthentication::getCurrentUserName();
            $statement->bind_param($f->fieldType.'si',$username, $this->id);
        }
        else  {
            $statement->bind_param($f->fieldType.'i',$this->id);
        }
        TSqlStatement::ExecuteStatement($statement);
        $result = ($statement->affected_rows > 0);
        $statement->close();
        return ($result);
    }

    public function __toString()
    {
        $result = 'id = '.$this->getId()."<br/>";
        $i = 0;
        foreach($this->fields as $f) {
            ++$i;
            if ($f->fieldName)
                $result .= "$f->fieldName = ".$f->value."<br/>";
            else
                $result .= "PHANTOM<br/>";
        }
        return $result;
    }  //  __tostring

    public function dump()
    {
        echo $this->__toString();
    }  //  dump

    public function get($fieldName)
    {
        return $this->fields[$fieldName]->value;
    }  //  get


    protected function checkEmail($fieldName,$required=false) {
        if (isset($this->fields[$fieldName])) {
            $field = $this->fields[$fieldName];
            $isBlank = ((!$field->assigned) || empty($field->value));
            if ($isBlank) {
                if (!$required)
                    return true;
            }
            else {
                if  (TPostOffice::IsValidEmail($field->value))
                    return true;
            }
        }
        $this->isValid = false;
        return false;
    }


    protected function checkRequired($fieldName) {
        if (isset($this->fields[$fieldName])) {
            $field = $this->fields[$fieldName];
            if ($field->assigned && !empty($field->value))
                return true;
        }
        $this->isValid = false;
        return false;
    }

    public function isValid() {
        return $this->isValid;
    }


}


TTracer::Off('entity');


