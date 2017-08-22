<?php
class TUpdateAddress extends TPageAction
{
    protected function run() {
        TTracer::Trace('TUpdateAddress.run');
		$formController = TAddressFormController::Create($this->pageController);
        if ($this->argCount == 0) {
            $formData = $formController->getRequestData();
        }
        else {
            $formData = $this->getArg();
        }
        $address = $formData->address;
        $aid = $address->getId();

        if ($aid > 0) {
            $address->update();
            $dbAction = 'updated';
        }
        else {
            $address->add();
            $aid = $address->getId();
            $formData->aid = $aid;
            $dbAction = 'added';
        }

        if (!empty($formData->pid)) {
            TPerson::ChangeAddress($formData->pid,$aid);
        }

        TAddressLocations::UpdateAddressLocation($aid);

        $this->pageController->addInfoMessage("Address $dbAction.");

        return true;
    }

}
// TUpdateAddress



?>