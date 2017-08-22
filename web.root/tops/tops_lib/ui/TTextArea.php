<?php
/** Class: TTextArea ***************************************/
/// renders text area field
/**
*****************************************************************/
class TTextArea extends TUIComponent
{
    private $rows;
    private $columns;
    private $name;
    private $cssClass;
    private $value;

    public function __construct($name,$value,$rows=0,$columns=0,$cssClass='') {
        $this->name = $name;
        $this->value = $value;
        $this->cssClass = $cssClass;
        $this->rows = $rows;
        $this->columns = $columns;
    }

    public function render() {
        $rows =  $this->rows == 0 ? '' : sprintf(' rows="%d" ',$this->rows);
        $columns =  $this->columns == 0 ? '' : sprintf(' columns="%d" ',$this->columns);
        $class = empty($this->cssClass) ? '' : sprintf('class="%s"',$this->cssClass);
        return sprintf('<textarea %s id="%s" name="%s" %s %s>%s</textarea>',
            $class, $this->name, $this->name,$rows, $columns, $this->value);
    }

    public static function CreateFieldSet($name,$legend,$value,$rows=0,$columns=0,$fieldSetClass='',$textAreaClass='') {
        TTracer::Trace('CreateTextAreaFieldSet');
        $fieldSet = new TFieldSet($name.'Panel',$legend,$fieldSetClass);
        $fieldSet->add(new TTextArea($name,$value,$rows,$columns,$textAreaClass));
        return $fieldSet;
    }
}
// end TTextArea
