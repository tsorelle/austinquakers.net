<?php
/** Class: THtml ************************************************/
/// A Class factory for common HTML elements
/**
Instantiates frequently used TOPS UI components using the classes
TTagComponent and TInputComponent
   - paragraph
   - span
   - header (H)
   - radio button
   - checkbox
   - submit button
*****************************************************************/
class THtml
{

    /// Create any tag element
    public static function CreateTextElement($tagName, $text=null, $cssClass=null) {
        $result = new TTagComponent($tagName);
      	if (!empty($cssClass))
            $result->setCssClass($cssClass);
        if (!empty($text))
    		$result->addText($text);
    	return $result;
    }

    /// Create any input element
    public static function CreateInputElement($type,$name,$value,$cssClass=null) {
        $result = new TInputComponent($type,$name,$value);
        if (isset($cssClass))
            $result->setCssClass($cssClass);
      	return $result;
    }

    /// Create a &lt;p&gt; tag component.
    public static function Paragraph($text=null, $cssClass=null) {
        return THtml::CreateTextElement('p', $text, $cssClass);
    }

    /// Create a &lt;span&gt; tag component.
    public static function Span($text=null, $cssClass=null) {
        return THtml::CreateTextElement('span', $text, $cssClass);
    }

    /// Create a &lt;h&gt; tag component.
    public static function Header($level, $text=null, $cssClass=null) {
        return THtml::CreateTextElement('h'.$level, $text, $cssClass);
    }
    /// Create a checkbox control
    public static function CheckBox($name,$checked=false) {
        $result = new TInputComponent('checkbox',$name,1);
        $result->setAttribute('checked',(!empty($checked)));
      	return $result;
    }

    /// Create a radio button control
    public static function RadioButton($name,$checked=false) {
        $result = new TInputComponent('radio',$name,1);
        $result->attributes['checked'] = (!empty($checked));
      	return $result;
    }

    /// Create a submit button
    /**
    * Consider using TAction button in most cases
    */
    public static function SubmitButton($name,$value,$cssClass=null) {
        return THtml::CreateInputElement('submit',$name,$value,$cssClass);
    }

    public static function TextField($name,$value,$cssClass=null) {
        return THtml::CreateInputElement('text',$name,$value,$cssClass);
    }

    public static function HiddenField($name,$value) {
        return THtml::CreateInputElement('hidden',$name,$value);
    }
}
// end THtmlInput



