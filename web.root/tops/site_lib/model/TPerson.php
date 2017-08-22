<?php
require_once("tops_lib/model/TEntityObject.php");

class TPerson extends TEntityObject {

    private $membershipType;

    public function  __construct()
    {
        $this->tableName = 'persons';
        $this->idFieldName = 'personID';
        $this->addField('personID',INT_FIELD);

        $this->addField('firstName',STRING_FIELD);
        $this->addField('lastName',STRING_FIELD);
        $this->addField('middleName',STRING_FIELD);
        $this->addField('addressId',INT_FIELD);
        $this->addField('phone',STRING_FIELD);
        $this->addField('workPhone',STRING_FIELD);
        $this->addField('email',STRING_FIELD);
        $this->addField('membershipStatus',INT_FIELD);
        $this->addField('birthYear',INT_FIELD);
        $this->addField('username',STRING_FIELD);
        $this->addField('password',STRING_FIELD);
        $this->addField('notes',STRING_FIELD);
        $this->addField('junior',BOOLEAN_FIELD);
        $this->addField('sortkey',STRING_FIELD);
        $this->addField('dateAdded',CREATEDATE_FIELD);
        $this->addField('dateUpdated',DATESTAMP_FIELD);
        $this->addField('active',INT_FIELD);
        $this->addField('dateOfBirth',DATE_FIELD);
        $this->addField('deceased',DATE_FIELD);
        $this->addField('createdBy',CREATEUSER_FIELD);
        $this->addField('updatedBy',USERSTAMP_FIELD);
        $this->addField('directoryCode',INT_FIELD);
        $this->addField('otherAffiliation',STRING_FIELD);
        $this->addField('residenceLocation',STRING_FIELD);


    }  //  TPerson

    function getFirstName() {
        return $this->get('firstName');
    }
    function setFirstName($value) {
        $this->setFieldValue('firstName',$value);
    }

    function getLastName() {
        return $this->get('lastName');
    }
    function setLastName($value) {
        $this->setFieldValue('lastName',$value);
    }

    function getMiddleName() {
        return $this->get('middleName');
    }
    function setMiddleName($value) {
        $this->setFieldValue('middleName',$value);
    }

    function getUsername() {
        return $this->get('username');
    }
    function setUsername($value) {
        $this->setFieldValue('username',$value);
    }

     function getAddressID() {
      return $this->get('addressId');
    }
    function getPhone() {
      return $this->get('phone');
    }
    function getWorkPhone() {
      return $this->get('workPhone');
    }
    function getEmail() {
      return $this->get('email');
    }
    function getMembershipStatus() {
      return $this->get('membershipStatus');
    }

    function getMemberStatusText()
    {
        $sql = 'SELECT statusDescription FROM memberstatus WHERE memberStatusId = ?';
        $result = TSqlStatement::ExecuteScaler($sql,'i',$this->getMembershipStatus());
        if ($result === false)
            return "Unknown";

        return $result;
    }  //  getMemberStatusText

    function getBirthYear() {
      return $this->get('birthYear');
    }

        function getPassword() {
      return $this->get('password');
    }
    function getNotes() {
      return $this->get('notes');
    }
    function getJunior() {
      return $this->get('junior');
    }
    function getSortkey() {
      return $this->get('sortkey');
    }

    function setAddressID($value) {
      $this->setFieldValue('addressId',$value);
    }
    function setPhone($value) {
      $this->setFieldValue('phone',$value);
    }
    function setWorkPhone($value) {
      $this->setFieldValue('workPhone',$value);
    }
    function setEmail($value) {
      $this->setFieldValue('email',$value);
    }
    function setMembershipStatus($value) {
      $this->setFieldValue('membershipStatus',$value);
    }
    function setBirthYear($value) {
      $this->setFieldValue('birthYear',$value);
    }

    function setPassword($value) {
      $this->setFieldValue('password',$value);
    }

    function setNotes($value) {
      $this->setFieldValue('notes',$value);
    }
    function setJunior($value) {
      $this->setFieldValue('junior',$value);
    }
    function setSortkey($value) {
      $this->setFieldValue('sortkey',$value);
    }

    function getDateAdded() {
        return $this->getDate('dateAdded');
    }

    function getAddedBy() {
        return $this->get('addedBy');
    }

    function getDateUpdated() {
        return $this->getDate('dateUpdated');
    }

    function getUpdatedBy() {
        return $this->get('updatedBy');
    }

    function getActive() {
        return $this->get('active');
    }
    function setActive($value) {
        $this->setFieldValue('active',$value);
    }

        function getDateOfBirth() {
        return $this->getDate('dateOfBirth');
    }
    function setDateOfBirth($value) {
        $this->setDateFieldValue('dateOfBirth',$value);
    }

    function getDeceased() {
        return $this->getDate('deceased');
    }
    function setDeceased($value) {
        $this->setDateFieldValue('deceased',$value);
    }

    function getCreatedBy() {
        return $this->get('createdBy');
    }

    function getDirectoryCode() {
        return $this->get('directoryCode');
    }
    function setDirectoryCode($value) {
        $this->setFieldValue('directoryCode',$value);
    }

    function getOtherAffiliation() {
        return $this->get('otherAffiliation');
    }
    function setOtherAffiliation($value) {
        $this->setFieldValue('otherAffiliation',$value);
    }

    function getResidenceLocation() {
        return $this->get('residenceLocation');
    }
    function setResidenceLocation($value) {
        $this->setFieldValue('residenceLocation',$value);
    }

    public function validate() {
        $this->isValid = true;
        $errors = array();
        if (!$this->checkRequired('lastName'))
            array_push($errors, 'Last name  is required.');
        if (!$this->checkRequired('firstName'))
            array_push($errors, 'First name is required.');
        if (!$this->checkEmail('email'))
            array_push($errors, "The email address '.$this->email.' is not a valid address.");
        return $errors;
    }

    public static function FindIdForUserName($userName) {
            $sql = 'select personId from persons '.
                    'where username = ?';
            $result = TSqlStatement::ExecuteScaler($sql,'s',$userName);
            if ($result === false)
                return 0;
            return $result;
    }

    public static function FindAddressIdForUserName($userName) {
            $sql = 'select addressId from persons '.
                    'where username = ?';
            $result = TSqlStatement::ExecuteScaler($sql,'s',$userName);
            if ($result === false)
                return 0;
            return $result;
    }

    public static function Drop($pid) {
        $sql = 'delete from persons where personId = ?';
        $result = TSqlStatement::ExecuteNonQuery($sql,'i',$pid);
        $sql = 'delete from emails where personId = ?';
        $result = TSqlStatement::ExecuteNonQuery($sql,'i',$pid);
    }

    public static function GetPerson($pid) {
        $result = new TPerson();
        $result->select($pid);
        return $result;
    }

    public static function GetPersonsAtAddress($aid) {

        $result = array();
        $sql = 'SELECT personId, firstName, middleName, lastName FROM persons WHERE addressId = ?';
        $statement = TSqlStatement::ExecuteQuery($sql,'i',$aid);
        $statement->instance->bind_result($id,$first,$middle,$last);

        while($statement->next()) {
            $item = new stdclass();
            $item->id = $id;
            $item->name = TPerson::FullName($first,$middle,$last);
            array_push($result,$item);
        }
        return $result;
    }

    public static function CheckForCrossLinkedAccounts($username) {
        $sql = 'SELECT COUNT(*) FROM persons WHERE username = ?';
        $count = TSqlStatement::ExecuteScaler($sql,'s',$username);
        return ($count > 1);
    }

    public function getFullName() {
        return self::FullName($this->getFirstName(), $this->getMiddleName(), $this->getLastName());
    }

    public static function GetFullNameForUser($username) {
        $sql = "SELECT firstName, middleName, lastName FROM persons WHERE userName = ?";
        $statement = TSqlStatement::ExecuteQuery($sql,'s',$username);
        $statement->instance->bind_result($firstName, $middleName, $lastName);
        if ($statement->next()) {
                return self::FullName($firstName,$middleName,$lastName);
            }
        return '';
    }

    public static function FullName($firstName, $middleName, $lastName) {
        $result = '';
        if (!empty($firstName))
            $result = $firstName;
        if (!empty($middleName)) {
            if (!empty($result))
                $result .= ' ';
            $result .= $middleName;
        }
        if (!empty($lastName)) {
            if (!empty($result))
                $result .= ' ';
            $result .= $lastName;
        }
        return $result;
    }

    public static function ChangeAddress($personId,$addressId) {
        $sql = 'update persons set addressId = ? where personId = ?';
        $result = TSqlStatement::ExecuteNonQuery($sql,'ii',$addressId,$personId);
        return $result;
    }

    public static function FindIdByName($name) {
        if (is_numeric($name))
            return $name;
        $sql = 'select personId from persons where ';
        $nameParts = explode(' ',$name);
        $count = count($nameParts);
        $pid = 0;
        if ($count == 1) {
            $pid = TSqlStatement::ExecuteScaler($sql.'username = ?','s',$name);
        }
        else if ($count == 2) {
            $pid = TSqlStatement::ExecuteScaler(
                $sql.'firstName=? and lastName=?',
                'ss',$nameParts[0],$nameParts[1]);

        }
        else if ($count == 3) {
            $pid = TSqlStatement::ExecuteScaler(
                $sql.'firstName =? and middleName=? and lastName=?',
                'sss',$nameParts[0],$nameParts[1],$nameParts[2]);

        }
        return $pid;
    }

    public static function FindByName($name) {
        $pid = self::FindIdByName($name);
        if ($pid) {
            $person = self::GetPerson($pid);
            return $person;
        }
        return false;
    }

    public static function FindByUserName($userName) {
        $pid = self::FindIdForUserName($userName);
        if ($pid) {
            $person = self::GetPerson($pid);
            return $person;
        }
        return false;
    }


} // end class

