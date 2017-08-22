<?php
require_once("tops_lib/model/TEntityObject.php");

class TReminder extends TEntityObject { 
    public function  __construct()
    {
        $this->tableName = 'reminders';
        $this->idFieldName = 'reminderId';
        $this->addField('reminderId',INT_FIELD);
        $this->addField('personId',INT_FIELD);
        $this->addField('eventId',INT_FIELD);
    }  //  TReminder

    function getPersonId() {
        return $this->get('personId');
    }
    function setPersonId($value) {
        $this->setFieldValue('personId',$value);
    }

    function getEventId() {
        return $this->get('eventId');
    }
    function setEventId($value) {
        $this->setFieldValue('eventId',$value);
    }

    /**
     * @param $personId
     * @param $eventId
     * @return TReminder
     * @throws Exception
     */
    public static function create($personId,$eventId) {
        if (!(is_numeric($personId) && is_numeric($eventId))) {
            throw new Exception('Invalid parameters: TReminder::create');
        }
        $where = "personId=$personId && eventId=$eventId";
        $instance = new TReminder();
        $instance->search("personId=$personId && eventId=$eventId");
        if (!$instance->getId()) {
            $instance->setEventId($eventId);
            $instance->setPersonId($personId);
        }
        return $instance;
    }

    public static function addReminder($personId,$eventId) {
        $reminder = self::create($personId,$eventId);
        $id = $reminder->getId();
        if (empty($id)) {
            $reminder->add();
        }
    }

    public static function removeReminder($personId,$eventId) {
        $reminder = self::create($personId,$eventId);
        $id = $reminder->getId();
        if (!empty($id)) {
            $reminder->delete();
        }
    }
} // end class

