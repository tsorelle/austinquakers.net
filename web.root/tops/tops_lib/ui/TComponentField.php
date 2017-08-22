<?php
/** Class: TComponentField ***************************************/
/// Used by TFieldSet to render a text field
/*******************************************************************/
class TComponentField extends TAbstractFormField
{
    private $component;

    public function __construct($labelText,$labelClass,$component) {
        $this->component = $component;
        $this->labelText = $labelText;
        $this->labelClass = $labelClass;
    }

    public function renderInput() {
        return $this->component->render();
    }

}
// TFormField

