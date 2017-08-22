<?php
class TCommittee extends TEntityObject {
    public function  __construct()
    {
        $this->tableName = 'committees';
        $this->idFieldName = 'committeeId';
        $this->addField('committeeId',INT_FIELD);
        $this->addField('name',STRING_FIELD);
        $this->addField('mailbox',STRING_FIELD);
        $this->addField('active',INT_FIELD);
        $this->addField('isStanding',INT_FIELD);
        $this->addField('isLiaison',INT_FIELD);
        $this->addField('membershipRequired',INT_FIELD);
        $this->addField('description',STRING_FIELD);
        $this->addField('notes',STRING_FIELD);
        $this->addField('dateAdded',CREATEDATE_FIELD);
        $this->addField('dateUpdated',DATESTAMP_FIELD);
    }  //  TCommittee

    function getName() {
        return $this->get('name');
    }
    function setName($value) {
        $this->setFieldValue('name',$value);
    }

    function getMailbox() {
        return $this->get('mailbox');
    }
    function setMailbox($value) {
        $this->setFieldValue('mailbox',$value);
    }

    function getActive() {
        return $this->get('active');
    }
    function setActive($value) {
        $this->setFieldValue('active',$value);
    }

    function getIsStanding() {
        return $this->get('isStanding');
    }
    function setIsStanding($value) {
        $this->setFieldValue('isStanding',$value);
    }

    function getIsLiaison() {
        return $this->get('isLiaison');
    }
    function setIsLiaison($value) {
        $this->setFieldValue('isLiaison',$value);
    }

    function getMembershipRequired() {
        return $this->get('membershipRequired');
    }
    function setMembershipRequired($value) {
        $this->setFieldValue('membershipRequired',$value);
    }

    function getDescription() {
        return $this->get('description');
    }
    function setDescription($value) {
        $this->setFieldValue('description',$value);
    }

    function getNotes() {
        return $this->get('notes');
    }
    function setNotes($value) {
        $this->setFieldValue('notes',$value);
    }

    function getDateAdded() {
        return $this->get('dateAdded');
    }

    function getDateUpdated() {
        return $this->get('dateUpdated');
    }

    public static function GetCommittee($id) {
        $result = new TCommittee();
        $exists = $result->select($id);
        if ($exists === false)
            return null;
        return $result;
    }

    public function toDTO() {
        $result = new stdClass();
        $result->committeeId        = $this->getId();
        $result->name               = trim(utf8_encode($this->getName()));
        $result->mailbox            = $this->getMailbox();
        $result->active             = $this->getActive();
        $result->isStanding         = $this->getIsStanding();
        $result->isLiaison          = $this->getIsLiaison();
        $result->membershipRequired = $this->getMembershipRequired();
        $result->description        = trim(utf8_encode($this->getDescription()));
        $result->notes              = TText::HtmlToText($this->getNotes());
        $result->dateAdded          = $this->getDateAdded();
        $result->dateUpdated        = $this->getDateUpdated();
        return $result;
    }
    public static function GetCommitteeDTO($id )
    {
        
        $committee = self::GetCommittee($id);
        if ($committee == null) {
            return null;
        }
        return $committee->toDTO();
    }

    public static function GetCommitteeList() {

        $result = array();
        $sql = 'select committeeId, name from committees where active=1';
        $statement = TSqlStatement::ExecuteQuery($sql);
        $committeeId = null; $committeeName = null;
        $statement->instance->bind_result($committeeId,$committeeName);
        while($statement->next()) {
            $result[$committeeId] = $committeeName;
        }
        asort($result);
        return $result;

    }

    public static function GetReport() {
        $result = array();
        $sql = 'select committeeId,committeeName,statusId,memberName,email,phone,role,nominationStatus from committeeReportView';
        $statement = TSqlStatement::ExecuteQuery($sql);
        $committeeId       = null;
        $committeeName     = null;
        $statusId          = null;
        $memberName        = null;
        $email             = null;
        $phone             = null;
        $role              = null;
        $nominationStatus = null;

        $statement->instance->bind_result($committeeId,$committeeName,$statusId,$memberName,$email,
            $phone,$role,$nominationStatus);
        while($statement->next()) {
            $item = new stdClass();
            $item->committeeId        = $committeeId;
            $item->committeeName      = $committeeName;
            $item->statusId           = $statusId;
            $item->memberName         = $memberName;
            $item->email              = $email;
            $item->phone              = $phone;
            $item->role               = $role;
            $item->nominationStatus  = $nominationStatus;
            array_push($result,$item);
        }
        return $result;
    }


} // end class

