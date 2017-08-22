<?php

/** Class: TUIContainer ***************************************/
/// Base class for ui components which contain other components.
/****************************************************************/
class TUIContainer extends TUIComponent {
    private $components;
    /// Add component to collection
    public function add($component) {
        if (!isset($component)) {
            TTracer::Trace("Attempted to add null component.");
            return;
        }

        if (empty ($this->components))
            $this->components = Array();
        if (is_string($component))
            $component = new TTextItem($component);
        array_push($this->components, $component);
    }
    //  Add

    public function addText($text) {
        $this->add(new TTextItem($text));
    }
    //  AddText

    public function render() {
        return $this->renderComponents();
    }
    //  Render

    protected function renderComponents() {
        $content = '';
        if (!empty ($this->components)) {
            foreach ($this->components as $component) {
                $content .= '  ' . $component->render() . "\n";
            }
        }
        return $content;
    }
    //  renderComponents

    public function getComponentCount() {
        if (!isset ($this->components))
            return 0;
        return count($this->components);
    }

    public function isEmpty() {
        return ((!isset ($this->components)) || (count($this->components) == 0));
    }
    //  isEmpty
}
//  TUIComponent


