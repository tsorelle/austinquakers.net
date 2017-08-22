<?php
/** Class: TAddAddressPerson ***************************************/
///
/**
*****************************************************************/
class TAddAddressPerson extends TPageAction
{
    private $aid;
    private $pid;

    public function __toString() {
        return 'TAddAddressPerson';
    }

    private function getEntities() {
        $result  = new stdClass();
        $person  = TPerson::GetPerson($this->pid);
        $address = TAddress::GetAddress($this->aid);
        if ($person->getId() == 0) {
            $this->pageController->redirectErrorMessage("Person not found.");
            return false;
        }
        if ($address->getId() == 0) {
            $this->pageController->redirectErrorMessage("Address not found.");
            return false;
        }

        $result->person = $person;
        $result->address = $address;
        $aid = $person->getAddressId();
        if ($aid) {
            if ($aid == $this->aid) {
                $this->pageController->redirectErrorMessage(
                    'Selected person already lives here.',
                    'directory&cmd=showAddress&aid='.$this->aid );
                return false;
            }
            $result->currentAddress = TAddress::GetAddress($aid);
        }
        return $result;
    }

    private function showConfirmationForm() {
        $entities = $this->getEntities();
        if ($entities === false)
            return;

        $this->pageController->addLocalCssImport('/fma/style/pages', 'searchForm');
        $this->pageController->setPageTitle('Confirm Person');
        // prevent page timeout on back-button

        $this->pageController->useGetMethod();

        $this->pageController->setFormVariable('aid', $this->aid);
        $this->pageController->setFormVariable('pid', $this->pid);
        $this->pageController->setFormVariable('cmd', 'addAddressPerson');

        $content = TDiv::Create('addAddressPersonConfirmForm');
        $content->add('<h2>Add Person to Address</h2>');

        $topPanel =  TFieldSet::Create($id='addAddressPersonInfo');

        $topPanel->addLabeledText('Person:',$entities->person->getFullName());
        $topPanel->addLabeledText('Add to address:',$entities->address->getAddressName());
        if ($entities->currentAddress) {
            $topPanel->addText('<p style="color:red;font-weight:bold;margin-top:8px">WARNING! This person is already assigned to an address</p>');
            $topPanel->addLabeledText('Current address:',$entities->currentAddress->getAddressName());
        }
        $content->add($topPanel);

        $buttonPanel = TDiv::Create('buttons','inlineButtons');
        $buttonPanel->add(THtml::SubmitButton('confirmButton','OK'));
        $buttonPanel->add(THtml::SubmitButton('cancelActionButton','Cancel'));
        $content->add($buttonPanel);

        $this->pageController->addMainContent($content);


    }

    private function linkPersonAddress() {
        $person  = TPerson::GetPerson($this->pid);
        if ($person->getId() == 0) {
            $this->pageController->redirectErrorMessage("Person not found.");
            return;
        }
        $person->setAddressId($this->aid);
        $person->update();

        $this->pageController->redirectInfoMessage(
            "Added '".$person->GetFullName()."' to address",
            'directory&cmd=showAddress&aid='.$this->aid);
    }

    protected function run() {
        TTracer::Trace('TAddAddressPerson->run');
        $request = TRequest::GetInstance();
        $this->aid = $request->get('aid');
        $this->pid = $request->get('pid');
        if ($request->includes('cancelActionButton')) {
            $this->pageController->addInfoMessage('Cancelled');
            $this->pageController->redirectLocal('directory&cmd=showAddress&aid='.$this->aid);
        }
        else if ($request->includes('confirmButton'))
           $this->linkPersonAddress();
        else
            $this->showConfirmationForm();
    }

}
