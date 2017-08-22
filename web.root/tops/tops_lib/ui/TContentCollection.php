<?php
/** Class: TContentCollection ***************************************/
/// Holds content and include file references used by TPageController
/*****************************************************************/
class TContentCollection
{
    private $items = array();

    public function add($item) {
        array_push($this->items, $item);
    }

    public function addFile($path) {
        array_push($this->items, new TFilePath($path));
    }

    public function getContent() {
        return($this->items);
    }

}   // finish class TContentCollection


