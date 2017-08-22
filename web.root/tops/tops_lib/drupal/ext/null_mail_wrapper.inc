<?php

function drupal_mail_wrapper($message) {
/*
Use this to override Drupal mail for testing on
installations with no SMTP server installed.

To install, add this line to settings.php

  $conf['smtp_library'] = 'tops_ext/null_mail_wrapper.inc';

*/
    return true;
}

