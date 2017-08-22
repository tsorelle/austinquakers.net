<?php
/** Class: TNullComponent ***************************************/
///  use as place holder for empty component value
/*******************************************************************/
class TNullComponent extends TUIComponent
{
    private static $instance;

    public function render()
    {
        return '';
    }  //  render

    public static function GetValue() {
        if (!isset(TNullComponent::$instance))
            TNullComponent::$instance = new TNullComponent();

        return TNullComponent::$instance;
    }
}   // finish class TNullComponent

// backward compatibility
$NULL_COMPONENT = TNullComponent::GetValue();
function nullComponent()
{
    return TNullComponent::GetValue();
}  //  nullComponent


