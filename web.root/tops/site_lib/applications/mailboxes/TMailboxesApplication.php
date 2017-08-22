<?php
/** Class: TMailboxesApplication ***************************************/
/// contact-us page with e-mail form
/**
*****************************************************************/
class TMailboxesApplication extends TApplication
{
    public function __construct() {
//        restore_error_handler ();
//        set_error_handler('tops_error_handler');
//        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//        error_reporting(E_ALL);

        $this->setApplicationName('mailboxes');
        $this->pageController = TDrupalPageController::GetInstance();
        $this->pageController->setFormVariable('id', 'mailboxes');
        //$this->pageController->setHeaderSubtitle('QUIP Mailboxes');
        $this->pageController->setPageTitle('Mailboxes');
    }

    protected function run($cmd=null) {
        if ($_SERVER['SCRIPT_NAME'] != '/index.php' || !TUser::Authenticated())
            return;

        TTracer::Trace('TMailboxesApplication::run');
        TTracer::Trace("run: $cmd");
        switch ($cmd) {
            case 'showList' :
                $this->showList();
                break;
            case 'editMailbox' :
                $this->editMailbox();
                break;
            case 'updateMailbox' :
                $this->updateMailbox();
                break;
            case 'dropMailbox' :
                $this->dropMailbox();
                break;
            case 'addMailbox' :
                $this->addMailbox();
                break;
            default :
                $this->pageController->addErrorMessage("Command '$cmd' is not implemented.");
                break;
        }
    }

    private function showList() {
        TTracer::Trace('showList');
        $list = TMailbox::GetList();
        $url = $newBoxUrl = $this->pageController->getPostBackUrl('cmd=%s');

        $content = TMailboxListing::Build($list,$url);
        $this->pageController->addMainContent($content);
    }

    private function editMailbox($mailBox=null) {
        TTracer::Trace('editMailbox');
        if (empty($mailBox)) {
            $boxId = TRequest::GetValue('mailboxId',0);
            $mailBox = TMailbox::Find($boxId);
        }
        $this->pageController->setFormVariable('mailboxId', $mailBox->getId());

        $content = TMailboxForm::Build($mailBox);
        $this->pageController->addMainContent($content);
        // build the form
    }

    private function getMailboxFromRequest() {
        TTracer::Trace('getMailboxFromRequest');
        $request = TRequest::GetInstance();
        $mailBox = TMailbox::Find(0);
        $mailBox->setId($request->get('mailboxId',0));
        $mailBox->setName($request->get('name',''));
        $mailBox->setEmail($request->get('email',''));
        $mailBox->setDescription($request->get('description',''));
        $mailBox->setMailboxCode($request->get('mailboxCode',''));
        $selectionList = ($request->isChecked('selectionList')) ? 1 : 0;
        $mailBox->setSelectionList($selectionList) ;
        return $mailBox;
    }


    private function updateMailbox() {
        TTracer::Trace('updateMailbox');
        $mailBox = $this->getMailBoxFromRequest();
        $errors = $mailBox->validate();
        if ($mailBox->isValid()) {
            if ($mailBox->getId() == 0) {
               if (TMailbox::Exists($mailBox->getMailboxCode())) {
                    $this->pageController->addErrorMessage('Mailbox code already used.');
                    $this->editMailBox($mailBox);
                    return;
               }
               else   {
                    $mailBox->add();
                    $this->pageController->addInfoMessage('Mailbox entry added.');
               }
            }
            else {
                $mailBox->update();
                $this->pageController->addInfoMessage('Mailbox entry updated.');
            }
            $this->showList();
        }
        else {
            foreach($errors as $message) {
                $this->pageController->addErrorMessage($message);
            }
            $this->editMailBox($mailBox);
        }
    }

    private function dropMailbox() {
        TTracer::Trace('dropMailbox');
        $id = TRequest::GetValue('mailboxId',0);
        if ($id) {
            TMailbox::Drop($id);
            $this->pageController->addInfoMessage('Mailbox deleted.');
        }
        $this->showList();
    }

    private function addMailbox() {
        TTracer::Trace('addMailbox');
        $mailBox = TMailbox::Find(0);
        $this->editMailbox($mailBox);
    }

}
