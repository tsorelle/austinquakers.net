<?
  class TPasswordsApplication extends TApplication {

    public function __construct() {
//        set_error_handler('tops_error_handler');
        $this->setApplicationName('passwords');
        $this->pageController = new TFmaMembersPageController();
        TDrupalPageController::SetInstance($this->pageController);
        $this->setCssPath('/fma/style');
        // $this->pageController->setFormVariable('id', 'directory');
        $this->pageController->setHeaderSubtitle('User Password');
        $this->pageController->setPageTitle('FMA - Password Manager');
       // $this->addStyleSheet('common/formStyles');
    }

    protected function run($cmd=null) {
        if ($_SERVER['SCRIPT_NAME'] != '/index.php' || !TUser::Authenticated())
            return;

        if (empty($cmd))
            $cmd = $this->request->getCommand('showPwdForm');
        TTracer::Trace("run: $cmd");
        // $this->addReturnCrumbs($cmd);
        switch ($cmd) {
            case 'showPwdForm' :
                $this->executeAction('showPasswordForm');
                break;
            case 'changePassword' :
                TTracer::Trace('changePassword');
                if (!$this->executeAction('changePassword'))
                    $this->executeAction('showPasswordForm');
                break;
             case 'showUserMenu' :
                $this->pageController->redirectLocal("/userinfo");
                break;

        }

    }
}
