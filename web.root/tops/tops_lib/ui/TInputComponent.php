<?php
require_once ('tops_lib/global/definitions/phptags.php');

/** Class: TInputComponent ***************************************/
/// Base class for input components such as text fields, text areas and buttons
/*******************************************************************/
class TInputComponent extends TTagComponent {

    public function __construct($type = NULL, $name = NULL, $value = NULL) {
        $this->initInput($type, $name, $value);
    }
    //  TInputComponent

    protected function initInput($type = NULL, $name = NULL, $value = NULL) {
        $this->tagName = 'input';
        if (isset ($type))
            $this->setAttribute('type', $type);
        if (isset ($name))
            $this->setAttribute('name', $name);
        if (isset ($value))
            $this->setAttribute('value', $value);
    }
    //  initInput

    public function setValue($value) {
        $this->setAttribute('value', $value);
    }
    //  setNameValue

    public function setName($name) {
        $this->name = $name;
        $this->setAttribute('name', $name);
    }
    //  setName

    public function setNameValue($name, $value) {
        $this->setName($name);
        $this->setAttribute('value', $value);
    }
    //  setNameValue

}
// finish class TInputComponent


