<?php

define ("UNKNOWN_AGENT","0");
define ("SUPPORTED_BROWSER","1");
define ("UNSUPPORTED_BROWSER","2");
define ("SMALL_DEVICE", "3");


/** Class: TBrowser ***************************************/
/// Get detailed information about browser agent
/*******************************************************************/
class TBrowser
{
    private  $agent = 'UNKNOWN';
    private  $version = 0;
    private  $productVer = 0;
    private  $platform = 'unknown';
    public   $userAgent = 'unknown';
    private  $productName = 'unknown';
    private  $MSIECompatible = 0;
    private  $deviceType = UNKNOWN_AGENT;


    public function getProductAndVersion($userAgent) {
        // Version and default product info
        $a = explode('/',$userAgent);
        if (!empty($a[0])) {
            $this->productName = $a[0];
            if (empty($a[1]))
                $this->version = 0;
            else {
              $a = explode(' ',$a[1]);
              $this->version = $a[0];
            }
        }

        if (strstr($userAgent,'Opera') ) {
                $this->productName='Opera';
            if (ereg( 'Opera/ ([0-9].[0-9]{1,2})',$userAgent,$versiontext)) {
                // Opera/6.05 (Windows XP; U) [en]
                $this->version=$versiontext[1];
            }
            else if (ereg('Opera ([0-9].[0-9])',$userAgent,$versiontext)) {
                $this->version=$versiontext[1];
            }
            if (strstr($userAgent,'Opera Mini'))
                  $this->agent = 'OPERA_MINI';
        }
        else if (ereg( 'Firefox/([0-9].[0-9].[0-9].[0-9])',$userAgent,$versiontext)) {
            $this->version=$versiontext[1];
            $this->productName='Firefox';
        }
        else if (ereg( 'Firefox/([0-9].[0-9].[0-9])',$userAgent,$versiontext)) {
            $this->version=$versiontext[1];
            $this->productName='Firefox';
        }
        else if (ereg( 'Safari/([0-9].[0-9])',$userAgent,$versiontext)) {
            $version=$versiontext[1];
            if ($this->version == 413)
                $this->agent = 'NOKIA';
            else
                $this->productName='Safari';
        }
        else if (strstr($this->userAgent,'Netscape')) {
            $this->productName = 'Netscape';
            // Versions prior to 6.0 may not have 'Netscape' in the header.
            if (ereg( 'Netscape([0-9])/([0-9].[0-9])',$this->userAgent,$versiontext)) {
                // Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:0.9.4.1) Gecko/20020508 Netscape6/6.2.3
                if (!empty($versiontext[2]))
                    $this->version =$versiontext[2];
                else if (!empty($versiontext[1]))
                    $this->version =$versiontext[1];
            }
            else  if (ereg( 'Netscape/([0-9].[0-9])',$this->userAgent,$versiontext)) {
                // later versions a little different
                // Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.0.2) Gecko/20021120 Netscape/7.01
                if (!empty($versiontext[1]))
                  $this->version =$versiontext[1];
            }
            else if (ereg( 'Netscape/([0-9].[0-9])',$this->userAgent,$versiontext)) {
                // Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.0.2) Gecko/20021120 Netscape/7.01
                if (!empty($versiontext[1]))
                  $this->version = $versiontext[1];
            }
        }
        else if (ereg('AOL ([0-9].[0-9])',$userAgent,$versiontext)) {
            //Mozilla/4.0 (compatible; MSIE 5.5; AOL 8.0; Windows NT 5.0)
            $this->productName='AOL';
            if (!empty($versiontext[1]))
                $this->productVer = $versiontext[1];
        }
        else if (ereg( 'AvantGO ([0-9].[0-9])',$userAgent,$versiontext)) {
            $version=$versiontext[1];
            $this->productName='AvantGO';
            $this->platform = 'PalmOS';
        }
        else if (ereg( 'Mozilla/([0-9].[0-9]{1,2})',$userAgent,$versiontext)) {
            $version=$versiontext[1];
            $this->productName='Mozilla';
        }
    }

    public function getDeviceType() {
        if ($this->platform == 'PalmOS' || $this->platform == 'WinCE')
            return SMALL_DEVICE;

        switch ( $this->agent )
        {
            case 'FIREFOX' :
            case 'SAFARI' :
                return SUPPORTED_BROWSER;
            case 'MSIE' :
                if ($this->version >= 6.0)
                    return SUPPORTED_BROWSER;
                else
                    return UNSUPPORTED_BROWSER;
            case 'OPERA' :
                if (($this->version >= 9.0) || ($this->MSIECompatible >= 6.0))
                    return SUPPORTED_BROWSER;
                else
                    return UNSUPPORTED_BROWSER;
            case 'NETSCAPE' :
            case 'AOL' :
            case 'MOZILLA' :
                return UNSUPPORTED_BROWSER;

           case 'VODAFONE' :
           case 'J-PHONE' :
           case 'UPG1 UP' :
           case 'PORTALMMM' :
           case 'AU-MIC' :
           case 'REQWIRELESSWEB' :
           case 'EPOC32-WTL' :
                return SMALL_DEVICE;
        }
        $smallDevices = array  (
            'NOKIA',
            'OPERA_MINI',
            'MOT-',
            'SAMSUNG',
            'SEC-',
            'SHARP-',
            'SIE-',
            'SONYERICSSON'
        );
        foreach($smallDevices as $device) {
            if (strpos($this->agent,$device) === 0)
                return SMALL_DEVICE;
        }
        return UNKNOWN_AGENT;
    }

    public function getMSIECompatibility($userAgent)
    {
        // Check for Microsoft compatibility, in parenthetical area
        // Means it either is MSIE or it is something like Opera
        // trying to look like MSIE.  Opera can have up to three different
        // versions - the agent version, the product version and MSIE version
        if (strstr($userAgent,'MSIE')) {
            $result = $this->version;
            if (ereg( 'MSIE ([0-9].[0-9]{1,2})',$this->userAgent,$versiontext)) {
                //Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.0.3705)
                if (!empty($versiontext[1]))
                    return $versiontext[1];
            }
        }
        return 0;
    }

    public function getPlatformFromHeader($userAgent) {
        if (strstr($userAgent,'Win'))
            return 'Windows';
        if (strstr($userAgent,'Mac'))
            return 'Macintosh';
        if (strstr($userAgent,'Linux'))
               return 'Linux';
        if (strstr($userAgent,'Unix'))
                return 'Unix';
        if (strstr($userAgent,'PalmOS') || strstr($userAgent,'PalmSource'))
            return 'PalmOS';
        if (strstr($userAgent,'Windows CE'))
            return 'Win CE';
        return 'unknown';
    }


    public function getUserAgent() {
        global $HTTP_USER_AGENT;
        global $_SERVER;
        global $_ENV;

        // $HTTP_USER_AGENT is undefined or blank in some environments
        if (!empty($HTTP_USER_AGENT))
            return $HTTP_USER_AGENT;

        if (!empty($_SERVER["HTTP_USER_AGENT"]))
            return $_SERVER["HTTP_USER_AGENT"];

        if (!empty($_ENV["HTTP_USER_AGENT"]))
            return $_ENV["HTTP_USER_AGENT"];

        return 'unknown';
    }


    public function __construct($userAgent = null) {

        if (!empty($userAgent))
            $this->userAgent = $userAgent;
        else
            $this->userAgent = $this->getUserAgent();
        $this->platform = $this->getPlatformFromHeader($this->userAgent);
        $this->MSIECompatible = $this->getMSIECompatibility($this->userAgent);
        $this->getProductAndVersion($this->userAgent);
        if ($this->productName == 'Mozilla') {
            if ($this->MSIECompatible != 0) {
                $this->productName = 'Internet Explorer';
                $this->agent = 'MSIE';
                $this->version = $this->MSIECompatible;
            }
            else if (ereg( 'rv:([0-9].[0-9])',$this->userAgent,$versiontext)) {
                // Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.1) Gecko/20020826
                if (!empty($versiontext[1]))
                  $this->productVer = $versiontext[1];
            }
        }

        if ($this->agent == 'UNKNOWN')
            $this->agent = strtoupper($this->productName);

    }


        public function getAgent() {
          return $this->agent;
        }

        public function getVersion() {
          return $this->version;
        }

        public function getPlatform() {
          return $this->platform;
        }


    public function getMSIECompatibilityVersion()
    {
      return $this->MSIECompatible;
    }  //  getMSIECompatibility()

    public function getProductName() {
      return $this->productName;
    }

    // If the get_browser public function is not supported the hosts.
    // getProperties will return false
    public function getProperties() {
      if ($this->userAgent == 'UNKNOWN')
        return false;
      return get_browser($this->userAgent);
    }

    // If the get_browser public function is not supported the hosts.
    // getProperty will return 'UNKNOWN'
    public function getProperty($value) {
      $result = $this->getProperties();
      if (empty($result)  || empty($result[$value]))
        return 'UNKNOWN';
      return $result[$value];
    }
}	// finish class TBrowser



