<?php

//Pear has some scrict mode violations. So must turne
//error_reporting(E_ALL ^ E_STRICT);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);


require_once "Mail.php"; // from PEAR


class TPearMailMessage {
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
echo "sending<br>";
    $config = TConfiguration::GetSettings();

    $mailMethod = $config->getValue('smtp','method','mail');
    $host = $config->getValue('smtp','host','localhost');
    $username = $config->getValue('smtp','username',false);
    $password = $config->getValue('smtp','password',false);
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
    if ($host == 'localhost')
        $smtp = Mail::factory($mailMethod);
    else
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


/*
// Tests

$testId = 'MM_Ten';

$msg = new TMailMessage();
$msg->setFromAddress('webclerk@austinquakers.org');
$msg->setIdentity('FMA Tester');

$msg->setMessageBody('This is a test message.');
$msg->setRecipient("Terry SoRelle <terrys@2quakers.net>");

//$msg->addRecipient("Terry SoRelle <terrys@2quakers.net>");
//$msg->addRecipient("Second Recipient <tls@2quakers.net>");
//$msg->addRecipient("Third Recipient <badquaker@dell.com>");

$msg->setReturnAddress("tls@2quakers.net");
$msg->setSubject("MailMessage Test $testId");

$result = $msg->send();

if ($result === true)
    echo("<p>Message ($testId) successfully sent! </p>");
else
    echo "Error: $result";
*/

