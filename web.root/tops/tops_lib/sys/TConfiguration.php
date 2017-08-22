<?php
// TClassLib must be included or in autoload path.

class TConfiguration {
    private static $configs = array();
    private $config;


   public function getConfig() {
        return $this->config;
   }

   public function __construct($configPath) {
      if (!TFilePath::Exists($configPath))
         exit("Cannot find configuration file '$configPath'.");
      $this->config = parse_ini_file ($configPath , true);
   }


   public function getFlagValue($section,$name,$default=0) {
        $setting = $this->getValue($section, $name, $default);
        return ($setting == '1');
   }

   public function sectionExists($section) {
       return isset($this->config[$section]);
   }

   public function getSection($sectionName) {
        if (isset($this->config[$sectionName]))
            return($this->config[$sectionName]);
        return array();
   }

   public function getValue($section,$name,$default='') {
      if (isset($this->config[$section])) {
         $values = $this->config[$section];
         if (isset($values[$name]))
            return $values[$name];
      }
      return $default;
   }

   public function getValues($section) {
      if (isset($this->config[$section])) {
         $values = $this->config[$section];
         if (!empty($values))
            return array_values($values);
      }
      return array();
   }

   public function getSectionDefault($section) {
      if (isset($this->config[$section])) {
         $values = $this->config[$section];
         if ( isset($values['default'] ))
            return ($values['default']);
      }
      return '';
   }

   public function getValueOrSectionDefault($section,$name,$default='')
   {
      if (isset($this->config[$section])) {
         $values = $this->config[$section];
         if (isset($values[$name]))
            return $values[$name];
         if ( isset($values['default'] ))
            return ($values['default']);
      }
      return $default;

   }  //  getSectionValue

    /****** Static Methods *******/

    public static function getDatabaseSettings()
    {
        return TConfiguration::GetSettings('database');
    }  //  databaseSetting

    public static function GetSettings($configId = 'siteconfig')
    {
         if (!isset(TConfiguration::$configs[$configId])) {
             $SITE_LIB=TClassLib::GetSiteLib();
             $config = new TConfiguration("$SITE_LIB/config/$configId.ini");
             TConfiguration::$configs[$configId] = $config;
             return $config;
         }
         return TConfiguration::$configs[$configId];
    }  //  settings


    public static function GetEnvironmentType()
    {
        $configuration =  TConfiguration::getSettings();
        return $configuration->getValue('settings','environment','production');
    }  //  environmentType



    public static function GetPage($pageId)
    {
        $configuration = getSettings();
        $result = $configuration->getValue('pages',$pageId,NULL);
        if (isset($result))
            return $result;
        // assume post back
        return $_SERVER['SCRIPT_NAME'];
    }  //  getPage

    public static function GetPublicPages()
    {
        $configuration = TConfiguration::GetSettings('pages');
        $result = $configuration->getValues('public');
        return $result;
    }  //  getPage



    public static function Get($section,$name,$default='')
    {
        $configuration =  TConfiguration::GetSettings();
        return $configuration->getValue($section,$name,$default);
    }  //  public static getValue

    public static function GetSectionSettings($section) {
        $configuration =  TConfiguration::GetSettings();
        return $configuration->getSection($section);
    }



}


/***** Backward compatibility functions *****************************

    public static function ReadValue($section,$name,$default='')
    {
        // for backward compatibility
        return TConfiguration::Get($section,$name,$default);
    }  //  public static getValue


function getDefaultConfiguration() {
    return TConfiguration::GetSettings();
}

function getDatabaseConfiguration() {
    return TConfiguration::GetDatabaseSettings();
}

function getEnvironmentType()
{
    return TConfiguration::GetEnvironmentType();
}  //  getEnvioronmentType
*/


