<?php
/** Class: TTagComponent ***************************************/
/// Base class for any component that randers an HTML element.
/**
* See class factory THtml
*****************************************************************/
class TTagComponent extends TUIContainer {
    protected $tagName;
    public $attributes;
    protected $requiresEndTag = true;

    public function __construct($tagName) {
        $this->tagName = $tagName;
    }

    private function getAttributeList() {
        if (empty($this->attributes))
            return '';
        $attributeList = '';
        foreach ($this->attributes as $attribute => $value) {
            if ($value === true)
                $attributeList .= ' ' . $attribute;
            else if ($value !== false && (trim($value) != '') ) {
                $attributeList .= sprintf(' %s="%s"', $attribute, $value);
            }
        }
        return $attributeList;
    }

    public function render() {
        $attributeList = $this->getAttributeList();
        $count =$this->getComponentCount();
        if ($count == 0)
            return sprintf('<%s%s/>'."\n", $this->tagName, $attributeList);
        return sprintf("<%s%s>\n%s</%s>\n",
            $this->tagName,
            $attributeList,
            $this->renderComponents(),
            $this->tagName);
    }

    public function setAttribute($attribute, $value) {
        $this->attributes[$attribute] = $value;
    }
    //  SetAttribute

    public function setTagName($name) {
        $this->tagName = $name;
    }
    //  SetTagName

    public function setCssClass($cssClass) {
        $this->attributes['class'] = $cssClass;
    }
    //  SetClass

    public function setID($ID) {
        $this->attributes['id'] = $ID;
    }
    //  SetClass

    public function setTitle($value) {
        $this->attributes['title'] = $value;
    }
    //  setTitle

    public function getName() {
        if (isset ($this->attributes['name']))
            return $this->attributes['name'];
        return '';
    }
    //  GetName

    public function setName($value) {
        $this->attributes['name'] = $value;
    }
    //  GetName

    public function getComponentType() {
        return $this->tagName;
    }
    //  GetComponentType
}
//  TagComponent


