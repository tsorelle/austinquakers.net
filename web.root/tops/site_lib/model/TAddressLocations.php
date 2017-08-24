<?php
/** Class: TAddressLocations ***************************************/
///
/**
*****************************************************************/
class TAddressLocations
{
    private $db;
    private $api;
    private static $instance;

    public static function GetInstance()
    {
        if (!self::$instance)
            self::$instance = new TAddressLocations();
        return self::$instance;
    }

    public function __construct()
    {
        $this->db = TPdoDatabase::CreateDatabase();
        $this->api = TMappingApi::Create();
    }

    public function __toString()
    {
        return 'TAddressLocations';
    }


    public static function GetAddressForMap($addressId)
    {
        $instance = self::GetInstance();
        return $instance->getMapAddress($addressId);
    }

    public static function GetAddressLocation($addressId)
    {
        $instance = self::GetInstance();
        return $instance->Locate($addressId);
    }

    public static function UpdateAddressLocation($addressId)
    {
        $instance = self::GetInstance();
        return $instance->updateLocation($addressId);
    }

    public static function FindAddress($addressId)
    {
        $instance = self::GetInstance();
        return $instance->getAddress($addressId);
    }

    public function getMapAddress($addressId)
    {
        $sql = 'SELECT id,name,addr,lat,lon FROM addresspoints WHERE id = ? ';

        return $this->db->Find($sql, $addressId);
    }

    public function getAddress($addressId)
    {
        $location = $this->Locate($addressId);
        if (!$location)
            return false;
        $address = $this->db->Find('SELECT addressName, address1, address2 FROM addresses WHERE addressId = ?', $addressId);
        if ($address) {
            $result = new stdClass();
            $result->address = '<a href=\"/directory?cmd=showAddress&aid=' . $addressId . '\">' .
                $address->addressName . '</a>, ';
            if ($address->address1) {
                $result->address .= $address->address1;
                if ($address->address2)
                    $result->address .= ', ';
            }

            if ($address->address2)
                $result->address .= $address->address2;


            $coords = $location->getCoordinates();
            $result->latitude = $coords->Latitude;
            $result->longitude = $coords->Longitude;

            return $result;
        } else
            return false;
    }

    public function updateLocation($addressId)
    {
        $address = $this->db->FindRow('SELECT * FROM addresses WHERE addressId = ?', $addressId);
        $loc = $this->api->GetLocation($address['address1'], $address['address2'], $address['city'], $address['state'], $address['postalCode']);
        if ($loc->Successful) {
            $coordinates = $loc->Location->GetCoordinates();
            $location = $this->db->FindRow('SELECT * FROM addresslocations WHERE addressId = ?', $addressId);
            if ($location === false) {
                // add record
                $this->addLocation($addressId,$coordinates);
            } else {
                // update record
                $this->db->ExecuteCommand('UPDATE addresslocations SET latitude = ?, longitude = ? WHERE addressId = ?',
                    $coordinates->Latitude, $coordinates->Longitude, $addressId);
            }
        }
        return $loc->location;
    }

    private function addLocation($addressId,$coordinates) {
        $this->db->ExecuteCommand('insert into addresslocations (addressId,latitude,longitude) values (?,?,?)',
            $addressId,$coordinates->Latitude,$coordinates->Longitude);
    }

    public function Locate($addressId) {
        $location = $this->db->FindRow('select * from addresslocations where addressId = ?',$addressId);
        if ($location === false) {
            $address = $this->db->FindRow('select * from addresses where addressId = ?',$addressId);
            // TTracer::ShowArray($address);
            $loc =   $this->api->GetLocation($address['address1'],$address['address2'],$address['city'],$address['state'],$address['postalCode']);
            if ($loc->Location) {
                TTracer::Trace('adding');
                $coordinates = $loc->Location->GetCoordinates();
                $this->addLocation($addressId,$coordinates);
            }
            return $loc->Location;
        }
        else {
            return new TGeoLocation($location['latitude'],$location['longitude']);
        }
    }



    public function getLocationsInBox($swLat, $swLong, $neLat, $neLong)
    {
        $sql =
           'SELECT id,name,addr,lat,lon FROM addresspoints ' .
           "WHERE (lat BETWEEN ? AND  ?) AND (lon BETWEEN ? AND ?) " .
           'ORDER BY lat,lon';

        $addresses = $this->db->Select($sql, $swLat, $neLat, $swLong, $neLong);
        // TTracer::ShowArray($addresses);
        if ($addresses)
            return $addresses;
        return array();
    }

}
// end TAddressLocations