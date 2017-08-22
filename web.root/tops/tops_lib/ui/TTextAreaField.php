<?php
/** Class: TTextAreaField ***************************************/
/// Used by TFieldSet to render text area
/*******************************************************************/
class TTextAreaField extends TAbstractFormField
{
    private $rows;
    private $columns;

    public function __construct($name,$labelText,$labelClass,$inputClass,$value,$rows=0,$columns=0) {
        $this->init($name,$labelText,$labelClass,$inputClass,$value,'textarea');
        $this->rows = $rows;
        $this->columns = $columns;
    }

    public function renderInput() {
        $rows =  $this->rows == 0 ? '' : sprintf(' rows="%d" ',$this->rows);
        $columns =  $this->columns == 0 ? '' : sprintf(' columns="%d" ',$this->columns);

        $class = empty($this->inputClass) ? '' : sprintf('class="%s"',$this->inputClass);
        return sprintf('<textarea %s id="%s" name="%s" %s %s>%s</textarea>',
            $class, $this->name, $this->name,$rows, $columns, $this->value);
    }

    public static function Create($name,$labelText,$labelClass,$inputClass,$value,$rows=0,$columns=0)
    {
        TTracer::Trace('Create');
        return new TTextAreaField($name,$labelText,$labelClass,$inputClass,$value,$rows,$columns);
    }
}
// TFormField

