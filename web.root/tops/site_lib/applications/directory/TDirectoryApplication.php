<?php

class TDirectoryApplication extends TApplication {

    public function __construct() {
//        set_error_handler('tops_error_handler');
        $this->setApplicationName('directory');
        $this->pageController = new TFmaMembersPageController();
        TDrupalPageController::SetInstance($this->pageController);
        $this->setCssPath('/fma/style');
        $this->pageController->setFormVariable('id', 'directory');
        $this->pageController->setHeaderSubtitle('Member Directory');
        $this->pageController->setPageTitle('FMA - Directory');
        // $this->addStyleSheet('common/formStyles');


    }

    private function showConfirmForm($confirmAction,$cancelAction,$message,$idName,$idValue)
    {
        $actionRequest = new stdClass();
        $actionRequest->confirmAction = $confirmAction;
        $actionRequest->cancelAction = $cancelAction;
        $actionRequest->message = $message;
        $actionRequest->idName = $idName;
        $actionRequest->idValue = $idValue;
        $this->executeAction("showConfirmForm",$actionRequest);
    }
    protected function run($cmd=null) {
        if ($_SERVER['SCRIPT_NAME'] != '/index.php' || !TUser::Authenticated())
            return;

        if (empty($cmd))
            $cmd = $this->request->getCommand('search');
        TTracer::Trace("run: $cmd");
        $this->addReturnCrumbs($cmd);
        switch ($cmd) {
            case 'search':
            case 'searchPersons':
                $this->executeAction('directorySearch', $cmd);
                break;
            case 'showMap' :
            case 'addAddressPerson' :
                $this->executeAction($cmd);
                break;
            case 'showPerson':
            case 'showAddress' :
                $this->executeAction('showPerson');
                break;

            case 'showUserAddress' :
                TTracer::Trace("show user Address??");
                $userName = TAuthentication::GetCurrentUserName();
                $pid = TPerson::FindIdForUserName($userName);
                if (empty($pid))
                    $this->pageController->addErrorMessage("No Address record found for current user.");
                else {
                    $controller = TPersonFormController::Create($this->pageController);
                    $formData = $controller->getPersonData($pid);
                    $this->executeAction('showPerson',$formData);
                }
                break;

            case 'editPerson':
                $pid = $this->request->get('pid',0);
                if (empty($pid))
                    throw new Exception('No Person ID assigned');
                $this->editPerson($pid);
                break;
            case 'showPdwForm' :
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

            case 'addPerson':
                $this->editPerson();
                break;
            case 'updatePerson':
                $this->updatePerson();
                break;
            case 'deletePerson':
                $pid = $this->request->get('pid',0);
                $this->showConfirmForm('performDeletePerson','showPerson','Delete Person','pid',$pid);
                break;
            case 'performDeletePerson':
                $aid = $this->dropPerson();
                if ($aid) {
                    $request = new stdClass();
                    $request->pid = 0;
                    $request->aid = $aid;
                    $this->executeAction('showPerson',$request);
                }
                else {
                    $this->pageController->addInfoMessage("Person removed");
                }
                break;
            case 'deleteAddress' :
                $aid = $this->request->get('aid',0);
                $this->showConfirmForm('performDeleteAddress','search','Delete Address','aid',$aid);
                break;
            case 'performDeleteAddress' :
                $aid = $this->request->get('aid',0);
                if (empty($aid)) {
                    $this->pageController->addErrorMessage("Address not found");
                    return;
                }
                $address = TAddress::GetAddress($aid);
                if ($address->getId() != $aid) {
                    $this->pageController->addErrorMessage("Address not found");
                    return;
                }
                $address->delete();
                $this->executeAction('directorySearch', 'search');
                break;

            case 'editAddress':
                $aid = $this->request->get('aid',0);
                if (empty($aid))
                    throw new Exception('No Address ID assigned');
                $this->editAddress($aid);
                break;
            case 'addAddress' :
                $this->editAddress();
                break;
            case 'updateAddress':
                $this->updateAddress();
                break;

            case 'showSubscriptionForm' :
                $pid = $this->request->get('pid',0);
                $this->showSubscriptionForm($pid);
                break;
            case 'updateSubscriptions' :
                $controller = new TSubscriptionFormController($this->pageController);
                $formData = $controller->getRequestData();
                if ($formData->isValid) {
                    TSubscriptions::UpdatePersonInfo($formData->pid, $formData->addressId, $formData->email, $formData->fnByMail);
                    TSubscriptions::UpdateSubscriptions($formData->pid, $formData->subscriptions);
                    $this->pageController->addInfoMessage('Subscriptions updated');
                     $this->executeAction('showPerson');
                }
                else {
                    $form = TSubscriptionsForm::Build($formData);
                    $this->pageController->addMainContent($form);
                }
                break;


            case 'batchenroll' :
                $this->executeAction('batchSiteEnroll');
                break;
            case 'doimport' :
                $this->executeAction('doImport');
                break;
            case 'showAll' :
                $this->showAll();
                break;
        }
    }




    private function showAll() {
        TTracer::Trace('showAll not implemented'); /*
        $searchOption = TRequest::GetValue('searchOption','browse');
        if ($searchOption == 'browse') {
            $controller = TSearchFormController::Create($this->pageController);
            $Persons = TContactList::SearchAll();
            $controller->showSearchResult($contacts);
        }
        else
            TContactList::DownloadAll();
            */
    }

    public function showAddress() {
        TTracer::Trace('showAddress not implemented'); /*
        $mid = $this->getEntityId('mid');
        $pid = TMembership::GetInitialContactId($mid);
        if (empty($pid) || empty($mid))
            throw new Exception('No entity ids found for showAddress()');
        $this->pageController->setFormVariable('pid',$pid);
        $this->showContact($pid);
        */
    }

    private function editPerson($pid=0) {

        TTracer::Trace("editPerson($pid)");
        $controller = TPersonFormController::Create($this->pageController);
        $formData = $controller->getPersonData($pid);
        $this->executeAction('showPersonForm',$formData);

    }

    private function editAddress($aid=0) {
        TTracer::Trace("editAddress()");
        $controller = TAddressFormController::Create($this->pageController);
        $formData = $controller->getAddressData($aid);
        $this->executeAction('showAddressForm',$formData);

    }

    private function updateAddress() {
        TTracer::Trace('updateAddress()');
        $controller = TAddressFormController::Create($this->pageController);
        $formData = $controller->getRequestData();
        if ($formData->isValid) {
            if ($this->executeAction('updateAddress',$formData)) {

                $this->executeAction('showPerson',$formData);
            }
        }
        else {
            $this->executeAction('showAddressForm',$formData);
        }
    }

    private function updatePerson() {
        TTracer::Trace('update person');
        $controller = TPersonFormController::Create($this->pageController);
        $formData = $controller->getRequestData();

        if ($formData->isValid) {
            $isNew = empty($formData->pid);

            TTracer::Trace("Before update pid=".$formData->pid);
            TTracer::Assert($isNew,"new person.");
            if ($this->executeAction('updatePerson',$formData)) {
                if ($isNew) {
                    $email = $formData->person->getEmail();
                    if (!empty($email)) {
                        $this->showSubscriptionForm(($formData->pid));
                        return;
                    }
                }
                $this->executeAction('showPerson',$formData);
            }

        }
        else {
            $this->executeAction('showPersonForm',$formData);
        }

    }

    private function addPerson() {
        TTracer::Trace('addPerson()');
        $controller = TPersonFormController::Create($this->pageController);
        $formData = $controller->getPersonData();
        $this->executeAction('showPersonForm',$formData);
    }

    private function dropPerson() {
        TTracer::Trace('dropPerson not implemented');
        $pid = $this->request->get('pid',0);
        if (empty($pid)) {
            return 0;
        }
        $person = new TPerson();
        $person->select($pid);
        if ($person->getId() != $pid) {
            return 0;
        }
        $aid = $person->getAddressID();
        $userName = $person->getUsername();

        TPerson::Drop($pid);
        $person = null;
        TDrupalAccountManager::DropUser($userName);
        return $aid;
    }

    private function addReturnCrumbs($cmd) {
        TTracer::Trace('addReturnCrumbs');
        if ($cmd != 'search')
            $this->pageController->addBreadCrumbCommand('Search','search','Return to search form.');
        $return = $this->request->get('return');
        if ($return != $cmd && !empty($return)) {
            if ($return == 'admin' || $return == 'showPendingApprovals' ) {
                 $this->pageController->addBreadCrumbCommand('Approvals','showPendingApprovals','Return to membership approvals page.');
            }
            $this->pageController->setFormVariable('return',$return);
        }
    }

   private function showSubscriptionForm($pid) {
       TTracer::Trace('showSubscriptionForm');
        if (empty($pid)) {
            $this->pageController->addErrorMessage("No person id for subscriptions.");
            $this->pageController->redirectLocal("directory");
            return;
        }
        $controller = new TSubscriptionFormController($this->pageController);
        $formData = $controller->getFormData($pid);
        if ($formData === false) {
            $this->pageController->addErrorMessage('No FMA user account found.');
            $this->pageController->redirectLocal("directory");
            return;
        }
        $formData->returnForm = 'directory';
        $form = TSubscriptionsForm::Build($formData);
        $this->pageController->addMainContent($form);
   }

}
// TDirecotoryApplication


