<?php
/** Class: TFormController ***************************************/
/// base class for form controllers
/**
*****************************************************************/
class TFormController
{
    // private static $instance;
    protected $pageController;
    protected $request;
    public function __construct($pageController) {
        $this->pageController = $pageController;
        $this->request = TRequest::GetInstance();
    }

    public function __toString() {
        return 'TFormController';
    }
}
// end TFormController



