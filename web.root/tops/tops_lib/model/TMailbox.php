<?php
require_once("tops_lib/model/TEntityObject.php");

class TMailbox extends TEntityObject {
    public function  __construct()
    {
        $this->tableName = 'mailboxes';
        $this->idFieldName = 'mailboxId';
        $this->addField('mailboxId',INT_FIELD);
        $this->addField('mailboxCode',STRING_FIELD);
        $this->addField('name',STRING_FIELD);
        $this->addField('email',STRING_FIELD);
        $this->addField('description',STRING_FIELD);
        $this->addField('selectionList',INT_FIELD);
    }  //  TMailbox

    function getSelectionList() {
        return $this->get('selectionList');
    }

    function setSelectionList($value) {
        $this->setFieldValue('selectionList',$value);
    }

    function getMailboxCode() {
        return $this->get('mailboxCode');
    }
    function setMailboxCode($value) {
        $this->setFieldValue('mailboxCode',$value);
    }

    function getName() {
        return $this->get('name');
    }
    function setName($value) {
        $this->setFieldValue('name',$value);
    }

    function getEmail() {
        return $this->get('email');
    }
    function setEmail($value) {
        $this->setFieldValue('email',$value);
    }

    function getDescription() {
        return $this->get('description');
    }
    function setDescription($value) {
        $this->setFieldValue('description',$value);
    }

    public static function GetMailboxList($selectionList) {
        $result = array();
        $sql = 'select mailboxCode, name from mailboxes where selectionList = ? order by name';
        $statement = TSqlStatement::ExecuteQuery($sql,'i',$selectionList);
        $statement->instance->bind_result($mailboxCode, $name);
        while($statement->next()) {
            $result[$mailboxCode] = $name;
        }
        return $result;

    }
    public static function GetList() {
        TTracer::Trace('GetList');
        $result = array();
        $sql = 'select mailboxId, mailboxCode, name, email, description from mailboxes order by name';
        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($mailboxId, $mailboxCode, $name, $email, $description);
        while($statement->next()) {
            $box = new stdClass();
            $box->mailboxId    = $mailboxId;
            $box->mailboxCode  = $mailboxCode;
            $box->name         = $name;
            $box->email        = $email;
            $box->description  = $description;
            array_push($result,$box);
        }
        return $result;
    }

    public static function GetAddresses() {
        TTracer::Trace('GetAddresses');
        $result = array();
        $sql = 'select mailboxCode, name, email from mailboxes';
        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($mailboxCode, $name, $email);
        while($statement->next()) {
            $box = new stdClass();
            $box->name        = $name;
            $box->address      = $email;
            $result[$mailboxCode] = $box;
        }
        return $result;
    }


      public function validate() {
        $this->isValid = true;
        $errors = array();
        if (!$this->checkRequired('mailboxCode'))
            array_push($errors, 'Mailbox code is required.');
        if (!$this->checkRequired('name'))
            array_push($errors, 'Mailbox sender name is required');
        if ($this->checkRequired('email')) {
            if (!$this->checkEmail('email'))
                array_push($errors, "The email address is not valid.");
        }
        else
          array_push($errors, "The email address is required.");

        return $errors;
    }


    public static function Find($id) {
        TTracer::Trace('TMailbox::Find');
        $result = new TMailbox();
        if (!empty($id)) {
            if (!is_numeric($id)) {
                $id = TSqlStatement::ExecuteScaler(
                   'select mailboxId from mailboxes where mailboxCode = ?','s',$id);
            }
            $result->select($id);
        }
        return $result;
    }

    public static function Exists($mailboxCode) {
        TTracer::Trace('Exists');
        $count = TSqlStatement::ExecuteScaler('select count(*) from mailboxes where mailboxCode=?','s',$mailboxCode);
        return ($count > 0);
    }


    public static function Drop($id) {
        TTracer::Trace('TMailbox::Drop');
        TSqlStatement::ExecuteNonQuery('delete from mailboxes where mailboxId = ?','i',$id);

    }


} // end class

