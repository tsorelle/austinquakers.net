<?php
/** Class: TAbstractFormField ***************************************/
/// Abstract base class for labeled input components
/**
* Basis for input and textarea controls with labels.
*****************************************************************/
abstract class TAbstractFormField extends TUIComponent
{
    protected $name;
    protected $type;
    protected $labelClass;
    protected $labelText = '';
    protected $inputClass;
    protected $value;

    /// Constructor
    public function __construct($name,$labelText,$labelClass,$inputClass,$value,$type='input') {
        $this->init($name,$labelText,$labelClass,$inputClass,$value,$type);
    }

    /// Derived classes should implement this in lieu of the constructor
    protected function init($name,$labelText,$labelClass,$inputClass,$value,$type='input') {
        $this->name = $name;
        $this->labelText = $labelText;
        $this->type = $type;
        $this->labelClass = $labelClass;
        $this->inputClass = $inputClass;
        $this->value = $value;
    }

    /// Get Label
    private function getLabel() {
        if (empty($this->labelText))
            return '';
        $labelClass = empty($this->labelClass) ? '' : sprintf('class="%s" ',$this->labelClass);
        return sprintf('<label for "%s" %s>%s</label>',$this->name,$labelClass,$this->labelText);
    }

    /// Derived classes should implement this.
    /**
    * Render the input tag. The label tag preceeds and both are
    * enclosed in a div.
    */
    abstract function renderInput();

    public function render() {
        return
            '<div class="formField">'."\n".
            $this->getLabel()."\n".
            $this->renderInput()."\n".
            '</div>';

    }

}
// TAbstrictFormField




