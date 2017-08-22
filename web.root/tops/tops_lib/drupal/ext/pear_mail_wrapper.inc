<?php
 require_once "Mail.php"; // from PEAR


/*
Use this to send Drupal mail using Pear mail functions.

To install, add this line to settings.php

  $conf['smtp_library'] = 'tops_ext/pear_mail_wrapper.inc';

*/


function drupal_mail_wrapper($message) {

krumo($message);
    $host = "localhost";
    $auth = false;
    $username = '';
    $password = '';

    $smtp = Mail::factory('smtp',
      array (
      'host' => $host,
        'auth' => $auth,
        'username' => $username,
        'password' => $password));

    $headers = array();
    $headers['To'] = $message['to'];
    $headers['Subject'] = $message['subject'];
    $headers['Date'] = date("r");
    foreach ($message['headers'] as $name => $value) {
        $headers[$name] = $value;
    }

    $mail = $smtp->send(
        $message['to'],
        $headers,
        str_replace("\r", '', $message['body']));

    if (PEAR::isError($mail)) {
        watchdog("error", "mail error: ".$mail->getMessage());
        return false;
     }

    return true;
}
