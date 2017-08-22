<?php
/** Class: TFieldsSet ***************************************/
/// Renders an html fieldset element
/*******************************************************************/
class TFieldSet extends TUIComponent
{
    private $legend = '';
    private $id = '';
    private $fields;
    private $class = '';

    public function __construct($id='',$legend='', $class='') {
        $this->legend = $legend;
        $this->id = $id;
        $this->fields = new TUIContainer();
        $this->class = $class;
    }

    /// Create and add an input (text field) component
    public function addInputField($name,$labelText,$labelClass,$inputClass,$value,$type='input') {
        $this->fields->add(new TFormField($name,$labelText,$labelClass,$inputClass,$value,$type));
    }

    /// Create and add a text area component
    public function addTextAreaField($name,$labelText,$labelClass,$inputClass,$value,$rows=0,$columns=0) {
        $this->fields->add(new TTextAreaField($name,$labelText,$labelClass,$inputClass,$value,$rows,$columns));
    }

    public function addCheckBoxDiv($name, $text, $title, $checked=false) {
        $this->fields->add(new TCheckBoxDiv($name, $text, $title, $checked));
    }

    public function addCheckBoxField($name,$labelText,$value) {
        $this->fields->add(new TCheckBoxField($name,$labelText,'','',$value));
    }

    public function addComponent($labelText,$labelClass, $component) {
        $this->fields->add(new TComponentField($labelText,$labelClass, $component));
    }

    public function addDropDown(
        $name, $labelText, $items, $selectedValue,
        $blankText = null, $blankValue=null, $labelClass = null) {
        $list = new TDropDownList($name);
        if (isset($blankValue)) {
            if (empty($blankText))
                $blankText = '';
            $list->addOption($blankValue, $blankText, ($blankValue == $selectedValue));
        }
        foreach ($items as $item) {
            $title = isset($item->title) ? $item->title : null;
            $list->addOption($item->value,$item->text, ($item->value == $selectedValue),$title);
        }
        $this->addComponent($labelText,$labelClass,$list);
    }

    /// Add text
    public function addText($text) {
        $this->fields->add(new TTextItem($text));
    }

    /// Add any component
    public function add($component) {
        $this->fields->add($component);
    }

    public function addLabeledText($label,$text) {
        $this->addLabeledComponent(
            $label,
            sprintf('<div class="labeledText">%s</div>',$text)."\n");
/*        $outer = new TDiv();
        $outer->setCssClass('formField');
        $outer->add(sprintf("<label>%s</label> ",$label));
        $outer->add(sprintf('<div class="labeledText">%s</div>',$text)."\n");

        $this->fields->add($outer);
        */
    }

    public function addLabeledComponent($label,$item) {
        $outer = new TDiv();
        $outer->setCssClass('formField');
        $outer->add(sprintf("<label>%s</label> ",$label));
        $outer->add($item);
        $this->fields->add($outer);
    }


    public function render() {
        $class = empty($this->class) ? '' : sprintf(' class="%s" ',$this->class);
        $id = empty($this->id) ? '' : sprintf(' id="%s"',$this->id);
        $legend = empty($this->legend) ? '' : "\n".sprintf('<legend>%s</legend>',$this->legend)."\n";
        return sprintf('<fieldset %s %s>%s %s'."\n".'</fieldset>',
            $id,$class, $legend,$this->fields);
    }

    public static function Create($id='',$legend='', $class='') {
        return new TFieldSet($id,$legend,$class);
    }
}
// TFieldSet


