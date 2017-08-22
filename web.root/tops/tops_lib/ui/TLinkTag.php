<?php
require_once ('tops_lib/ui/TTagComponent.php');

/** Class: TLinkTag ***************************************/
/// Renders link tag - such as CSS link
/*******************************************************************/
class TLinkTag extends TTagComponent {

    public function __construct($rel, $href, $type, $media) {
        $this->tagName = 'link';
        $this->attributes['rel'] = $rel;
        $this->attributes['href'] = $href;
        $this->attributes['type'] = $type;
        $this->attributes['media'] = $media;
        $this->requiresEndTag = false;
    }

}

