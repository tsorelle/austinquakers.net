<?php
/** Class: TCommandLink ***************************************/
/// link to command postback
/**
*****************************************************************/
class TCommandLink
{
    public function __construct() {
    }

    public function __toString() {
        return 'TCommandLink';
    }

    public static function Create($cmd,$text,$hint = '', $params=null) {
        $url = '/?q='.$_GET['q'].'&cmd='.$cmd;
        if (!empty($params))
            $url .= '&'.$params;
        // return THyperLink::getTextLink($url, $text, $hint,'commandLink');
        return sprintf('<a href="%s" class="commandLink" title="%s">%s</a>',$url,$hint,$text);
    }

}
// end TCommandLink