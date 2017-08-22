<?php

/*****************************************************************
Class:  TPersonsListEntry
Description:
*****************************************************************/
class TDirectoryListEntry {
    private
        $personId,
        $addressId,
        $firstName,
        $middleName,
        $lastName,
        $addressName;

    public function __construct(
             $personId,
             $addressId,
             $firstName,
             $middleName,
             $lastName,
             $addressName) {
        $this->personId = $personId;
        $this->addressId = $addressId;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->addressName = $addressName;
    }

    public function __toString() {
        return $this->getDisplayName();
    }

    public function getDisplayName() {
        return TPerson::FullName($this->firstName,$this->middleName,$this->lastName);
    }

    public function getPersonId() {
        return $this->personId;
    }
    public function getAddressId() {
        return $this->addressId;
    }
    public function getFirstName() {
        return $this->firstName;
    }
    public function getLastName() {
        return $this->lastName;
    }
    public function getMiddleName() {
        return $this->middleName;
    }
    public function getAddressName() {
        return $this->addressName;
    }
}
// end TPersonsListEntry

/*****************************************************************
Class:  TPersonsList
Description:
*****************************************************************/
class TDirectorySearchList {
    private $pageNumber=0, $itemsPerPage=10;
    private $activeOnly = true;
    private $searchOption;
    private $firstName;
    private $lastName;
    private $addressName;

    public function __construct($firstName,$lastName,$addressName,$searchOption,$pageNumber = 0, $itemsPerPage = 0) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->addressName = $addressName;
        $this->searchOption = $searchOption;
        $this->pageNumber = $pageNumber;
        if ($itemsPerPage > 0)
                $this->itemsPerPage = $itemsPerPage;
                // TTracer::Trace("pg = $this->pageNumber items = $this->itemsPerPage");

    }

    public function __toString() {
        return 'not implemented';
    }

    public function setPaging($pageNumber, $itemsPerPage=10) {
        $this->pageNumber= $pageNumber;
        $this->itemsPerPage= $itemsPerPage;
    }

    public function showInactive() {
        $this->activeOnly = false;
    }


    private function getPersonEntries($statement) {
        $statement->instance->bind_result(
             $personId,
             $addressId,
              $firstName,
              $middleName,
              $lastName,
              $addressName);

        $result = array();

        while($statement->next()) {
 // TTracer::Trace("Contact: $firstName $lastName");
            array_push($result,
                new TDirectoryListEntry(
                     $personId,
                     $addressId,
                     $firstName,
                     $middleName,
                     $lastName,
                     $addressName));
        }
 //$count = sizeof($result);
 //TTracer::Trace("Contact count: $count");

        return $result;
    }


    private function getSearchExpression($fieldName, $value, $searchOption) {
        $result = ' ('.$fieldName;
        if ($searchOption == 'exact')
            $result .= " = '$value' ";
        else {
            $result .= " like ";
            switch($searchOption) {
                case 'start' :
                    $result .= "'$value%' "; break;
                case 'end' :
                    $result .= "'%$value' "; break;
                default:
                    $result .= "'%$value%' "; break;
            }
        }
        return $result.') ';

    }

    public function getSearchResult() {

        $searchPerson = !(empty($this->firstName) && empty($this->lastName));
        $searchAddress = !empty($this->addressName);
        $searchBoth = $searchPerson && $searchAddress;

        // $sql = 'SELECT DISTINCT ';
        if ($searchBoth || $searchPerson) {
            $fieldList =
                ' p.personId, a.addressId, firstName, middleName, lastName, a.addressName ';
            $sql =
                ' FROM persons p left outer join addresses a on p.addressId = a.addressId ';
        }
        else if ($searchAddress)    {
            $fieldList .=
                 ' null as personId, addressId, null as firstName, null as middleName, null as lastName, addressName ';
                 $sql = ' FROM addresses a ';
        }
        $sql .= ' WHERE ';
        $personExpression = '';
        $addressExpression = '';
        if ($searchBoth || $searchAddress)
            $addressExpression = $this->getSearchExpression('addressName', $this->addressName, $this->searchOption);
        if ($searchBoth || $searchPerson) {
            if (!empty($this->firstName))
                $personExpression = $this->getSearchExpression('firstName', $this->firstName, $this->searchOption);
            if (!empty($this->lastName)) {
                if (!empty($personExpression))
                    $personExpression .= ' and ';
                $personExpression .= $this->getSearchExpression('lastName', $this->lastName, $this->searchOption);
            }
        }
        $sql .= '(';
        if (!empty($addressExpression))
            $sql .= $addressExpression;
        if (!empty($personExpression)) {
            if (!empty($addressExpression))
                $sql .= ' or ';
            $sql .= $personExpression;
        }
        $sql .= ') ';

        if ($searchBoth || $searchPerson) {
            $seeAll = true; // TODO: Check permissions
            if (!$seeAll)
                $sql .= ' AND (p.directoryCode > 1) ';
            $sql .= ' AND (p.active = 1) ';
        }
        if ($searchBoth || $searchAddress)
            $sql .= ' AND (a.active = 1) ';

         $totalItems = TSqlStatement::ExecuteScaler('SELECT COUNT(*) '.$sql);
         TTracer::Trace("totalItems = ".$totalItems);


        $sql .= ' ORDER BY ';
        $orderExpression = '';
        if ($searchBoth || $searchPerson)
            $orderExpression = 'p.lastName,p.firstName ';
        if ($searchBoth || $searchAddress) {
            if (!empty($orderExpression))
                $orderExpression .= ',';
            $orderExpression .= 'a.addressName ';
        }

        $sql .= $orderExpression;

        if ($this->pageNumber > 0 && $this->itemsPerPage > 0) {
            $offset = ($this->pageNumber - 1) * $this->itemsPerPage;
            $sql .= " LIMIT $offset, $this->itemsPerPage ";
        }

        TTracer::Trace($sql);
        $statement = TSqlStatement::ExecuteQuery("SELECT DISTINCT".$fieldList.$sql);
        $list =  $this->getPersonEntries($statement);
        $result = new stdclass();
        $result->totalItems = $totalItems;
        $result->pageNumber = $this->pageNumber;
        $result->itemsPerPage = $this->itemsPerPage;
        $result->list = $list;
        //TTracer::ShowArray($result);
        return $result;
    }


        /*
    public function getDownload() {
        TTracer::Trace('download not implemented');
        ob_start();
        header("Content-type: application/excel");
        header("Content-Disposition: attachment; filename=quiplist.csv");
        print('"memberName","firstName","middleName","lastName","email","phone","address1","address2","city","state","postalCode","country"'."\n");
        $statement = TSqlStatement::ExecuteQuery($sql);


        $statement->instance->bind_result(
            $personId,
            $username,
            $membershipId,
            $membershipType,
            $firstName,
            $middleName,
            $lastName,
            $memberName,
            $email,
            $phone,
            $expiration,
            $address1, $address2, $city, $state, $postalCode,
            $country, $phone, $phone2, $email, $email2);

        while($statement->next()) {
           printf('"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"'."\n",
                $memberName,
                $firstName,
                $middleName,
                $lastName,
                $email,
                $phone,
                $address1,
        	    $address2,
        	    $city,
        	    $state,
        	    $postalCode,
                $country);
        }
        exit;
    }
        */


    public static function Search($firstName, $lastName, $addressName, $searchOption,
        $pageNumber = 0, $itemsPerPage = 0) {
        $search = new TDirectorySearchList($firstName,$lastName,$addressName,$searchOption,$pageNumber);

        return $search->getSearchResult();
    }


    public static function Create($firstName, $lastName, $addressName, $searchOption) {
        TTracer::Trace('Create');
        $search = new TDirectorySearchList($firstName,$lastName,$addressName,$searchOption);
    }

}


// end TContactList