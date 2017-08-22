<?
// TODO: Update for new site
include_once("$fmaRoot/php/classes/TLookupTable.php");

class TMemberStatusLookup extends TLookupTable {
  function TMemberStatusLookup()
  {
    $this->tableName = 'memberstatus';
    $this->idField   = 'memberStatusID';
    $this->displayField = 'statusDescription';
  }  //  TMemberStatusLookup
}
?>
