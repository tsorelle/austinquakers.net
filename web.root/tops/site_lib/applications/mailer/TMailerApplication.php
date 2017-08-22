<?
/** Class: TSubscriptionsApplication ***************************************/
/// manage email subscriptions for user
/**
*****************************************************************/
class TMailerApplication extends TApplication
{
    public function __construct() {
        $this->setApplicationName('mailer');
        $this->pageController = new TFmaMembersPageController();
        TDrupalPageController::SetInstance($this->pageController);
        $this->setCssPath('/fma/style');
        $this->pageController->setFormVariable('id', 'mailer');
        $this->pageController->setHeaderSubtitle('Mailings');
        $this->pageController->setPageTitle('FMA - Mailings');
       // $this->addStyleSheet('common/formStyles');
    }

    protected function run($cmd=null) {
        if ($_SERVER['SCRIPT_NAME'] != '/index.php' || !TUser::Authenticated())
            return;

        if (empty($cmd))
            $cmd = $this->request->getCommand('menu');
        TTracer::Trace("run: $cmd");

        switch ($cmd) {

            case 'test' :
                $user = TUser:: GetShortName();
                TTracer::Trace("User short name = $user");
                $this->pageController->addInfoMessage("TEST $user");
                $this->showMenu();
                break;

            case 'menu' :
                $this->showMenu();
                break;

            case 'showForm' :
                $controller = new TSendMailFormController($this->pageController);
                $formData = $controller->getFormData();
                $this->showForm($formData);
                break;

            case 'send' :
                $formData = $this->getPostBack();
                if (!$formData->isValid)
                    $this->showForm($formData);
                else
                    $this->sendMessages($formData);
                break;

            case 'sendTest' :
                $formData = $this->getPostBack();
                if (!$formData->isValid)
                    $this->showForm($formData);
                else
                    $this->sendTestMessage($formData);
                break;

            case 'fnmenu' :
                $this->showFNMenu();
                break;
            case 'fnupload' :
                break;
            case 'fnshowform' :
                break;
            case 'fnsendtest' :
                break;
            case 'fnsend' :
                break;
            case 'fnlabels' :
                break;

        }
    }

    private function showForm($formData) {
        $this->pageController->addBreadCrumbCommand('List Menu','menu','Return to list menu.');
        $form = TSendMailForm::Build($formData);
        $this->pageController->addMainContent($form);
    }
    private function showMenu() {
        $elists = TEList::getElists();
        $menu = TSendMailMenu::Build($elists);
        $this->pageController->addMainContent($menu);

        $messages = TEmailQueue::GetPendingMessageStatus();
        $statusTable = TEmailQueueStatusTable::Build($messages);
        $this->pageController->addMainContent($statusTable);

    }

    private function sendMessages($formData) {
        $distributor = new TMailDistributor($formData->lid);
        $messageCount = $distributor->sendMail($formData->subject, $formData->messageText);
        $this->pageController->addInfoMessage("$messageCount messages were posted to the output queue.");
        $this->pageController->redirectLocal("/mailer");
    }

    private function sendTestMessage($formData) {
        $distributor = new TMailDistributor($formData->lid);
        $distributor->sendTestMessage($formData->subject, $formData->messageText);
        $this->pageController->addInfoMessage("One test message was sent.");
        $this->showForm($formData);
    }

    private function getPostBack() {
        $controller = new TSendMailFormController($this->pageController);
        $formData = $controller->getFormData();
        $controller->validateFormData($formData);
        return $formData;

    }

    private function showFNMenu() {
        $this->pageController->addBreadCrumbCommand('List Menu','menu','Return to list menu.');
        $menu = TFNotesMenu::Build();
        $this->pageController->addMainContent($menu);
    }

    public function __toString() {
        return 'TMailerApplication';
    }
}
