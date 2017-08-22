<?php
/** Class: TClassLib ***************************************/
/// Singleton that stores and supplies path information for class library locations
/**
See TClassPath for useage example
/*******************************************************************/
class TClassLib
{
    private static $instance;
    private $topsLibPath;
    private $siteLibPath;

    /// Class factory method
    public static function Create(
        /// path to TOPS library. Default: $DOCUMENT_ROOT/tops/tops_lib
        $topsLib=null,
        /// path to site library. Default: $DOCUMENT_ROOT/tops/site_lib
        $siteLib=null
        ) {
       if (isset(self::$instance))
            return; // redundant call

        if (!isset($topsLib))
            $topsLib = $_SERVER['DOCUMENT_ROOT'].'/tops/tops_lib';
        if (!isset($siteLib))
            $siteLib = $_SERVER['DOCUMENT_ROOT'].'/tops/site_lib';

        $topsLib = realpath($topsLib);
        if ($topsLib === FALSE)
            throw new Exception("TopsLib path not found '$topsLib'");
        $siteLib = realpath($siteLib);
        if ($siteLib === FALSE)
            throw new Exception("SiteLib path not found '$siteLib'");

        self::$instance = new TClassLib();
        self::$instance->topsLibPath = $topsLib;
        self::$instance->siteLibPath = $siteLib;

        $separator = self::GetPathSeparator();
        $topsRoot = realpath($topsLib.'/..');
        $path = sprintf('%s%s%s', ini_get('include_path'),$separator,$topsRoot);
        $siteRoot = realpath($siteLib.'/..');
        if ($topsRoot != $siteRoot)
            $path .= $separator.$siteRoot;
        ini_set("include_path", $path);

        // for debug when needed.
        // echo "<p>TClassLib path= $path</p>";
    }

    private static function getPathSeparator() {
        if ( isset($_ENV["OS"] ))
            $os = $_ENV["OS"];
         else
            $os = PHP_OS;
         return (strtoupper(substr($os,0,3)=='WIN')) ? ';' : ':';
    }

    /// Return location of standard TOPS library
    public static function GetTopsLib() {
        if (!isset(self::$instance))
            self::Create();
        return self::$instance->topsLibPath;
    }


    /// Return location of web site specific library
    public static function GetSiteLib() {
        if (!isset(self::$instance))
            self::Create();
        return self::$instance->siteLibPath;
    }

    /// Append file path the site library path
    public static function GetSiteFile($filePath) {
        return self::GetSiteLib()."/$filePath";
    }

    /// Append file path the TOPS library path
    public static function GetTopsFile($filePath) {
        return self::GetTopsLib()."/$filePath";
    }

}   // finish class TClassPath



