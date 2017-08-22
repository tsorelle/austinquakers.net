<?php
/** Class: GeoLocation ***************************************/
/// Holds and formats geocoding coordinates
/**
*****************************************************************/
class TGeoLocation
{
    private $latitude;
    private $longitude;

    public function __construct($latitude, $longitude) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString() {
        return "GEO:$this->latitude;$this->longitude";
    }

    public function GetCoordinates() {
        $result = new stdClass();
        $result->Latitude = $this->latitude;
        $result->Longitude = $this->longitude;
        return $result;
    }

    public function GetVCard() {
       return  '<div class="geo">GEO:'."\n".
                ' <span class="latitude">37.386013</span>,'."\n".
                ' <span class="longitude">-122.082932</span>'."\n".
                '</div>'."\n";
    }

    public function GetGeoFormat() {
        // NOT implemented!
        throw new Exception('Not implemented.');
        /*
        <div class="geo">
         <abbr class="latitude" title="37.408183">N 37° 24.491</abbr>
         <abbr class="longitude" title="-122.13855">W 122° 08.313</abbr>
        </div>
        */

    }

}
// end GeoLocation
