<?
// TODO: Update for new site
// prerequisite: standardclasses.php
include_once("$fmaRoot/php/classes/TLookupTable.php");
class TCommitteeStatusLookup extends TLookupTable {
  function TCommitteeStatusLookup() {
    $this->tableName = 'committeestatus';
    $this->idField   = 'statusId';
    $this->displayField = 'description';
  }
}  //  TCommitteeStatusLookup.php
?>
