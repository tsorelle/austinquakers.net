<?php
class TShowAddressForm extends TPageAction
{
    private function buildButtonPanel($formData) {
        TTracer::Trace('buildButtonPanel');
        $buttonPanel = new TFieldSet('addressFormButtons','','inlineButtons');
        $buttonPanel->add(new TActionButton('update', 'updateAddress','Save' ));
        $cancelAction = empty($formData->aid) ? 'search' : 'showAddress';
        $buttonPanel->add(new TActionButton('cancel', $cancelAction ,'Cancel'));
        return $buttonPanel;
    }


    protected function run() {
        TTracer::Trace('TShowAddressForm.run');
		$formController = TAddressFormController::Create($this->pageController);
        if ($this->argCount == 0) {
            $formData = $formController->getAddressData();
        }
        else {
            $formData = $this->getArg();
        }

        $buttonPanel = $this->buildButtonPanel($formData);
        $form = TAddressEditForm::Build($formData, $buttonPanel);
        $this->pageController->addMainContent($form);

    }

}
// TShowPersonForm



