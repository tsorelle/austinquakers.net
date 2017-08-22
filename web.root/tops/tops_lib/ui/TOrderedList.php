<?php

/** Class: TOrderedList ***************************************/
/// Render An OL List
/*******************************************************************/
class TOrderedList extends TListComponent {

    public function TOrderedList($cssClass = null) {
        $this->tagName = 'ol';
        if (!empty ($cssClass))
            $this->setCssClass($cssClass);
    }

}
//  OrderedList


