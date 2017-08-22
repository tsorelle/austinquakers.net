<?php
/** Class: TApplicationFactory ***************************************/
/// Class factory for TApplication objects
/*******************************************************************/
class TApplicationFactory
{
    public static function Create($appId) {
        $className = 'T'.ucfirst($appId).'Application';
        $classPath = "site_lib/applications/$appId";
        $result = TClassFactory::MakeObject($className, $classPath);
        if (!$result)
            throw new Exception("Application class $className not found in '$classPath'.");
        return $result;
    }
}
// end TQuipApplictionFactory



