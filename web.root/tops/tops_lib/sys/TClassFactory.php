<?php
/** Class: TClassFactory ***************************************/
/// Instatiates object based on configuration entry
/**
Intended to allow injection of specialized object implementation
as required by the site.  Settings are stored in the classes.ini
configuration.  Example: <pre>
    [classes]
    mailer=TPearMailer

    [path]
    TTraceMailer=tops_lib/sys
</pre>

The TPostOffice class uses this to instantiate a TMailer object that
handles the sending of e-mail. Alternative classes in this case can
include TPearMailer, TPhpMailer and TTraceMailer.
*****************************************************************/
class TClassFactory
{
    /// Select class and instantiate based on classes.ini entry
    public static function Create(
        /// Id used to indicate class in the ini
        $classId,
        /// Use this class if no entry found.
        $default,
        /// Use this include path for the default class
        $classPath='tops_lib/sys') {

        $config = TConfiguration::GetSettings('classes');
        $className = $config->getValue('classes',$classId);
        if (empty($className)) {
            $className = $default;
        }
        else   {
            $classPath = $config->getValue('path',$className,'site_lib/sys');
        }
        TTracer::Trace("class path='$classPath'");
        return TClassFactory::MakeObject($className, $classPath);
    }

    /// Instantiate an object based on class name and include file path
    public static function MakeObject($className, $classPath='site_lib/sys') {
        // TTracer::Trace('Creating class: '.$classPath.'/'.$className);
        $result = include_once($classPath.'/'.$className.'.php');

        if (!$result) {
            // TTracer::Trace("no result for ".$classPath.'/'.$className.'.php');
            return null;
        }

        eval('$result = new '.$className.'();');
        return $result;
    }
}

// end TClassFactory



