<?php
require_once("tops_lib/model/TEntityObject.php");

class TCommitteeMember extends TEntityObject { 
    public function  __construct()
    {
        $this->tableName = 'committeemembers';
        $this->idFieldName = 'committeeMemberId';
        $this->addField('committeeMemberId',INT_FIELD);
        $this->addField('committeeId',INT_FIELD);
        $this->addField('personId',INT_FIELD);
        $this->addField('roleId',INT_FIELD);
        $this->addField('notes',STRING_FIELD);
        $this->addField('status',INT_FIELD);
        $this->addField('startOfService',DATE_FIELD);
        $this->addField('endOfService',DATE_FIELD);
        $this->addField('dateRelieved',DATE_FIELD);
        $this->addField('dateAdded',CREATEDATE_FIELD);
        $this->addField('dateUpdated',DATESTAMP_FIELD);
    }  //  TCommitteeMember

    function getCommitteeId() {
        return $this->get('committeeId');
    }
    function setCommitteeId($value) {
        $this->setFieldValue('committeeId',$value);
    }

    function getPersonId() {
        return $this->get('personId');
    }
    function setPersonId($value) {
        $this->setFieldValue('personId',$value);
    }

    function getRoleId() {
        return $this->get('roleId');
    }
    function setRoleId($value) {
        $this->setFieldValue('roleId',$value);
    }

    function getNotes() {
        return $this->get('notes');
    }
    function setNotes($value) {
        $this->setFieldValue('notes',$value);
    }

    function getStatus() {
        return $this->get('status');
    }
    function setStatus($value) {
        $this->setFieldValue('status',$value);
    }

    function getStartOfService() {
        return $this->get('startOfService');
    }
    function setStartOfService($value) {
        $this->setFieldValue('startOfService',$value);
    }

    function getEndOfService() {
        return $this->get('endOfService');
    }
    function setEndOfService($value) {
        $this->setFieldValue('endOfService',$value);
    }

    function getDateRelieved() {
        return $this->get('dateRelieved');
    }
    function setDateRelieved($value) {
        $this->setFieldValue('dateRelieved',$value);
    }

    function getDateAdded() {
        return $this->get('dateAdded');
    }

    function getDateUpdated() {
        return $this->get('dateUpdated');
    }

    public static function GetCurrentMemberList($committeeId) {
        $sql = 'select distinct p.personId, p.firstName, p.lastName, cm.roleId, r.roleName,  p.email from persons p '.
                'join committeemembers cm on cm.personId = p.personId '.
                'join committeeroles r on cm.roleId = r.roleId '.
                'where cm.committeeId = ? '.
                'and cm.status = 3 '.
                'and (dateRelieved is null or dateRelieved > curdate()) '.
                'order by p.lastName';

        $result = array();
        $statement = TSqlStatement::ExecuteQuery($sql,'i',$committeeId);
        $pid = null;$firstName= null;$lastName= null;$roleId= null;$roleName= null;$email=null;
            
        $statement->instance->bind_result($pid,$firstName,$lastName,$roleId,$roleName,$email);
        $i = 0;
        while($statement->next()) {
            $item = new stdClass();
            $item->pid = $pid;
            $item->name = $firstName.' '.$lastName;
            $item->roleId = $roleId;
            $item->roleName = $roleName;
            $item->email = $email;
            $result[$i++] = $item;
        }
        return $result;
    }

    public static function GetAllMembers($committeeId) {
        $sql = 'SELECT committeeMemberId, committeeId, personId, name, email, phone, roleId, role, status, statusId, '.
                'startOfService, endOfService, dateRelieved, notes, dateAdded, dateUpdated '.
                'from committeeMemberView WHERE committeeId = ?';
        $committeeMemberId = null;
        $personId = null;
        $name = null;
        $email = null;
        $phone = null;
        $roleId = null;
        $role = null;
        $status = null;
        $statusId = null;
        $startOfService = null;
        $endOfService = null;
        $dateRelieved = null;
        $notes = null;
        $dateAdded = null;
        $dateUpdated = null;

        $result = array();
        $statement = TSqlStatement::ExecuteQuery($sql,'i',$committeeId);
        $statement->instance->bind_result(
            $committeeMemberId,
            $committeeId,
            $personId,
            $name,
            $email,
            $phone,
            $roleId,
            $role,
            $status,
            $statusId,
            $startOfService,
            $endOfService,
            $dateRelieved,
            $notes,
            $dateAdded,
            $dateUpdated
        );

        $i = 0;
        $today = date('Y-m-d');
        while($statement->next()) {
            $item = new stdClass();
            if (!empty($email)) {
                $email = $name.'<'.$email.'>';
            }
            $item->committeeMemberId = $committeeMemberId;
            $item->committeeId       = $committeeId;
            $item->personId          = $personId;
            $item->name              = trim(utf8_encode($name));
            $item->email             = $email;
            $item->phone             = $phone;
            $item->roleId            = $roleId;
            $item->role              = $role;
            $item->status            = $status;
            $item->statusId          = $statusId;
            $item->startOfService    = $startOfService;
            $item->endOfService      = $endOfService;
            $item->dateRelieved      = $dateRelieved;
            $item->notes             = trim(utf8_encode($notes));
            $item->dateAdded         = $dateAdded;
            $item->dateUpdated       = $dateUpdated;

            $isCurrent = empty($dateRelieved);
            $endDate = $isCurrent ? $endOfService : $dateRelieved;

            if (empty($startOfService)) {
                $item->termOfService =  $isCurrent ? 'current' : 'dates unknown';
            }
            else {
                if ($isCurrent && $today > $endOfService) {
                    $item->termOfService = "$startOfService to present";
                }
                else {
                    $item->termOfService = "$startOfService to $endDate";
                }
            }
            
            array_push($result,$item );
        }
        return $result;
    }

} // end class

