<?php
/** Class: TDiv ***************************************/
/// Renders a DIV element
/*******************************************************************/
class TDiv extends TTagComponent {

    public function __construct($cssClass = NULL, $id = NULL, $attributes = NULL) {
        $this->tagName = 'div';
        if (!empty ($cssClass))
            $this->setCssClass($cssClass);
        if (!empty ($id))
            $this->setId($id);
        if (isset ($attributes))
            foreach ($attributes as $key => $value)
                $this->setAttribute($key, $value);
    }

    /// Class factory method
    public static function Create($id, $class = null, $attributes = null) {
        return new TDiv($class, $id, $attributes);
    }

}
//  TDiv


