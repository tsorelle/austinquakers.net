<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/7/14
 * Time: 6:45 PM
 */

class TConsole {

    public static function GetOutputMode() {
        if (array_key_exists('REQUEST_METHOD', $_SERVER))
            return "html";
        return "console";
    }

    public static function WriteLine($value) {
        if (self::GetOutputMode() == "html")
            print "$value<br>";
        else
            print "$value\n";
    }
} 