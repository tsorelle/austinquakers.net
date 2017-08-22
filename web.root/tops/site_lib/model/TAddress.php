<?php
require_once("tops_lib/model/TEntityObject.php");

class TAddress extends TEntityObject {
    public function  __construct()
    {
        $this->tableName = 'addresses';
        $this->idFieldName = 'addressID';
        $this->addField('addressID',INT_FIELD);
        $this->addField('addressType',INT_FIELD);
        $this->addField('addressName',STRING_FIELD);
        $this->addField('address1',STRING_FIELD);
        $this->addField('address2',STRING_FIELD);
        $this->addField('city',STRING_FIELD);
        $this->addField('state',STRING_FIELD);
        $this->addField('postalCode',STRING_FIELD);
        $this->addField('country',STRING_FIELD);
        $this->addField('phone',STRING_FIELD);
        $this->addField('notes',STRING_FIELD);
        $this->addField('dateAdded',CREATEDATE_FIELD);
        $this->addField('dateUpdated',DATESTAMP_FIELD);
        $this->addField('active',INT_FIELD);
        $this->addField('sortkey',STRING_FIELD);
        $this->addField('directoryCode',INT_FIELD);
        $this->addField('fnotes',INT_FIELD);
        $this->addField('createdBy',CREATEUSER_FIELD);
        $this->addField('updatedBy',USERSTAMP_FIELD);
    }  //  TAddress

    function getAddressType() {
        return $this->get('addressType');
    }
    function setAddressType($value) {
        $this->setFieldValue('addressType',$value);
    }

    function getAddressName() {
        return $this->get('addressName');
    }
    function setAddressName($value) {
        $this->setFieldValue('addressName',$value);
    }

    function getAddress1() {
        return $this->get('address1');
    }
    function setAddress1($value) {
        $this->setFieldValue('address1',$value);
    }

    function getAddress2() {
        return $this->get('address2');
    }
    function setAddress2($value) {
        $this->setFieldValue('address2',$value);
    }

    function getCity() {
        return $this->get('city');
    }
    function setCity($value) {
        $this->setFieldValue('city',$value);
    }

    function getState() {
        return $this->get('state');
    }
    function setState($value) {
        $this->setFieldValue('state',$value);
    }

    function getPostalCode() {
        return $this->get('postalCode');
    }
    function setPostalCode($value) {
        $this->setFieldValue('postalCode',$value);
    }

    function getCountry() {
        return $this->get('country');
    }
    function setCountry($value) {
        $this->setFieldValue('country',$value);
    }

    function getPhone() {
        return $this->get('phone');
    }
    function setPhone($value) {
        $this->setFieldValue('phone',$value);
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

    function getActive() {
        return $this->get('active');
    }
    function setActive($value) {
        $this->setFieldValue('active',$value);
    }

    function getSortkey() {
        return $this->get('sortkey');
    }
    function setSortkey($value) {
        $this->setFieldValue('sortkey',$value);
    }

    function getDirectoryCode() {
        return $this->get('directoryCode');
    }
    function setDirectoryCode($value) {
        $this->setFieldValue('directoryCode',$value);
    }

    function getFnotes() {
        return $this->get('fnotes');
    }
    function setFnotes($value) {
        $this->setFieldValue('fnotes',$value);
    }

    function getCreatedBy() {
        return $this->get('createdBy');
    }

    function getUpdatedBy() {
        return $this->get('updatedBy');
    }



    /***** Relational operators ***********/

    function addPerson($pid)
    {
        $aid = $this->getId();
        $sql ="UPDATE persons SET addressID = ? WHERE personID = ?";
        TSqlStatement::ExecuteNonQuery($sql,'ii',$aid,$pid);
    }  //  addPerson

    function removePerson($pid)
    {
        $sql = "UPDATE persons SET addressID = NULL WHERE personID = ?";
        TSqlStatement::ExecuteNonQuery($sql,'i',$pid);
    }  //  removePerson

    function unlinkPersons()
    {
        $aid = $this->getId();
        $sql ="UPDATE persons SET addressID = NULL WHERE addressID = ?";
        TSqlStatement::ExecuteNonQuery($sql,'i',$aid);
    }  //  unlinkPersons

    function deletePersons()
    {
        $aid = $this->getId();
        $sql = "DELETE FROM persons WHERE addressID = ?";
        TSqlStatement::ExecuteNonQuery($sql,'i',$aid);
    }  //  deletePersons


    function drop() {
        $id = $this->getId();
        $sql = "DELETE FROM addresses WHERE addressID = ?";
        TSqlStatement::ExecuteNonQuery($sql,'i',$id);
        return $id;
    }

    function delete() {
        $oldId = $this->drop();
        $this->unlinkPersons();
        $this->setId(0);
    }

    function deleteCascade() {
        $oldId = $this->drop();
        $this->deletePersons();
        $this->setId(0);
    }


    function deactivateCascade()
    {
        $aid = $this->getId();
        $this->deActivate();
        $sql = "UPDATE persons SET active = 0 WHERE addressID = ?";
        TSqlStatement::ExecuteNonQuery($sql,'i',$aid);
    }  //  deactivateCascade


    function getAddressTypeText()
    {
        $sql =
          'SELECT addressTypeDescription FROM addresstypes WHERE addressTypeID = ?';
          $addressType = $this->getAddressType();
          $result = TSqlStatement::ExecuteScaler($sql,'i',$addressType);
          if ($result === false)
             return 'Unknown';
          return $result;
    }  //  getMemberStatusText


    public function validate() {
        $this->isValid = true;
        $errors = array();
        if (!$this->checkRequired('addressName'))
            array_push($errors, 'Address name  is required.');
        if (!$this->checkRequired('city'))
            array_push($errors, 'City name  is required.');

        return $errors;
    }


    public static function GetAddress($aid) {
        $result = new TAddress();
        $result->select($aid);
        return $result;
    }

} // end class

