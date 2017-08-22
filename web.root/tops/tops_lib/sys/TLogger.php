<?php
/*****************************************************************
    Class supports all kinds of logging
                               11/19/2006 7:24AM
*****************************************************************/
abstract class TLogger
{
    protected function getUserId()
    {
        $siteUser = TUser::GetCurrentUser();
        if (!empty($siteUser))
            return $siteUser->getUserName();
        if (isset($_SERVER['REMOTE_USER']) )
            return $_SERVER['REMOTE_USER'];
        return "guest";

    }  //  getUserId

    public function write($message, $details='', $stackTrace='')    {
        $time = date('d-M-Y h:i:s');
        $user = $this->getUserId();
        $this->writeToLog($time,$user,$message,$details, $stackTrace);
    }  //  write

    abstract protected function writeToLog($time, $user, $message, $details='', $stackTrace='');
}   // finish class TLogger


