<?php
// TODO: Update for new site
  // assumes standardclasses.php previously included
  include_once("$fmaRoot/php/classes/TSearch.php");
  class TPersonSearch  extends TSearch
  {
    var $directoryFilter;

    function TPersonSearch() {
      $this->rowCount = 0;
      $this->params = '';
      $this->directoryFilter = false;
    }

    function setDirectoryFilterOn() {
        $this->directoryFilter = true;
    }

    function getPersonId() {
      return $this->result->get('personId');
    }

    function getName()
    {
      return
        $this->result->get('lastName').', '.
        $this->result->get('firstName').' '.
        $this->result->get('middleName');
    }  //  getName

    function getPersonsList($whereClause) {
        $filterClause =
           ($this->directoryFilter) ?
                ' AND (a.addressId is null OR a.DirectoryCode = 1) ' :
                '';
    $querySql =
        'SELECT personId, firstName, middleName, lastName '.
        'FROM persons p '.
        'LEFT OUTER JOIN addresses a ON p.addressId = a.addressId '.
        'WHERE p.active = 1 '.$filterClause.
        'AND ('.
        $whereClause.
        ') ORDER BY lastName,FirstName';

      $this->getList($querySql);
   // echo $querySql.'<br>';
    }


    function getByAddress($addressId)
    {
      $result = $this->getPersonsList("a.addressId = $addressId");
    }  //  byAddress

    function getOthersAtAddress($addressId, $personId)
    {
      $result =
        $this->getPersonsList("p.addressId = $addressId AND p.personId <> $personId");
    }  //  byAddress

    function getAll()
    {
      $this->getPersonsList("1=1");
    }

    function getByName($searchOption, $firstName, $middleName, $lastName, $addressName)
    {
      if ($firstName)
        $firstName = $this->getCompareClause($searchOption,'firstName',$firstName);

      if ($middleName)
        $middleName = $this->getCompareClause($searchOption,'middleName',$middleName);

      if ($lastName)
        $lastName = $this->getCompareClause($searchOption,'lastName',$lastName);

      if ($addressName)
        $addressName = $this->getCompareClause($searchOption,'addressName',$addressName);

     $clause = $firstName;

      if ($middleName) {
        if ($firstName)
          $clause .= ' AND ';
        $clause .= $middleName;
      }

      if ($lastName) {
        if ($firstName || $middleName)
          $clause .= ' AND ';
        $clause .= $lastName;
      }

      if ($addressName) {
        if ($firstName || $middleName || $lastName)
          $clause .= ' AND ';
        $clause .= $addressName;
      }

      $this->getPersonsList($clause);
      return $this->rowCount;
    }

    // ********** Methods below are deprecated *****************

    function setUrl($value) {
      $this->url = $value;
    }

    function setParams($value)
    {
      $this->params = $value;
    }  //  setParams

    function addParam($name,$value)
    {
      $this->params .= '&'.$name.'='.$value;
    }  //  addParam

    function getLink() {
      $query = $this->result;
      if (!$query)
        return "ERROR: No query result";
      $name =
        $query->get('lastName').', '.
        $query->get('firstName').' '.
        $query->get('middleName');

      return
        '<LI><A HREF='.
        $this->url.'?pid='.
        $query->get('personId').
        $this->params.
        '>'.$name.'</A>';
    }


  } // finish class personSearch

?>
