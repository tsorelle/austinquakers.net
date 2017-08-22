<?php
/*****************************************************************

                               9/28/2007 9:30PM
*****************************************************************/
class ErrorSetting
{
    const ShowErrors = 1;
    const LogErrors  = 2;
    const ShowWarnings = 4;
    const LogWarnings = 8;
    const ShowStrictWarnings = 16;
    const LogStrictWarnings = 32;
    const ShowDetail = 64;
    const ShowStackTrace = 128;
}   // finish class ErrorSetting

/*****************************************************************
    Holds configuration settings for errors and exceptions.

                                7/1/2007 5:14PM
*****************************************************************/
class TErrorSettings
{
    private static $_settings;

    public static function Set($flags) {
        TErrorSettings::$_settings = $flags;
    }

    public static function Get() {
        if (!isset(TErrorSettings::$_settings)) {
            $configuration = TConfiguration::GetSettings();
            TErrorSettings::$_settings = 0
                | ($configuration->getValue('errors','showerrors',      1) ? (ErrorSetting::ShowErrors) : 0)
                | ($configuration->getValue('errors','logerrors',       0) ? (ErrorSetting::LogErrors) : 0)
                | ($configuration->getValue('errors','showwarnings',    1) ? (ErrorSetting::ShowWarnings) : 0)
                | ($configuration->getValue('errors','logwarnings',     0) ? (ErrorSetting::LogWarnings) : 0)
                | 0 //($configuration->getValue('errors','showstrict',      0) ? (ErrorSetting::ShowStrictWarnings) : 0)
                | 0 // ($configuration->getValue('errors','logstrict',       0) ? (ErrorSetting::LogStrictWarnings) : 0)
                | ($configuration->getValue('errors','showdetail',      1) ? (ErrorSetting::ShowDetail) : 0)
                | ($configuration->getValue('errors','showstacktrace',  1) ? (ErrorSetting::ShowStackTrace) : 0);
        }
        return TErrorSettings::$_settings;
    }
}   // finish class ErrorSettings

