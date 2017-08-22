<?php
/** Class: TRadioButtonSet ***************************************/
/// Render a set of radio buttons enclosed in a div allow selection
/**
Deprecated: Consider using TControlSet instead.
/*******************************************************************/
class TRadioButtonSet extends TUIComponent
{
    private $name;
    private $selected = '';
    private $indented;
    private $buttons = array();
    public function __construct($name,$indented=true) {
        $this->name = $name;
        $this->indented = $indented;
    }

    public function add($value, $text, $title='') {
        $btn = new TRadioButtonDiv($this->name, $value, $text, $title);
        $btn->indent($this->indented);
        array_push($this->buttons,$btn);
    }

    public function select($value) {
        foreach($this->buttons as $btn)
            $btn->select($value);
    }

    public function render() {
        $result = '';
        foreach($this->buttons as $btn)
            $result .= $btn->render()."\n";
        return $result;
    }
}
// end TRadioButtonSet

