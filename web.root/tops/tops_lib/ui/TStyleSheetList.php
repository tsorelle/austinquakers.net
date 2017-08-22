<?php
/** Class: TStyleSheetList ***************************************/
/// used by TAbstractPageController
/*******************************************************************/
class TStyleSheetList
{
     private $links;

     public function __construct() {
        $this->links = new TUIContainer();
     }

     public function add($media, $href) {
        $this->links->add(new TLinkTag('stylesheet',$href,'text/css',$media));
     }

     public function render() {
        return $links->render();
     }
}   // finish class TStyleSheetList


