<?php
class TDropDownList extends TOptionContainer {
    function TDropDownList($name) {
      $this->tagName = 'select';
      $this->attributes['name'] = $name;
    }
}
