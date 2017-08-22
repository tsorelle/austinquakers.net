<?php
class TPhpSession extends TSession
{
    protected function getSessionValue($key) {
        if (isset($this->values[$key])) {
            if ($this->values[$key] == 'drop_from_session')
                return '';
            return $this->values[$key];
        }
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        return '';
    }

    protected function commitValue($key,$value) {
        if ($this->values[$key] == 'drop_from_session')
            unset($_SESSION[$key]);
        else
            $_SESSION[$key] = $this->values[$key];
    }

    protected function clearValue($key) {
        $this->values[$key] = 'drop_from_session';
    }
}
// TPhpSession



