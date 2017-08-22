<?php
/** Class: TTextItem ***************************************/
///  A class for adding text to a component collection
/*******************************************************************/
class TTextItem extends TUIComponent {
    private $text;

    public function __construct($text) {
        $this->text = $text;
        $this->type = 'Text';
    }

    public function render() {
        return $this->text;
    }
    //  render

}
//  TextItem


