<?php
require_once("tops_lib/sys/TFilePath.php");
/** Class: TClassPath ***************************************/
/// Used by __autoload to locate classes
/**
*****************************************************************/
class TClassPath
{
    private static $dirs = array();

    /// Add a set of class library directories to class search
    /**
    Example<pre>
        TClassPath::Add(TClassLib::GetTopsLib(),
            'sys','db','model','view','ui','drupal/sys');

    </pre>
    */
    public static function Add(
        /// Root file path of class libraries. Sub directories follow.
        $root ) {
        $argCount = func_num_args();
        $args = func_get_args();
        if ($argCount == 1)
            array_push(TClassPath::$dirs, $root);
        else {
            for ( $i=1; $i<$argCount; $i++ )
            {
                array_push(TClassPath::$dirs, $root.'/'.$args[$i]);
            }
        }
    }

    public static function IncludeClass($className) {
        foreach(TClassPath::$dirs as $dir) {
            $classFile = $dir.'/'.$className.'.php';
            if (TFilePath::Exists($classFile)) {
                require_once($classFile);
                return;
            }
        }
    }
}   // finish class TClassPath


spl_autoload_register('TClassPath::IncludeClass');

/*
function __autoload($className)
{
    TClassPath::IncludeClass($className);
}  //  __autoload
*/
