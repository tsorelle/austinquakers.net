<?php
/*****************************************************************
Class:  TestClass
Description:
*****************************************************************/
class TTestClass
{
    public function __construct() {
        TTracer::Trace('TTestClass::constructor');
    }

    public function __toString() {
        return 'TestClass';
    }
}
// end TestClass



