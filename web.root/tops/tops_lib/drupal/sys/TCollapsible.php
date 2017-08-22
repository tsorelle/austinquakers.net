<?php
class TCollapsible extends TTagComponent {

    public function __construct($id, $title, $collapsed = true) {
        $this->tagName = 'fieldset';
        $legend = new TTagComponent('legend');
        $legend->add($title);
        $this->add($legend);
        $cssClass = 'collapsible';
        if ($collapsed) {
            $cssClass .= " collapsed";
        }
        $this->setCssClass($cssClass);
        $this->setId($id);
    }


    public function addText($text) {
        $this->add('<div>'.$text.'</div>');
    }


    /// Class factory method
    public static function Create($id, $title, $collapsed = true, $content = null) {
        $result = new TCollapsible($id,$title,$collapsed);
        if ($content != null)
            $result->add($content);
        return $result;
    }

}
//  TDiv
