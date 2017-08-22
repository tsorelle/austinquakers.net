<?php
class TEList extends TEntityObject {
    public function  __construct()
    {
        $this->tableName = 'elists';
        $this->idFieldName = 'elistId';
        $this->addField('elistId',INT_FIELD);
        $this->addField('listCode',STRING_FIELD);
        $this->addField('listName',STRING_FIELD);
        $this->addField('mailBox',STRING_FIELD);
        $this->addField('fromName',STRING_FIELD);
    }  //  TEList

    public function getListCode() {
        return $this->get('listCode');
    }
    public function setListCode($value) {
        $this->setFieldValue('listCode',$value);
    }

    public function getListName() {
        return $this->get('listName');
    }
    public function setListName($value) {
        $this->setFieldValue('listName',$value);
    }

    public function getMailBox() {
        return $this->get('mailBox');
    }
    public function setMailBox($value) {
        $this->setFieldValue('mailBox',$value);
    }

    public function getFromName() {
        return $this->get('fromName');
    }
    public function setFromName($value) {
        $this->setFieldValue('fromName',$value);
    }

    public static function getElists() {
        $result = array();
        $sql = 'SELECT elistId,listCode,listName,mailBox,fromName FROM elists';
        $statement = TSqlStatement::ExecuteQuery($sql);
        $id=0; $listCode=''; $listName=''; $mailBox=''; $fromName='';
        $statement->instance->bind_result($id, $listCode, $listName, $mailBox, $fromName);
        while ($statement->next()) {
            $list = new stdClass();
            $list->id = $id;
            $list->code = $listCode;
            $list->name = $listName;
            $list->mailBox = $mailBox;
            $list->fromName = $fromName;
            array_push($result,$list);
        }
        return $result;
    }
    
   

} // end class

