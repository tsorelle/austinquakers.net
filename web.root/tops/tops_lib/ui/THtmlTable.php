<?php
/** Class: TTableRow ***************************************/
/// Creates a TR element
/*******************************************************************/
class TTableRow extends TTagComponent {

    public function __construct($cssClass=null) {
        if (!empty($cssClass))
			$this->setCssClass($cssClass);
        $this->tagName = "tr";
    }

    public function addCell($item,$cssClass=null) {
        $this->add(THtmlTable::CreateCell($item, $cssClass));
    }
    //  AddCell

    public function addHeader($item, $cssClass = null) {
        $this->add(THtmlTable::CreateHeader($item, $cssClass));
    }
    //  AddCell

    public function addLinkCell($item, $url, $cssClass = null) {
        $this->add(THtmlTable::CreateLinkCell($item,$url,$cssClass));
    }
    //  AddLinkCell
}


/** Class: TTableCell *******************************************
/// Table TD element
*****************************************************************/
class TTableCell extends TTagComponent {

    public function __construct($cssClass=null) {
        $this->tagName = "td";
        if (isset ($cssClass))
            $this->setAttribute('class', $cssClass);
    }

    public function setColSpan($value) {
        $this->setAttribute('colspan',$value);
    }
    public function setRowSpan($value) {
        $this->setAttribute('colspan',$value);
    }

}

/*** Class: THeaderCell  ***************************************/
/// Description:
/*****************************************************************/
class THeaderCell extends TTableCell {

    public function __construct($cssClass = NULL) {
        $this->tagName = "th";
        if (isset ($cssClass))
            $this->setAttribute('class', $cssClass);
    }
}
//  THeaderCell

/** Class: THtmlTable ***************************************/
/// Renders an html table
/*******************************************************************/
class THtmlTable extends TTagComponent {
    private $headerStyle;
    private $rowStyle;
    private $altRowStyle;
    private $rowCount = 0;

    public function __construct($id=null, $cssClass=null) {
        $this->tagName = 'table';
        $this->setCellSpace(0,0);
        if (!empty($cssClass))
			$this->setCssClass($cssClass);
       if (!empty($id))
            $this->setId($id);
    }

    public function setCellSpace($cellpadding, $cellspacing = 0) {
        $this->setAttribute('cellpadding',$cellpadding);
        $this->setAttribute('cellspacing',$cellspacing);
    }

    public function setSummary($value) {
        $this->setAttribute('summary',$value);
    }

    public function getRowCount() {
        return $this->rowCount;
    }

    public static function Create($id=null, $title=null, $summary=null, $cssClass=null) {
        $result = new THtmlTable($id,$cssClass);
        if (!empty($title))
            $result->setTitle($title);
        if (!empty($summary))
            $result->setSummary($summary);
    	return $result;
    }

    public function addHeaderRow($row, $cssClass=null) {
        if (!empty($cssClass))
            $row->setAttribute('class',$cssClass);
        else
            if (isset($this->headerStyle))
                $row->setAttribute('class',$this->headerStyle);
        $this->add($row);
    }

    public function addColumnTitles($titles,$headerClass=null) {
        TTracer::Trace('columnTitles');
        if (!is_array($titles))
            $titles = explode(',',$titles);
        $row = new TTableRow($headerClass);
        foreach ($titles as $title) {
            $cell = new THeaderCell($headerClass);
            $cell->add($title);
            $row->add($cell);
        }
        $this->addRow($row,$headerClass);
    }


    public function addRow($row, $cssClass=null) {
        if (!empty($cssClass))
            $row->setAttribute('class',$cssClass);
        else {
            $zebra = $this->rowCount % 2;
            if ($zebra && isset($this->altRowStyle))
                $row->setAttribute('class',$this->altRowStyle);
            else if (isset($this->rowStyle))
                $row->setAttribute('class',$this->rowStyle);
        }
        $this->rowCount++;
        $this->add($row);
    }

    /// Class factory method to create a TR
    public static function CreateRow($cssClass=null) {
        return new TTableRow($cssClass);
    }

    /// Class factory method to create a TD
    public static function CreateCell($item=null,$cssClass=null) {
        $result = new TTableCell($cssClass);
        if (!empty($item))
            $result->add($item); // item can be text or object
        return $result;
    }

    /// Class factory method to create a TH
    public static function CreateHeader($item=null,$cssClass=null) {
        $result = new THeaderCell($cssClass);
        if (!empty($item))
            $result->add($item); // item can be text or object
        return $result;
    }

    /// Class factory method to create a cell containing a hyperlink
     public static function CreateLinkCell($item, $url, $cssClass=null) {
        $link = new THyperLink($url);
        $link->add($item);
        $cell = new TTableCell($cssClass);
        $cell->add($link);
        return $cell;
    }
} //  THtmlTable


