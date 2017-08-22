<?php
/** Class: TCheckBoxField ***************************************/
/// labeled field for fieldset checkbox
/**
*****************************************************************/
class TCheckBoxField extends TAbstractFormField
{
    public function __construct($name,$labelText,$labelClass,$inputClass,$value) {
        $this->init($name,$labelText,$labelClass,$inputClass,$value,'checkbox');
    }

    public function renderInput() {
        $class = empty($this->inputClass) ? 'class="checkBox"' : sprintf('class="%s"',$this->inputClass);
        $checked = empty($this->value) ? '' : 'CHECKED';
        return sprintf('<input type="checkbox" %s id="%s" name="%s" value="%s" %s />',
            $class, $this->name, $this->name, $this->value, $checked);
    }

}
