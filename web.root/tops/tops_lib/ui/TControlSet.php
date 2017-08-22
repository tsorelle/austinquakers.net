<?php
/** Class: TControlSet ***************************************/
/// renders a set of controls in multiple columns
/*******************************************************************/
class TControlSet extends TUIComponent
 {
     protected $columnCount;
     protected $controls = array();

     public function __construct($columnCount=3) {
         $this->initialize($columnCount);
     }

     public function initialize($columnCount) {
        $this->columnCount = $columnCount;
     }

     /// add any control to the collection
     public function addControl($control) {
        array_push($this->controls,$control);
     }

     /// Create a check box in the collection
     public function addCheckBox($name, $text, $title, $checked=false) {
         array_push($this->controls,
            new TCheckBoxDiv($name, $text, $title, $checked));
     }

     /// Create a radio button in the collection
     public function addRadioButton($name, $value, $text, $title, $checked=false) {
         array_push($this->controls,
            new TRadioButtonDiv($name, $value, $text, $title, $checked));
     }

     /// Hold a blank spot in the list
     public function addSkip($count = 1) {
         $nullControl = TNullComponent::GetValue();
         for ($i=0; $i<$count; $i++)
            array_push($this->controls,$nullControl);
     }

     private function addControls($div, $start, $count, $max) {
         $end = $start + $count;
         if ($end > $max) {
             $end = $max;
         }
         for ($i = $start; $i < $end; $i++) {

            $div->add($this->controls[$i]);
         }
     }

     public function render() {
         $controlCount = sizeof($this->controls);
         $perColumn = ceil($controlCount / $this->columnCount);
         $start = 0;
         $outer = new TDiv('columns'.$this->columnCount);
         if ($this->columnCount == 1) {
            $this->addControls($outer,0,$perColumn,$controlCount);
         }
         else {
             $left = new TDiv('left');
             if ($this->columnCount == 2) {
                 $this->addControls($left,0,$perColumn,$controlCount);
             }
             else if ($this->columnCount == 3) {
                 $column = new TDiv('col1');
                 $this->addControls($column,0,$perColumn,$controlCount);
                 $left->add($column);

                 $column = new TDiv('col2');
                 $this->addControls($column,$perColumn,$perColumn,$controlCount);
                 $left->add($column);
             }
             $right = new TDiv('right');
             $start = ($perColumn * ($this->columnCount - 1));
             $this->addControls($right,$start,$perColumn,$controlCount);

             $outer->add($left);
             $outer->add($right);
         }
         $result = $outer->render();
         return $result;
     }

     /// Add a set of checkboxes based on a collection of descriptions
     public function addCheckBoxList(
        /// object with properties: name, desctiption, checked
        $items,
        /// Prefix used by TRequest to return multiple values
        $prefix='cat') {
        foreach($items as $item) {
            $controlId = sprintf('%s-%d',$prefix, $item->id);
            $this->addCheckBox($controlId,$item->name,$item->description, $item->checked);
        }
        return $this;
     }

     /// Class factory method. See addCheckBoxList()
     public static function BuildCheckBoxList($items, $prefix='cat') {
        $set = new TControlSet();
        $set->addCheckBoxList($items, $prefix);
        return $set;
     }
 }
 // end TControlSet


