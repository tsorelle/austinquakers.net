<?php
/** Class: TMappingApi ***************************************/
/// Abstract class for mapping functions
/**
*****************************************************************/
abstract class TMappingApi
{
    protected $serviceCode;
    protected $serviceName;

    public function __construct($serviceName, $serviceCode = '') {
        $this->serviceCode = $serviceCode;
        $this->serviceName = $serviceName;
    }

    public function __toString() {
        return $this->serviceName;
    }

    public function setServiceCode($value) {
        $this->serviceCode = $value;
    }

    public function getServiceCode() {
        return $this->serviceCode;
    }

    public function getServiceName() {
        return $this->serviceName;
    }

    public static function Create($providerName = null) {
        $configValues =  TConfiguration::GetSectionSettings('maps');
        if (empty($providerName)) {
            $providerName = $configValues['geoprovider'];
        }
        $result = null;
        eval('$result = new T'.$providerName.'MapsApi();');
        $key = $configValues['providerkey.'.$providerName];
        if (!empty($key))
            $result->setServiceCode($key);
        return $result;
    }

    public static function GetProviderKey($providerName = null) {
        $configValues =  TConfiguration::GetSectionSettings('maps');
        if (empty($providerName)) {
            $providerName = $configValues['geoprovider'];
        }
        $key = $configValues['providerkey.'.$providerName];
        return $key;

    }

    public static function GetCurrentProvider() {
        $result = new stdClass();
        $configValues =  TConfiguration::GetSectionSettings('maps');
        $result->providerName = $configValues['geoprovider'];
        $result->key = $configValues['providerkey.'.$result->providerName];
        if (isset($configValues['scripturl.'.$result->providerName]))
            $result->scriptUrl = urldecode($configValues['scripturl.'.$result->providerName]);
        if (isset($configValues['mxn.'.$result->providerName]))
            $result->mxn = $configValues['mxn.'.$result->providerName];

        // cannot store URLs in config due


        return $result;
    }

    public abstract function GetLocation($address1, $address2, $city, $province, $postalCode, $country = '');


}
// end TMappingApi
