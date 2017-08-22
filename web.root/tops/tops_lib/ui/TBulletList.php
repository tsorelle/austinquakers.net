<?php
/** Class: TBulletList ***************************************/
/// Component to render an unordered list &lt;ul&gt;
/*******************************************************************/
class TBulletList extends TListComponent {

    public function TBulletList($cssClass = null) {
        $this->tagName = 'ul';
        if (!empty ($cssClass))
            $this->setCssClass($cssClass);
    }

}
//  BulletList


