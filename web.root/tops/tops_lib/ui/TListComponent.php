<?php
/** Class: TLineItem ***************************************/
/// Render an LI element for BulletList or OrderedList
/*****************************************************************/
class TLineItem extends TTagComponent {

    public function TLineItem() {
        $this->tagName = 'li';
    }

}//  TLineItem


/** Class: TListComponent ***************************************/
/// Base class for TOrderedList (OL) and TBulletList (UL)
/*******************************************************************/
class TListComponent extends TTagComponent {

    public function addLine($text, $cssClass = null) {
        $lineItem = new TLineItem();
        if (isset ($cssClass))
            $lineItem->setCssClass($cssClass);
        $lineItem->add(new TTextItem($text));
        $this->add($lineItem);
    }
    //  AddText

    public function addObject($component) {
        $lineItem = new TLineItem();
        $lineItem->add($component);
        $this->add($lineItem);
    }
    //  AddComponent

}
//  BulletList


