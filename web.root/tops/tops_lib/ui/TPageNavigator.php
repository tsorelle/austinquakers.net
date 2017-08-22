<?php
/** Class: TPageNavigator ***************************************/
/// Adds page navigation links
/*******************************************************************/
class TPageNavigator  extends TUIContainer
{
    public function __construct($pageNumber, $pageCount, $url) {
        if ($pageNumber == 1 && $pageCount == 1) {
            return;
        }
        $prevPage = $pageNumber - 1;
        $nextPage = $pageNumber < $pageCount ? $pageNumber + 1 : 0;
        $div = new TDiv();
        $div->setCssClass('pageNav');
        if ($pageNumber > 1) {
            $div->add(new THyperlink($url.$prevPage,'Previous page'));
            if ($nextPage > 0)
                $div->addText('  ');
        }
        if ($nextPage > 0)
            $div->add(new THyperlink($url.$nextPage,'Next page'));
       $this->add($div);
    }
}   // finish class TPageNavigator


