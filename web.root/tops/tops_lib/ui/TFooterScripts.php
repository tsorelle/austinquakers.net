<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/23/2017
 * Time: 6:56 AM
 */

class TFooterScripts
{
    private static $srcs = array();
    public static function Add($src) {
        self::$srcs[] = $src;
    }

    public static function Render()
    {
        if (!empty(self::$srcs)) {
            print("\n");
            foreach (self::$srcs as $src) {
                print "<script src='$src'></script>\n";
            }
            self::$srcs = array();
        }
    }


}