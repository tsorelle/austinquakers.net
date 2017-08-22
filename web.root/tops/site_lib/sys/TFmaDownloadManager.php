<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/16/2016
 * Time: 12:05 PM
 */
class TFmaDownloadManager
{
   public function getNewsletterList() {
       $result = array();

       $sql =
           'select addressName,  address1,  address2,  city, state, postalCode, country, sortkey from addresses '
           .'where active=1 and fnotes = 1 order by sortkey,addressName';

       $statement = TSqlStatement::ExecuteQuery($sql);
       $addressName = null;
       $address1 = null;
       $address2 = null;
       $city = null;
       $state = null;
       $postalCode = null;
       $country = null;
       $sortkey = null;
       $header = '"name","address1","address2","city","state","postalCode","country","sortkey"'."\n";

       array_push($result,$header);
       $statement->instance->bind_result($addressName,$address1,$address2,$city,$state,$postalCode,$country,$sortkey);
       while ($statement->next()) {
           $record =  '"'.$addressName.'","'.$address1.'","'.$address2.'","'.$city.'","'.$state.'","'.$postalCode.'","'.$country.'","'.$sortkey.'"'."\n";
            array_push($result,$record);
       }
       return $result;
   }
}