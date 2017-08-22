<?php
// Base class for SELECT and OPTGROUP components.
abstract class TOptionContainer extends TTagComponent {
    public function addOption($value,$text,$selected,$title=null)
    {
      $option = new TSelectOption($text);
      if ($selected)
        $option->attributes['selected'] = 'selected';
      $option->attributes['value'] = $value;
      if (!empty($title))
        $option->attributes['title'] = $title;
      $this->add($option);
    }  //  AddOptionValue

    public function addSelection($value,$text,$selectedValue) {
      if (is_array($selectedValue))
        $selected = in_array($value,$selectedValue);
      else
        $selected = ($selectedValue == $value);
      $this->addOption($value,$text,$selected);
    }  //  AddSelectedOption
}  //  TOptionContainer

// An OPTION component used inside a SELECT or OPTGROUP
class TSelectOption extends TValueComponent {
    public function TSelectOption($text) {
      $this->tagName= "option";
      $this->add(new TTextItem($text));
    }

    public function select()  {
      $this->attributes['selected'] = true;
    }  //  Select

    public function unselect() {
      $this->attributes['selected'] = false;
    }  //  SetSelected
}  //  TSelectOption


