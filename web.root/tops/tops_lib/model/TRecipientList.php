<?php
   // TODO: Update for new site

  if (!isset($fmaRoot))
    $fmaRoot = $DOCUMENT_ROOT;
  include_once("$fmaRoot/php/classes/TEntity.php");

  class TRecipientList extends TEntity{
    var $subscriber;

    function getListCode() {
      return $this-get('listCode');
    }

    function getListName() {
      return $this-get('listName');
    }

    function setListCode($value) {
      $this->setFieldValue('listCode',$value);
    }
    function setListName($value) {
      $this->setFieldValue('listName',$value);
    }

    function TRecipientList()
    {
      $this->tableName = 'elists';
      $this->idFieldName = 'elistId';
      $this->addField('listName',STRING_FIELD);
      $this->addField('listName',STRING_FIELD);
    }  //  TRecipientList

    function subscribe($email)
    {

    }  //  subscribe

    function subscribed($email)
    {

    }  //  subscribed


    function cancel($email)
    {

    }  //  cancel

    function changeAddress($newEmail, $oldEmail)
    {

    }  //  changeaddress

  }

  function getRecipientLists($email)
  {
    $query = new TQuery();
    $query->execute("SELECT * FROM elists");
    $i = -1;
    while ( $query->next() ) {
      ++$i;
      $result[0] = new TRecipientList($email);
    }
  }  //  getRecipientLists
