<?php
require_once("tops_lib/model/TEntityObject.php");

class TFdsClass extends TEntityObject { 
    public function  __construct()
    {
        $this->tableName = 'fdsclasses';
        $this->idFieldName = 'id';
        $this->addField('id',INT_FIELD);
        $this->addField('code',STRING_FIELD);
        $this->addField('name',STRING_FIELD);
    }  //  TFdsClass

    function getCode() {
        return $this->get('code');
    }
    function setCode($value) {
        $this->setFieldValue('code',$value);
    }

    function getName() {
        return $this->get('name');
    }
    function setName($value) {
        $this->setFieldValue('name',$value);
    }

} // end class

