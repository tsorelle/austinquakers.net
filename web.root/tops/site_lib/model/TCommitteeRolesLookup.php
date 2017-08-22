<?
// TODO: Update for new site
// prerequisite: standardclasses.php
include_once("$fmaRoot/php/classes/TLookupTable.php");
class TCommitteeRolesLookup extends TLookupTable {
  function TCommitteeRolesLookup() {
    $this->tableName = 'committeeroles';
    $this->idField   = 'roleId';
    $this->displayField = 'roleName';

  }
}  //  TCommitteeRolesLookup
?>
