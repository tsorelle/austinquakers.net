<?php
class TShowPersonForm extends TPageAction
{
    private function buildButtonPanel($formData) {
        TTracer::Trace('buildButtonPanel');
        $buttonPanel = new TFieldSet('personFormButtons','','inlineButtons');
        $buttonPanel->add(new TActionButton('update', 'updatePerson','Save' ));
        $cancelAction = empty($formData->pid) ? 'search' : 'showPerson';
        $buttonPanel->add(new TActionButton('cancel', $cancelAction ,'Cancel'));
        return $buttonPanel;
    }


    protected function run() {
        TTracer::Trace('TShowPersonForm.run');
		$formController = TPersonFormController::Create($this->pageController);
        if ($this->argCount == 0) {
            $formData = $formController->getFormData();
        }
        else {
            $formData = $this->getArg();
        }

        $buttonPanel = $this->buildButtonPanel($formData);
        $form = TPersonEditForm::Build($formData, $buttonPanel);
        $this->pageController->addMainContent($form);

    }

}
// TShowPersonForm



