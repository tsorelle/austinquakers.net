<?php
class TCookieSession extends TSession
{

    private $cookieName;

    public function __construct() {
        $configuration =  TConfiguration::getSettings();
        $this->cookieName =$configuration->getValue('settings','cookie','TOPS');
        $this->loadValues();
    }

    public function getSessionValue($key) {
        if (isset($this->values[$key]))
            return $this->values[$key];
        return null;
    }

    protected function loadValues() {
        if (isset($_COOKIE[$this->cookieName])) {
            $list = explode('|',$_COOKIE[$this->cookieName]);
            $count = sizeof($list) / 2;
            for ($i=0;$i< $count; $i++) {
                $this->values[$list[$i]] = $list[$i + $count];
            }
        }
    }

    protected function clearValue($key) {
        unset($this->values[$key]);
    }

    protected function commitValues() {
        $cookieData = implode('|',array_keys($this->values)).'|'.
            implode('|',array_values($this->values));
        setcookie($this->cookieName,$cookieData);
    }

 }
// TDrupalSession


