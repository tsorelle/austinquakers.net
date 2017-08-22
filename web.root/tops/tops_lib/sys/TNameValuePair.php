<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/25/2016
 * Time: 6:17 AM
 */
class TNameValuePair
{
    /**
     * @param $name
     * @param $value
     * @return stdClass
     */
    public static function Create($name,$value) {
        $result = new stdClass();
        $result->Name = $name;
        $result->Value = $value;
        return $result;
    }

    /**
     * @param array $a
     * @param $name
     * @param $value
     */
    public static function Add(Array &$a, $name,$value)
    {
        $item = self::Create($name, $value);
        array_push($a, $item);
    }
}