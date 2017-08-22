<?php
class TShowMembershipForm extends TPageAction
{
    private function getSubmitButtons() {
        $buttonPanel = new TFieldSet('membershipButtons','','inlineButtons');
        $buttonPanel->add(new TActionButton('submit', 'updateMembership' ,'Update' ));
        $buttonPanel->add(new TActionButton('cancel', 'showContact' ,'Cancel'));

        return $buttonPanel;
    }

    protected function run() {
         TTracer::Trace('TShowMembershipForm.run');
        if ($this->argCount == 0) {
            $controller = TMembershipFormController::Create($this->pageController);
            $formData = $controller->getFormData();
        }
        else {
            TTracer::Trace('get formData arg.');
            $formData = $this->getArg();
        }
        $buttons = $this->getSubmitButtons();
        $showExpiration = TUser::Authorized('update QUIP memberships');
        $showNotes = TUser::Authorized('view QUIP member notes');
        $template = TMembershipForm::Build($formData,$buttons,$showExpiration,$showNotes);
        $this->pageController->addMainContent($template);
     }
}
// TShowMembershipForm



