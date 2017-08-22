<?php
/*****************************************************************
Class:  TSession
Description:
*****************************************************************/
abstract class TSession
{
    private static $instance;

    protected $values = array();

    protected abstract function getSessionValue($key);
    protected function commitValue($key,$value) {
        throw new Exception("commitValue not defined.");
    }
    protected function clearValue($key) {
        throw new Exception("clearValue not defined.");
    }

    public function getValue($key) {
        if (isset($values[$key]))
            return $values[$key];
        return $this->getSessionValue($key);
    }

    public function setValue($key,$value) {
        $this->values[$key] = $value;
    }

    protected function commitValues() {
        // TTracer::Trace('commit');
        foreach ($this->values as $key => $value) {
            $this->commitValue($key,$value);
        }
        $this->values = array();
    }

    private static function getInstance() {
        if (!isset(TSession::$instance)) {
            //$classId,$default,$classPath=null)
           TSession::$instance =
                TClassFactory::Create('session','TPhpSession','tops_lib/sys');
        }
        return TSession::$instance;
    }

    public static function Get($key) {
        $instance = TSession::getInstance();
        return $instance->getValue($key);
    }

    public static function Clear($key) {
        $instance = TSession::getInstance();
        $instance->clearValue($key);
    }

    public static function Set($key,$value) {
        $instance = TSession::getInstance();
        $instance->setValue($key,$value);
    }

    public static function Commit() {
        if (isset(TSession::$instance))
            TSession::$instance->CommitValues();
    }
}
// end TSession



