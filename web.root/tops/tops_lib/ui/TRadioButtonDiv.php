<?php
/** Class: TRadioButtonDiv ***************************************/
/// Render a set of radio buttons enclosed in a div
/**
See also TRadioButtonSet
/*******************************************************************/
class TRadioButtonDiv extends TUIComponent
 {
     private $name;
     private $text;
     private $value;
     private $title;
     private $checked;
     private $indented = false;

     public function __construct($name, $value, $text, $title='', $checked=false) {
         $this->name = $name;
         $this->value = $value;
         $this->text = $text;
         if (empty($title))
            $this->title = $text;
         else
            $this->title = $title;
         $this->checked = $checked;
     }

     public function select($value) {
         $this->checked = ($this->value = $value);
     }

     public function check($value=true) {
         $this->checked = $value;
     }

     public function indent($value=true) {
         $this->indented = $value;
     }

     public function render() {
        return sprintf(
            '<div><input type="radio" name="%s" id="%s" value="%s" title="%s" %s />%s%s%s</div>',
            $this->name, $this->name, $this->value, $this->title,
            $this->checked ? 'CHECKED' : '',
            $this->indented ? '<p>' : '&nbsp;',
            $this->text,
            $this->indented ? '</p>' : '' );
    }

 }
 // end TRadioButtonDiv

