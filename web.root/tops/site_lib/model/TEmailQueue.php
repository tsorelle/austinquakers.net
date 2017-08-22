<?php
require_once("tops_lib/model/TEntityObject.php");

class TEmailQueue extends TEntityObject {
    public function  __construct()
    {
        $this->tableName = 'mailmessages';
        $this->idFieldName = 'mailMessageId';
        $this->addField('mailMessageId',INT_FIELD);

        $this->addField('listId',INT_FIELD);
        $this->addField('sender',STRING_FIELD);
        $this->addField('returnAddress',STRING_FIELD);
        $this->addField('subject',STRING_FIELD);
        $this->addField('message',STRING_FIELD);
        $this->addField('sentCount',INT_FIELD);
        $this->addField('recipientCount',INT_FIELD);
        $this->addField('postedDate',DATETIME_FIELD);
        $this->addField('postedBy',STRING_FIELD);
    }  //  TEmailQueue

    public $identity="TEmailQueue";

    function getListId() {
        return $this->get('listId');
    }
    function setListId($value) {
        $this->setFieldValue('listId',$value);
    }

    function getSender() {
        return $this->get('sender');
    }
    function setSender($value) {
        $this->setFieldValue('sender',$value);
    }

    function getReturnAddress() {
        return $this->get('returnAddress');
    }
    function setReturnAddress($value) {
        $this->setFieldValue('returnAddress',$value);
    }

    function getSubject() {
        return $this->get('subject');
    }
    function setSubject($value) {
        $this->setFieldValue('subject',$value);
    }

    function getMessage() {
        return $this->get('message');
    }
    function setMessage($value) {
        $this->setFieldValue('message',$value);
    }

    function getSentCount() {
        return $this->get('sentCount');
    }
    function setSentCount($value) {
        $this->setFieldValue('sentCount',$value);
    }

    function getRecipientCount() {
        return $this->get('recipientCount');
    }
    function setRecipientCount($value) {
        $this->setFieldValue('recipientCount',$value);
    }

    function getPostedDate() {
        return $this->getFieldValue('postedDate');
    }
    function setPostedDate($value) {
       $this->setFieldValue('postedDate',$value);
    }

    function getPostedBy() {
        return $this->get('postedBy');
    }
    function setPostedBy($value) {
        $this->setFieldValue('postedBy',$value);
    }

    public static function UpdateRecipientCount($messageId, $count) {
        $sql = "update mailmessages set recipientCount = ? where mailmessageId = ?";
        TSqlStatement::ExecuteNonQuery($sql,'ii',$count,$messageId);
    }

    public static function GetPendingMessageStatus() {
        $result = array();
        $sql = 'select listName, postedDate, postedBy, subject, sentCount, recipientCount '.
                'from mailmessages mm join elists elist on mm.listid = elist.elistId '.
                // 'where sentCount < recipientCount '.
                'WHERE postedDate > DATE_ADD(current_date(),INTERVAL -6 MONTH) '.
                'order by postedDate DESC';
                //                'order by elist.listName, postedDate DESC';


        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($listName, $postedDate, $postedBy,$subject,$sentCount, $recipientCount);
        $count = 0;
        while ($statement->Next()) {
            $record = new stdclass();
            $record->listName = $listName;
            $record->sendtime = $postedDate;
            $record->sender = $postedBy;
            $record->subject = $subject;
            $record->sentCount = $sentCount;
            $record->recipientCount = $recipientCount;
            $result[++$count] = $record;
        }
        return $result;

    }


} // end class

