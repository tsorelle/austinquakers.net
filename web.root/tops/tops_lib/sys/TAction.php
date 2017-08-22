<?php
/** Class: TAction ***************************************/
///  Abstract base class for command pattern
/*****************************************************************/
abstract class TAction
{
    protected $args = array();
    protected $argCount = 0;


    protected $actionId;
    public function __construct($id) {
        $this->actionId = $id;
    }

    protected abstract function run();

    public function execute() {
        return $this->run();
    }

    public function __toString() {
        return isset($this->actionId) ? $this->actionId : 'TAction';
    }

    protected function setArgs($argCount,$args) {
        $this->argCount = $argCount;
        if ($argCount > 0)
            $this->args = $args;
        else
            $this->args = array();
    }

    protected function getArgCount() {
        return $this->argCount;
    }

    protected function getArg($index = 0, $default='') {
        if ($index <= ($this->argCount - 1))
            return $this->args[$index];
        return $default;
    }
}
// end TAction



