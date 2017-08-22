<?php
/*****************************************************************
Class:  TContentType
Description:
*****************************************************************/
class TContentType
{
    private $teaserFlags;
    private $bodyFlags;
    private $contentType;

    private static $types = array();

    public function __construct($contentType) {

    }

    public function __toString() {
        return 'TContentType';
    }

}
// end TContentType



