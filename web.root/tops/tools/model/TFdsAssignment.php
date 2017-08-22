<?php
require_once("tops_lib/model/TEntityObject.php");

class TFdsAssignment extends TEntityObject { 
    public function  __construct()
    {
        $this->tableName = 'fdsassignments';
        $this->idFieldName = 'id';
        $this->addField('id',INT_FIELD);
        $this->addField('assignmentDate',DATE_FIELD);
        $this->addField('personID',INT_FIELD);
        $this->addField('classId',INT_FIELD);
        $this->addField('note',STRING_FIELD);
        $this->addField('role',INT_FIELD);
    }  //  TFdsAssignment

    function getAssignmentDate() {
        return $this->get('assignmentDate');
    }
    function setAssignmentDate($value) {
        $this->setFieldValue('assignmentDate',$value);
    }

    function getPersonID() {
        return $this->get('personID');
    }
    function setPersonID($value) {
        $this->setFieldValue('personID',$value);
    }

    function getClassId() {
        return $this->get('classId');
    }
    function setClassId($value) {
        $this->setFieldValue('classId',$value);
    }

    function getNote() {
        return $this->get('note');
    }
    function setNote($value) {
        $this->setFieldValue('note',$value);
    }

    function getRole() {
        return $this->get('role');
    }
    function setRole($value) {
        $this->setFieldValue('role',$value);
    }

} // end class

