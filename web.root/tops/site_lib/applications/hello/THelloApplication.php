<?php
/** Class: TMailboxesApplication ***************************************/
/// contact-us page with e-mail form
/**
*****************************************************************/
class THelloApplication extends TApplication
{
    public function __construct() {
//        restore_error_handler ();
//        set_error_handler('tops_error_handler');
//        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//        error_reporting(E_ALL);

        $this->setApplicationName('hello');
        $this->pageController = TDrupalPageController::GetInstance();
        $this->pageController->setFormVariable('id', 'mailboxes');
        //$this->pageController->setHeaderSubtitle('QUIP Mailboxes');
        $this->pageController->setPageTitle('Test Application');
    }

    protected function run($cmd=null) {
        $this->pageController->addInfoMessage('Running test application.');
        $this->pageController->addMainContent('<h1>Hello World</h1>'.
            "Command = $cmd");
    }

}
