<?php
/** Class: TStyleSheetImportList ***************************************/
/// used by TAbstractPageController
/*******************************************************************/
class TStyleSheetImportList
{
     private $imports = Array();

     public function add($media, $path) {
        if (!isset($this->imports[$media]))
            $this->imports[$media] = Array();
        array_push($this->imports[$media], $path);

     }

     private function getImports($importGroup) {
        $result = '';
        foreach($importGroup as $import)
            $result .= "    @import \"$import\";\n";
        return $result;
     }

     private function getImportBlock($media) {
        return
            '<style type="text/css" media="'.$media.'">'."\n".
                $this->getImports($this->imports[$media]).
            '</style>'."\n";
     }

     public function renderImportBlock($media) {
        if (!isset($this->imports[$media]))
            return '';
        return  $this->getImportBlock($media);
     }

     public function renderImportBlocks() {
        $result = '';
        foreach (array_keys($this->imports) as $media) {
            $result .= $this->getImportBlock($media);
//            echo "getting block for $media<br>";
        }
        return $result;
     }

     public function renderImports($media='all') {
        if (!isset($this->imports[$media]))
            return '';
        return $this->getImports($this->imports[$media]);
     }

}   // finish class TStyleSheetList


