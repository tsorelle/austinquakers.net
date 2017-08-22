<?
/** Class: TShowPasswordForm ***************************************/
/// change password
/************************************************************/
class TShowPasswordForm extends TPageAction
{
    protected function run() {
        TTracer::Trace('TShowPasswordForm::run');

        $panel = TFieldSet :: Create('changePwd', 'Change your password.' );
        $panel->addInputField('pwd', 'New password:'  ,'narrow','wide',  '');

        $buttonPanel = new TFieldSet('personFormButtons','','inlineButtons');
        $buttonPanel->add(new TActionButton('update', 'changePassword','Save' ));
        $buttonPanel->add(new TActionButton('cancel', 'showUserMenu' ,'Cancel'));

        $form = TDiv::Create('changePdwForm');
        $form->add($panel);
        $form->add($buttonPanel);
        $this->pageController->addMainContent($form);
    }

    public function __toString() {
        return 'TShowPasswordForm';
    }
}
