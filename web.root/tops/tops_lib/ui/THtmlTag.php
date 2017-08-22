<?php

/** Class: THtmlTag ***************************************/
/// Renders HTML Tags
/**
*****************************************************************/
class THtmlTag {

    public function renderTag($tagName, $attributes, $ending = '/>') {
        if (empty ($this->attributes))
            return sprintf('<%s %s'."\n", $tagName, $ending);
        $attributeList = '';
        foreach ($this->attributes as $attribute => $value) {
            if ($value === true)
                $attributeList .= ' ' . $attribute;
            else
                if ($value !== false)
                    $attributeList .= sprintf(' %s="%s"', $attribute, $value);
        }
        return sprintf('<%s %s %s'."\n", $tagName, $attributeList, $ending);
    }
}
// end THtmlTag


