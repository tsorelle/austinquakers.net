<?php
/** Class: TApplicationClient ***************************************/
/// Base class for form controllers and builders used by an application
/**
* See TApplication
*****************************************************************/
class TApplicationClient
{
    protected $application;
    protected $pageController;

    public function __construct($application) {
    }

    protected function initialize($application, $pageController=null) {
        TTracer::Trace('TApplicationClient::initialize');
            $this->application = $application;
        if (empty($pageController))
            $this->pageController=$application->pageController;
        else
            $this->pageController=$pageController;
    }


    public function __toString() {
        return 'TApplicationClient';
    }
}
// end TApplicationClient




