<?php
/** Class: THyperLink ***************************************/
/// Render an anchor tag
/*******************************************************************/
class THyperLink extends TTagComponent {

    public function THyperLink($url = null, $text = null, $hint = null, $cssClass = null) {
        $this->tagName = 'a';
        $this->attributes['href'] = empty ($url) ? '#' : str_replace('&', '&amp;', $url);
        if (isset ($text))
            $this->addText($text);
        if (!empty ($hint))
            $this->setAttribute('title', $hint);
        if (!empty ($cssClass))
            $this->setCssClass($cssClass);
    }

    public static function getTextLink($url, $text, $hint = null, $cssClass = null, $attributes = null) {
        $result = new THyperLink($url, $text, $hint);
        if (!empty ($cssClass))
            $result->setCssClass($cssClass);
        if (isset ($attributes))
            foreach ($attributes as $key => $value)
                $result->setAttribute($key, $value);
        return $result;
    }

}
//  HyperLink


