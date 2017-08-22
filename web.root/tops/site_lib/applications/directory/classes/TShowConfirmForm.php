<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/6/2015
 * Time: 4:37 PM
 */

class TShowConfirmForm extends TPageAction {
    private function buildButtonPanel($formData) {
        TTracer::Trace('buildButtonPanel');
        $buttonPanel = new TFieldSet('personFormButtons','','inlineButtons');
        $confirmAction = new TActionButton('confirm',$formData->confirmAction,$formData->message);
        $cancelAction = new TActionButton('cancel',$formData->cancelAction,'Cancel');
        $entityIdField = THtml::HiddenField($formData->idName,$formData->idValue);

        $buttonPanel->add($confirmAction);
        $buttonPanel->add($cancelAction);
        $buttonPanel->add($entityIdField);
        return $buttonPanel;
    }


    protected function run() {
        TTracer::Trace('TShowPersonForm.run');
        $formController = TPersonFormController::Create($this->pageController);
        if ($this->argCount == 0) {
            throw new Exception('No arguments to TShowConfirmForm');
        }
        else {
            $formData = $this->getArg();
        }

        $buttonPanel = $this->buildButtonPanel($formData);
        // $form = TPersonEditForm::Build($formData, $buttonPanel);
        $this->pageController->addMainContent($buttonPanel);

    }

}