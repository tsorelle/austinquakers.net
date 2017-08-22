<?php
/** Class: TAuthentication ***************************************/
/// Helper class to return username
/*******************************************************************/
class TAuthentication
{
    /// Return user name from TUser object or OS.
    public static function GetCurrentUserName()
    {
        global $SITE_USER;

        if (isset($SITE_USER))
            // for backward compatibility only
            $user = $SITE_USER;
        else
            $user = TUser::GetCurrentUser();

        if (!empty($user))
            return $user->getUserName();

        if (isset($_SERVER['REMOTE_USER']) )
            return $_SERVER['REMOTE_USER'];

        if ( isset($_SERVER['PHP_AUTH_USER']))
            return $_SERVER['PHP_AUTH_USER'];

        return "guest";
    }  //  GetCurrentUserName
}   // finish class TAuthentication

