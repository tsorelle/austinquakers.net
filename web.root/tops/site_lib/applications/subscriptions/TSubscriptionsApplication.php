<?
/** Class: TSubscriptionsApplication ***************************************/
/// manage email subscriptions for user
/**
*****************************************************************/
class TSubscriptionsApplication extends TApplication
{
    public function __construct() {
        $this->setApplicationName('directory');
        $this->pageController = new TFmaMembersPageController();
        TDrupalPageController::SetInstance($this->pageController);
        $this->setCssPath('/fma/style');
        $this->pageController->setFormVariable('id', 'subscriptions');
        $this->pageController->setHeaderSubtitle('Subscriptions');
        $this->pageController->setPageTitle('FMA - Subscriptions');
        // $this->addStyleSheet('common/formStyles');
    }

    protected function run($cmd=null) {
        if ($_SERVER['SCRIPT_NAME'] != '/index.php' || !TUser::Authenticated())
            return;

        if (empty($cmd))
            $cmd = $this->request->getCommand('showSubscriptionForm');
        TTracer::Trace("run: $cmd");
        // $this->addReturnCrumbs($cmd);
        switch ($cmd) {
            case 'showSubscriptionForm' :
                $pid = $this->request->get('pid',0);
                if (empty($pid)) {
                    $pid = TUser::GetUserPersonId();
                    TTracer::Trace("Pid = $pid");
                }

                $controller = new TSubscriptionFormController($this->pageController);
                $formData = $controller->getFormData($pid);
                if ($formData === false) {
                    $this->pageController->addErrorMessage('No FMA user account found.');
                    $this->pageController->redirectLocal("userinfo");
                }
                $formData->returnForm = 'user';
                $form = TSubscriptionsForm::Build($formData);
                  $this->pageController->addMainContent($form);
                break;
            case 'updateSubscriptions' :
                $controller = new TSubscriptionFormController($this->pageController);
                $formData = $controller->getRequestData();
                if ($formData->isValid) {
                    TSubscriptions::UpdatePersonInfo($formData->pid, $formData->addressId, $formData->email, $formData->fnByMail);
                    TSubscriptions::UpdateSubscriptions($formData->pid, $formData->subscriptions);
                    $this->pageController->addInfoMessage('Subscriptions updated');
                    $this->pageController->redirectLocal("userinfo");
                }
                else {
                    $form = TSubscriptionsForm::Build($formData);
                    $this->pageController->addMainContent($form);
                }
                break;
            case 'showUserMenu' :
                $this->pageController->redirectLocal("userinfo");
                break;

        }
    }

    public function __toString() {
        return 'TSubscriptionsApplication';
    }
}
