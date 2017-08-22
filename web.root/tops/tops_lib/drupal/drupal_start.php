<?php
if (str_replace('\\','/',realpath('.')) !=
    str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']))
    exit ("Main script must run from Drupal root.");
require_once ('./includes/bootstrap.inc');
//drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

