<?php
/** Class: THtmlMenu ***************************************/
/// Renders menu items in UL list
/**
*****************************************************************/
class THtmlMenu extends TListComponent
{
    private $selectedClass;
    private $itemClass;

    public function __construct($id='', $menuClass = null) {
        $this->tagName = 'ul';
        if (!empty ($menuClass))
            $this->setCssClass($menuClass);
        if (!empty ($id))
            $this->setId($id);
        $this->selectedClass = 'selected';
        $this->itemClass = '';
    }

    public function setSelectedClass($value) {
        $this->selectedClass = $value;

    }

    public function setItemClass($value) {
        $this->itemClass = $value;

    }

    public function addItem($text,$href,$hint='',$subtext='',$selected=true, $liClass='') {
        $class = $this->itemClass;
        if ($selected)
            $class .= " $this->selectedClass";
        if (!empty($liClass))
            $liClass = 'class="'.$liClass.'"';

        $item = sprintf('<li %s><a href="%s" title="%s" class="%s">%s</a>',
            $liClass,
            $href,
            $hint,
            $class,
            $text);

        if (!empty($subtext) )
            $item .= "<br/>$subtext";
        $item .= "</li>\n" ;
        $this->add($item);

    }

    public function addCommandItem($appAlias,$text,$cmd='',$hint='',$subtext='',$selected=true)
    {
        $href = "/?q=$appAlias";
        if (!empty($cmd))
            $href .= "&cmd=$cmd";
        $this->addItem($text,$href,$hint,$subtext,$selected);
    }

    public function addSubMenu($title,$menu) {
        TTracer::Trace("addSubMenu not implemented yet");
    }


    public function __toString() {
        return 'THtmlMenu';
    }
}
