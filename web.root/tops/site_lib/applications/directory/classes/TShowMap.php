<?php
class TShowMap extends TPageAction
{
    //providerurl.Google=http://maps.google.com/maps/api/js?sensor=false

    protected function run() {

        TTracer::Trace('TShowMap::Run');
        $aid = TRequest::GetValue('aid',0);
        $fmaLatitude = 30.2836450;
        $fmaLongitude = -97.6948260;
        if ($aid) {
            $address = TAddressLocations::GetAddressForMap($aid);
        }
        else
            $address = false;

        if ($address) {
            $startLat = $address->lat;
            $startLon = $address->lon;
            $addressName = $address->name;
            $address = json_encode($address);
        }
        else {
            $startLat = $fmaLatitude;
            $startLon = $fmaLongitude;
            $address = 'null';
        }

        $mapProvider = TMappingApi::GetCurrentProvider();
        // TTracer::ShowArray($mapProvider);
        // $this->pageController->addScriptReference($mapProvider->scriptUrl);
       // $this->pageController->addScriptReference('/fma/js/mxn/mxn.js?('.$mapProvider->mxn.')' );

        $this->pageController->addScriptReference('/fma/js/addressmap.js');

        $legendDiv = TFieldSet::Create('map-legend','Legend');
        $legend = ($addressName ? '<img src="/fma/js/iimm1-blue.png"/> '.$addressName.' ' : '');
        $legendDiv->addText(
          $legend.'<img src="/fma/js/iimm1-green.png"/> A Friend'."'".'s House <img src="/fma/js/iimm1-red.png" /> The Meeting House'
        );
        $this->pageController->addMainContent($legendDiv);

        $mapDiv = TDiv::Create('map_canvas','map-wide');
        $this->pageController->addMainContent($mapDiv);
        $this->pageController->addMainContent(TDiv::Create('info'));



        drupal_add_js(
            "var startLat = $startLat;\n".
            "var startLon = $startLon;\n".
            "var plotted = new Array();\n".
            'var fmaAddress = {"id":"0","name":"Friends Meeting House","addr":"3701 East Martin Luther King","lat":"30.2836450","lon":"-97.696724000000000"};'."\n".
            "var mainAddress = $address;\n"

            ,'inline');

        drupal_add_js('https://maps.googleapis.com/maps/api/js?key=AIzaSyA1uWoXmHoL53T5__jvvChMYhSSbAe0TIk&callback=create_map', 'inline', 'footer', FALSE, TRUE, FALSE);
    }
}
// TViewPerson



