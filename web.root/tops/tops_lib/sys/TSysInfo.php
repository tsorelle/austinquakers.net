<?php
/*****************************************************************
Class:  TSysInfo
Description:   Provides operating system information
*****************************************************************/
class TSysInfo
{
    public static function IsWindows()
    {
        if ( isset($_ENV["OS"] ))
            $os = $_ENV["OS"];
         else
            $os = PHP_OS;
         return (strtoupper(substr($os,0,3)=='WIN'));
    }

    public static function GetHostName() {
        $result = $_SERVER["HTTP_HOST"];
        if ($result == 'localhost') {
            $machine = getenv('COMPUTERNAME');
            if (!empty($machine))
                $result = $machine;
        }
        return $result;
    }
}
// end TSysInfo

// deprecated function
function TSysInfo_IsWindows()
{
  return TSysInfo::IsWindows();
}  //  TSysInfo_IsWindows

