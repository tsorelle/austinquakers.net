<?php
// Base class for any component with a VALUE attribute
// Typically an input component should also set the NAME
// attribute in the constructor.
abstract class TValueComponent extends TTagComponent {
    public function setValue()    {
      $this->attributes['value'] = $value;
    }  //  SetValue

    public function getValue() {
      if (isset($this->attributes['value']))
        return $this->attributes['value'];
      return '';
    }  //  GetValue
}  //  TValueComponent
