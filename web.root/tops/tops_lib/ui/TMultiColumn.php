<?php
/** Class: TMultiColumn ***************************************/
/// place compontnets in two or three column layour
/**
*****************************************************************/
class TMultiColumn extends TUIComponent
{
    private $left;
    private $center;
    private $right;
    private $columnCount;

        public function __construct($columnCount, $id = NULL, $attributes = NULL) {
            $this->columnCount = $columnCount;
             if ($this->columnCount == 2) {
                 $this->left =new TDiv('left');
             }
             else if ($this->columnCount == 3) {
                 $this->left = new TDiv('col1');
                 $this->center = new TDiv('col2');
             }
             $this->right = new TDiv('right');
        }

        public function addLeft($item) {
            $this->left->add($item);
        }

        public function addCenter($item) {
            if (columnCount < 3)
                throw new Exception('Only two columns in this div.');
            $this->center->add($item);
        }

        public function addRight($item) {
            $this->right->add($item);
        }




        public function render() {
             $outer = new TDiv('columns'.$this->columnCount);
             if ($this->columnCount == 2) {
                 $outer->add($this->left);
             }
             else if ($this->columnCount == 3) {
                 $leftColumn  = new TDiv('left');
                 $leftColumn->add($this->left);
                 $leftColumn->add($this->center);
                 $outer->add($leftColumn);
            }
            $outer->add($this->right);
            return $outer->render();

        }

        public static function CreateTwoColumn($id = NULL, $attributes = NULL) {
            return new TMultiColumn(2,$id,$attributes);
        }

        public static function CreateThreeColumn($id = NULL, $attributes = NULL) {
            return new TMultiColumn(3,$id,$attributes);
        }


}
