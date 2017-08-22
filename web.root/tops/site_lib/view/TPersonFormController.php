<?php
/*****************************************************************
Class:  TPersonFormController
Description:
*****************************************************************/
class TPersonFormController
{
    private static $instance;
    private $pageController;
    private $request;

    public function __construct($pageController) {
        $this->pageController = $pageController;
        $this->request = TRequest::GetInstance();
    }

    public function getAddressId() {
        $aid = $this->request->get('aid',0);
/*        if ($aid == 0) {
            $aid = TSession::Get('addressId');
        }
*/
        $this->pageController->setFormVariable('aid',$aid);
        // if ($aid == 0)
        //    throw new Exception('No address ID found.');
        return $aid;
    }


    public function __toString() {
        return 'TPersonFormController';
    }

    public function validateUserName($formData) {
        $formData->accountStatus = 'none';
        $userName = $formData->person->getUserName();
        if ($userName == 'none')
            return true;

        if (empty($userName) && ($formData->person->getId() < 1)) {
            $this->pageController->addErrorMessage(
                'Please enter a web site Username. '.
                'It can be an existing account or check "New account" to create a new one. '.
                'If you do not want a web site account for this user, enter "none" as the username.'
                );
                return false;
        }

        $drupalUser = TDrupalAccountManager::FindUser($userName);
        if ($drupalUser !== false) {
            $firstName = isset($drupalUser->profile_firstname) ?
                $drupalUser->profile_firstname : '';
            $lastName = isset($drupalUser->profile_lastname) ?
                $drupalUser->profile_lastname : '';
            if  ($formData->newAccount) {
                $this->pageController->addErrorMessage(
                    sprintf('This user name "%s" is already in use by "%s %s." '.
                        'If you wish to associate this account with this contact, '.
                        'leave "New account" un-checked.  Otherwise use a different name.',
                        $userName, $firstName, $lastName));
                    return false;
            }
            $formData->accountStatus = 'existing';
        }
        else { // no drupal user found
            if  (!$formData->newAccount) {
                $this->pageController->addErrorMessage(
                    sprintf('There is no existing web site account for user name "%s." '.
                        'If you want to create a new account, check "New account. "'.
                        'If you do not want a web site account for this user, enter "none" as the username.',
                        $userName));
                    return false;
            }
            $formData->accountStatus = 'new';
        }

        $email = $formData->person->getEmail();
        if (empty($email)) {
                $this->pageController->addErrorMessage(
                    'An email address is required for a web site account. '.
                    'If you do not want a web site account for this user, enter "none" as the username.');
                return false;

        }
        return true;
    }

    public function getRequestData() {
        TTracer::Trace('getRequestData');
        //TRequest::PrintArgs();
        $formData = new stdClass();
        $pid =  $this->request->get('pid',0);
        $this->pageController->setFormVariable('pid',$pid);

        $formData->pid = $pid;
        $formData->aid = $this->getAddressId();

        $person =  $this->getPersonFromRequest();

        $formData->person = $person;
        $formData->newAccount = $this->request->isChecked('newAccount');
        $password = $this->request->get('identifier','');
        if (empty($password)) {
            $formData->newPassword = false;
        }
        else {
            $person->setPassword($password);
            $formData->newPassword = $password;
        }

        // todo: implement validation
        $errors = $person->validate();

        foreach($errors as $message)
            $this->pageController->addErrorMessage($message);

        $formData->isValid = $person->isValid();
        if ($pid == 0)
            if (!$this->validateUserName($formData))
                $formData->isValid = false;

        return $formData;
    }


    public function getPersonData($pid=0) {
        TTracer::Trace("loadPersonForm: $pid");
        $formData = new stdClass();
        $formData->person = new TPerson();
        $formData->pid = $pid;
        if (empty($pid)) {
            $formData->aid = $this->request->get('aid',0);
        }
        else {
            $formData->person->select($pid);
            $formData->aid = $formData->person->getAddressId();
        }
        $this->pageController->setFormVariable('aid',$formData->aid);
        $this->pageController->setFormVariable('pid',$pid);

        return $formData;
    }

    private function getPersonFromRequest() {
        TTracer::Trace('getPersonFromRequest');

        $person = new TPerson();
    	$person->setId($this->request->get('pid',0));
        $person->setAddressId($this->request->get('aid',0));
		$person->setFirstName($this->request->get('firstName',''));
		$person->setLastName($this->request->get('lastName',''));
		$person->setMiddleName($this->request->get('middleName',''));
		$person->setPhone($this->request->get('phone',''));
		$person->setWorkPhone($this->request->get('workPhone',''));
		$person->setEmail($this->request->get('email',''));
		$person->setMembershipStatus($this->request->get('membershipStatus',2));
        $person->setJunior($this->request->get('junior',''));
        $person->setSortKey($this->request->get('sortKey',''));
        $person->setDateOfBirth($this->request->get('dateOfBirth',''));
        $person->setDeceased($this->request->get('deceased',''));
        $person->setActive($this->request->get('active',1));
        $person->setDirectoryCode($this->request->get('directoryCode',3));
        $person->setOtherAffiliation($this->request->get('otherAffiliation',''));
		$person->setUsername($this->request->get('username',''));
        $notes = $this->request->get('notes',false);
        if ($notes !== false)
            $person->setNotes($notes);


        $errors = $person->validate();
        foreach($errors as $message)
            $this->pageController->addErrorMessage($message);
        return $person;

    }

    public static function Create($pageController) {
        if (!isset(self::$instance))
            self::$instance = new TPersonFormController($pageController);
        return self::$instance;
    }
}
// end TContactForm



