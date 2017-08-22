<?php
/** Class: TCheckBoxDiv ***************************************/
/// Render a check box and label inside a DIV
/*******************************************************************/
class TCheckBoxDiv extends TUIComponent
 {
     private $name;
     private $text;
     private $title;
     private $checked;

     public function __construct($name, $text, $title, $checked=false) {
         $this->name = $name;
         $this->text = $text;
         $this->title = $title;
         $this->checked = $checked;
     }

     public function render() {
        return sprintf(
            '<div class ="checkBoxDiv"><input type="checkbox" name="%s" id="%s" title="%s" %s /> %s</div>',
            $this->name, $this->name, $this->title,
            $this->checked ? 'CHECKED' : '',
            $this->text);
    }

 }
 // end TCheckBoxComponent

