<?php
/** Class: TFormField ***************************************/
/// Used by TFieldSet to render a text field
/*******************************************************************/
class TFormField extends TAbstractFormField
{
    public function __construct($name,$labelText,$labelClass,$inputClass,$value,$type='input') {
        $this->init($name,$labelText,$labelClass,$inputClass,$value,$type);
    }

    public function renderInput() {
        $class = empty($this->inputClass) ? '' : sprintf('class="%s"',$this->inputClass);
        return sprintf('<input type="%s" %s id="%s" name="%s" value="%s" />',
            $this->type, $class, $this->name, $this->name, $this->value);
    }

    public static function CreateTextField($name,$labelText,$labelClass,$inputClass,$value) {
        return new TFormField($name,$labelText,$labelClass,$inputClass,$value);
    }
}
// TFormField

