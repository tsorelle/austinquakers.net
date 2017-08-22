<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/11/2015
 * Time: 5:33 AM
 */
class TText
{
    public static function HtmlToText($text) {
        if (empty($text)) {
            return '';
        };
        $text = str_ireplace("</p>","\n\n",$text);
        $text = str_ireplace("</div>","\n\n",$text);
        $text = str_ireplace("<br>","\n",$text);
        $text = str_ireplace("<br/>","\n",$text);
        $lines = explode("\n",$text);
        $text = '';
        foreach($lines as $line) {
            $text .= trim($line)."\n";
        }
        $text = self::ConvertToUtf8($text);
        return $text;
    }

    public static function HtmlToFlatText($text) {
        if (empty($text)) {
            return '';
        };
        $text = str_replace("\n",' ',$text);
        $text = str_replace("\r",'',$text);
        $text = self::ConvertToUtf8($text);
        return $text;
    }

    /**
     * @param $text
     * @return string
     *
     * Convert to serializeable string
     */
    private static function ConvertToUtf8($text) {
        $text = strip_tags($text);
        // replace actual question marks to keep from losing them later...
        $text = str_replace('?','~q~',$text);
        // html_entity_decode with ISO-8859-1 takes care of special characters
        $text = html_entity_decode($text,ENT_COMPAT,'ISO-8859-1');
        // But, we need it to convert to UTF-8 to serialize over the ajax calls
        $text = mb_convert_encoding($text,"ISO-8859-1",'UTF-8');
        // Non-ascii characters are converted to '?', get rid of them
        $text = str_replace('?',' ',$text);
        // restore legitimate question marks
        $test = str_replace('~q~','?',$text);
        return trim($text);
    }
}