<?php
/** Class: TBingMapsApi ***************************************/
/// Bing maps api implementations
/**
*****************************************************************/
class TBingMapsApi extends TMappingApi
{
    public function __construct() {
        parent::__construct('Bing Maps (REST)');
        // ,'AmJHqh8ngg_f7C0PNeHAP4mItyJND58Q5APnSDiRcwISEPUJZ2uQs78PWNBDVnah'
    }

    public function GetBingUrl($address1, $address2, $city, $province, $postalCode, $country = 'US') {
        // Construct the Locations API URI
        $address = $address1;
        if ($address2) {
            if ($address1)
                $address.=' ';
            $address .= $address2;
        }

        $serviceUrl =
            "http://dev.virtualearth.net/REST/v1/Locations?CountryRegion=$country";
        if ($province)
            $serviceUrl .= "&adminDistrict=$province";
        if ($city)
            $serviceUrl .= "&locality=$city";
        if ($postalCode)
            $serviceUrl .= "&postalCode=$postalCode";
        if ($address)
            $serviceUrl .= "&addressLine=$address";

        $findUrl = str_ireplace(" ","%20",$serviceUrl).$query."&output=xml&key=".$this->serviceCode;

        TTracer::Trace('<a href="'.$findUrl.'">Service Request</a>');
        return $findUrl;
    }

    public function QueryBingLocation($url) {
        $result = new stdClass();
        $result->FoundLocation = false;
        $output = @file_get_contents($url);
        if (!$output) {
            $result->Status = 'Service URL not found.';
            return $result;
        }

        // create an XML element based on the XML string
        try {
            $response = new SimpleXMLElement($output);
        }
        catch(Exception $e)  {
            $result->Status = 'Invalid query result from service';
        }

        $result->Status = (string)$response->StatusDescription;
        if ($result->Status != 'OK') {
            $result->Status == "Error: HTTP Status Code $result->Status";
        }

        $authenticationCode = (string)$response->AuthenticationResultCode;
        if ($authenticationCode != 'ValidCredentials') {
            $this->Status == "Error authentication failure: $authenticationCode";
        }

        $result->Location = $response->ResourceSets->ResourceSet->Resources->Location;
        $result->Latitude = (string)$response->ResourceSets->ResourceSet->Resources->Location->Point->Latitude;
        $result->Longitude = (string)$response->ResourceSets->ResourceSet->Resources->Location->Point->Longitude;
        if (is_numeric($result->Latitude) && is_numeric($result->Longitude))
            $result->FoundLocation = true;

        // TTracer::ShowArray($result);
        return $result;
    }

    public function GetLocation($address1, $address2, $city, $province, $postalCode, $country = 'US') {
        $result = new stdClass();
        $result->Successful = false;

        $url = $this->GetBingUrl($address1, $address2, $city, $province, $postalCode, $country = 'US');
        $queryResult = $this->QueryBingLocation($url);
        if ($queryResult->FoundLocation) {
            $result->Successful = true;
            $result->Location = new TGeoLocation($queryResult->Latitude,$queryResult->Longitude);
        }
        return $result;
    }
}
