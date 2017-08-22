<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/20/2015
 * Time: 7:06 AM
 */

class TCachedItem {
    private $value;
    private $expiration;

    public function __construct($value,$expiration=15) {
        $this->value = $value;
        $this->expiration = new DateTime();
        $this->expiration->add(new DateInterval('PT'.$expiration.'S'));
    }

    public function hasExpired() {
        $now = new DateTime();
        return $now > $this->expiration;
    }

    public function getValue() {
        $now = new DateTime();
        return $now > $this->expiration ?  null : $this->value;
    }

    public function getActualValue() {
        return $this->value;
    }

}