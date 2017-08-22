<?php
/** Class: TButtonPanel ***************************************/
/// Renders a set of action buttons in a DIV
/*******************************************************************/
class TButtonPanel extends TUIComponent
{
    private $div;

    public function __construct($id='buttons',$class='buttonPanel') {
        $this->div = TDiv::Create($id, $class);
    }

    /// Add an action button. See TActionButton
    public function addButton($name, $action, $text) {
        $this->div->add(new TActionButton($name, $action, $text));
    }

    public function render() {
        return $this->div->render();
    }
}
// end TButtonPanel



