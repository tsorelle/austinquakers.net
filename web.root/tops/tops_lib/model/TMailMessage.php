<?php
// die("called deprecated TMailMessage class.")
require_once "Mail.php"; // from PEAR

class TMailMessage {
  var $messageBody;
  var $recipientList;
  var $fromAddress;
  var $subject;
  var $identity;
  var $returnAddress;

  function addRecipient($recipient) {
    if (empty($this->recipientList)) {
      $this->recipientList = $recipient;
    }
    else  {
      if (!is_array($this->recipientList)) {
        $firstRecipient = $this->recipientList;
        $this->recipientList = array();
        array_push($this->recipientList,$firstRecipient);
      }
      array_push($this->recipientList,$recipient);
    }
  }
//  addRecipient
  function setRecipient($recipient) {
    $this->recipientList = $recipient;
  }
//  setRecipient
  function setFromAddress($value) {
//$this->fromAddress = "From: ".$value."\n";
    $this->fromAddress = $value;
  }
//  setFromAddress
  function setIdentity($value) {
    $this->identity = $value;
  }
//  setIdentity
  function setReturnAddress($value) {
    $this->returnAddress = $value;
  }
//  setReturnAddress
  function setSubject($value) {
    $this->subject = stripslashes($value);
  }
//  setSubject
  function setMessageBody($value) {
    $value = str_replace("\r\n", "\n", $value);
    $this->messageBody = stripslashes($value);
  }
//  setMessageBody
  function send() {
      global $DOCUMENT_ROOT;
    // Default SMTP Config
    $host = "localhost";
    $auth = false;
    $username = '';
    $password = '';

    // Read local SMTP settings
    $configPath = $DOCUMENT_ROOT.'/php/includes/smtpconfig.php';
    if (file_exists($configPath))
        include($configPath);
    $auth = (!empty($username));

    // Set headers and params
    if (empty ($this->identity))
      $from = $this->fromAddress;
    else
      $from = '"' . $this->identity . '"<' . $this->fromAddress . '>';

    if (is_array($this->recipientList))
        $to = $this->recipientList[0];
    else
        $to = $this->recipientList;

    $headers = array (
      'From' => $from,
      'To' => $to,
      'Subject' => $this->subject,
      'Date' => date("r"));

    if (!empty ($this->returnAddress)) {
        $headers['Return-Path'] = $this->returnAddress;
    }

    // Send messge
    $smtp = Mail::factory('smtp',
      array (
      'host' => $host,
        'auth' => $auth,
        'username' => $username,
        'password' => $password));


    $mail = $smtp->send(
        $this->recipientList,
        $headers,
        $this->messageBody);

    if (PEAR::isError($mail)) {
        return $mail->getMessage();
     } else {
        return true;
     }
  }
//  send
}

class TWebClerkMessage extends TMailMessage {
  function TWebClerkMessage() {
    $this->recipientList = 'websupport@austinquakers.org';
  }

}
