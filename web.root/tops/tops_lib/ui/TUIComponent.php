<?php
/** Class: TUIComponent ***************************************/
/// Abstract base class for all renderable components.
/*******************************************************************/
abstract class TUIComponent implements IRenderable
{
    // public function render(); defined in IRenderable

    /// Calls render() for simplified syntax
    public function __toString() {
        return $this->render();
    }
}   // finish class TUIComponent

