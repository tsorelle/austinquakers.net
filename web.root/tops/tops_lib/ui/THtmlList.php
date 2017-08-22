<?php
/** Class: TOptionContainer ***************************************/
///   Base class for SELECT and OPTGROUP components.
/******************************************************************/
abstract class TOptionContainer extends TTagComponent {

    public function addOption($value, $text, $selected) {
        $option = new TSelectOption($text);
        $option->attributes['value'] = $value;
        if ($selected)
            $option->attributes['selected'] = true;
        $this->add($option);
    }
    //  AddOptionValue

    public function addOptions($optionList,$selectedValue=false) {
        foreach($optionList as $value => $text) {
            $this->addOption($value, $text, ($value == $selectedValue));
        }
    }

    public function addSelection($value, $text, $selectedValue) {
        if (is_array($selectedValue))
            $selected = in_array($value, $selectedValue);
        else
            $selected = ($selectedValue == $value);
        $this->addOption($value, $text, $selected);
    }
    //  AddSelectedOption

}

/***Class:  TSelectOption **************************************/
/// An OPTION component used inside a SELECT or OPTGROUP
/*****************************************************************/
class TSelectOption extends TTagComponent {

    public function __construct($text) {
        $this->tagName = "option";
        $this->add(new TTextItem($text));
    }

    public function select() {
        $this->attributes['selected'] = true;
    }
    //  Select

    public function unselect() {
        $this->attributes['selected'] = false;
    }
    //  SetSelected

    public function setValue() {
        $this->attributes['value'] = $value;
    }
    //  SetValue

    public function getValue() {
        if (isset ($this->attributes['value']))
            return $this->attributes['value'];
        return '';
    }
    //  GetValue

}

/*****Class: TSelectBox *************************************************/
/// A box style SELECT
/*****************************************************************/
class TSelectBox extends TOptionContainer {

    public function __construct($name, $cssClass=null, $size = NULL) {
        $this->tagName = 'select';
        $this->attributes['name'] = $name;
        $this->attributes['id'] = $name;
        if (isset ($size))
            $this->attributes['size'] = $size;
    }

    public function enableMultiSelect() {
        $this->attributes['multiple'] = true;
        $this->attributes['name'] .= '[]';
    }
    //  SetMultiSelect

}

/**** Class: TOptionGroup ***************************************
/// An OPTGROUP component, used inside a SELECT
*****************************************************************/
class TOptionGroup extends TOptionContainer {
    public function __construct($label) {
        $this->tagName = 'optgroup';
        $this->attributes['label'] = $label;
    }
}

/** Class: TDropDownList ***************************************/
/// Renders a dropdown list
/*******************************************************************/
class TDropDownList extends TOptionContainer {
    public function __construct($name, $cssClass=null) {
        $this->tagName = 'select';
        $this->attributes['name'] = $name;
        $this->attributes['id'] = $name;
    }
}

/***  Class:   THtmlList ****************************************/
/// Class factory for HTML List Controls
/*****************************************************************/
class THtmlList {
    public static function CreateSelectBox($name, $cssClass=null, $size = NULL) {
        return new TSelectBox($name,$cssClass, $size);
    }

    public static function CreateDropDown($name, $cssClass=null) {
        return new TDropDownList($name, $cssClass);
    }

    public static function CreateDropDownList($items, $selectedValue, $name, $cssClass = null) {
        $result = new TDropDownList($name, $cssClass);
        $result->addOptions($items, $selectedValue);
        return $result;
    }

    public static function CreateOptionGroup($label) {
        return new TOptionGroup($label);
    }
}



