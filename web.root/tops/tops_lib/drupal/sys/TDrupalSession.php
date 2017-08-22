<?php

//  OOPS,  Doesent work. Using Tcookie session for now
class TDrupalSession extends TPhpSession //TSession
{
/*
    public function getSessionValue($key) {
        return sess_read($key);
        return '';
    }
    protected function commitValue($key,$value) {
        TTracer::Trace("Session write: $key = $value");
        sess_write($key,$value);
    }
*/
}
// TDrupalSession


