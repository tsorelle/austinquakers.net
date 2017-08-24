<?php
/** Class: TGoogleMapsApi ***************************************/
/// Google maps api implementations
/**
*****************************************************************/
class TGoogleMapsApi extends TMappingApi
{

    public function __construct() {
        parent::__construct('Google Maps');
        //,'ABQIAAAA0_GLIkmgBPIhRTs-w0tdbBTrjr2IFnpc3ZOGyM3U6eja5Mv7UhRxLPsK_5NnfUhMbhuoIZSxgnWlLA');
    }

    public function GetGoogleUrl($address1, $address2, $city, $province, $postalCode, $country = 'US') {
        // Construct the Locations API URI
        $address = $address1;
        if ($address2) {
            if ($address1)
                $address.=' ';
            $address .= $address2;
        }

        $addressParams = $address;
        if ($city)
            $addressParams .= ",+$city";
        if ($province)
            $addressParams .=  ",+$province";
        if ($postalCode)
            $addressParams .= "+$postalCode";

        $addressParams = str_replace('#',' ',$addressParams);
        $addressParams = str_replace('?',' ',$addressParams);
        $addressParams = str_replace('&',' ',$addressParams);
        $addressParams = str_replace(" ","+",$addressParams);

        $settings =  TConfiguration::GetSectionSettings('maps');

        // geocodeurl.Google='https://maps.googleapis.com/maps/api/geocode/xml?address=[[address]]&sensor=false&key=[[key]]'
        $apiKey = $settings['providerkey.Google'];
        $mappingUrl = $settings['geocodeurl.Google'];
        $mappingUrl = str_replace('[[key]]',$apiKey,
            str_replace('[[address]]',$addressParams,$mappingUrl));

        return $mappingUrl;
    }

    public function QueryGoogleLocation($url) {
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

        $result->Status = (string)$response->status;
        if ($result->Status != 'OK') {
            $result->Status == "Error: HTTP Status Code $result->Status";
        }

        $result->Location = $response->result->geometry->location;
        $result->Latitude = (string)$response->result->geometry->location->lat;
        $result->Longitude =  (string)$response->result->geometry->location->lng;
        if (is_numeric($result->Latitude) && is_numeric($result->Longitude))
            $result->FoundLocation = true;

        TTracer::ShowArray($result);
        return $result;
    }

    public function GetLocation($address1, $address2, $city, $province, $postalCode, $country = 'US') {
        $result = new stdClass();
        $result->Successful = false;

        $url = $this->GetGoogleUrl($address1, $address2, $city, $province, $postalCode, $country = 'US');
        $queryResult = $this->QueryGoogleLocation($url);
        if ($queryResult->FoundLocation) {
            $result->Successful = true;
            $result->Location = new TGeoLocation($queryResult->Latitude,$queryResult->Longitude);
        }
        return $result;
    }
}
