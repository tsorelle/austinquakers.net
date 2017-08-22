<?php
// TODO: Update for new site
  // assumes standardclasses.php previously included
  define('SO_ANYWHERE',1);
  define('SO_BEGINNING',2);
  define('SO_END',3);
  define('SO_EXACT',4);

  class TSearch
  {
    var $result;
    var $rowCount;

    // the properties below are deprecated
    var $url;
    var $action;
    var $params;

    function next() {
      return $this->result->next();
    }

    function getRowCount() {
      return $this->rowCount();
    }

    function getList($querySql)
    {
      $query = new TQuery();
      $query->execute($querySql);
      $this->rowCount = $query->getRowCount();
      $this->result = $query;
    }  //  getList


    function getCompareClause($searchOption, $fieldName, $value)
    {
      switch ($searchOption) {
        case SO_ANYWHERE :
          $exp = "like '%".$value."%'";
          break;
        case SO_BEGINNING :
          $exp = "like '".$value."%'";
          break;
        case SO_END :
          $exp = "like '%".$value."'";
          break;
        case SO_EXACT :
          $exp = "= '".$value."'";
          break;
      }
      return  "($fieldName ".$exp.')';
    }
  } // finish class TSearch

