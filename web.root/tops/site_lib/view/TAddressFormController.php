<?php
/*****************************************************************
Class: TAddressFormController
Description:
*****************************************************************/
class TAddressFormController
{
    private static $instance;
    private $pageController;
    private $request;

    public function __construct($pageController) {
        $this->pageController = $pageController;
        $this->request = TRequest::GetInstance();
    }


    public function __toString() {
        return 'TAddressFormController';
    }


    public function getRequestData() {
        TTracer::Trace('getRequestData');
        //TRequest::PrintArgs();
        $formData = new stdClass();
        $pid =  $this->request->get('pid',0);
        $aid =  $this->request->get('aid',0);
        $this->pageController->setFormVariable('pid',$pid);
        $this->pageController->setFormVariable('aid',$aid);

        $formData->pid = $pid;
        $formData->aid = $aid;

        $formData->address = $this->getAddressFromRequest();

        $errors = $formData->address->validate();

        foreach($errors as $message)
            $this->pageController->addErrorMessage($message);

        $formData->isValid = $formData->address->isValid();

        return $formData;
    }

    public function getAddressData($aid=0) {
        TTracer::Trace("getAddressData($aid)");
        $formData = new stdClass();
        $formData->address = new TAddress();
        $formData->aid = $aid;
        $formData->pid = $this->request->get('pid',0);
        if (!empty($aid)) {
            $formData->address->select($aid);
        }
        $this->pageController->setFormVariable('aid',$formData->aid);
        $this->pageController->setFormVariable('pid',$pid);

        return $formData;

    }

    private function getAddressFromRequest() {
        TTracer::Trace('getAddressFromRequest');

        $address = new TAddress();
    	$address->setId($this->request->get('aid',0));
        $addressName = $this->request->get('addressName','');
        $addressType = $this->request->get('addressType',1);
        $address->setAddressType(empty($addressType) ? 1 : $addressType);
        $address->setAddressName($this->request->get('addressName','') );
        $address->setAddress1($this->request->get('address1','') );
        $address->setAddress2($this->request->get('address2','') );
        $address->setCity($this->request->get('city','') );
        $address->setState($this->request->get('state','') );
        $address->setPostalCode($this->request->get('postalCode','') );
        $address->setCountry($this->request->get('country','') );
        $address->setPhone($this->request->get('phone','') );
        $address->setNotes($this->request->get('notes',''));
        $address->setSortkey($this->request->get('sortkey',''));
        $address->setFnotes($this->request->isChecked('fnotes') ? 1 : 0);
        $address->setDirectoryCode($this->request->isChecked('directoryCode') ? 1 : 0);

        return $address;
    }

    public static function Create($pageController) {
        if (!isset(self::$instance))
            self::$instance = new TAddressFormController($pageController);
        return self::$instance;
    }
}
// end TAddressFormController
