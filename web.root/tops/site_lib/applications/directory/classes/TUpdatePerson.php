<?php
class TUpdatePerson extends TPageAction
{
    protected function run() {
        TTracer::Trace('TUpdatePerson.run');
		$formController = TPersonFormController::Create($this->pageController);
        if ($this->argCount == 0) {
            $formData = $formController->getRequestData();
        }
        else {
            $formData = $this->getArg();
        }
        $person = $formData->person;
        $pid = $person->getId();
        $username = $person->getUsername();
        if ($username == 'none') {
            $webAccount = false;
            $person->userName = '';
        }
        else
            $webAccount = true;

        if ($pid > 0) {
            $person->update();
            $dbAction = 'updated';
        }
        else {

            if (!empty($username) && $username != 'none') {
                $duplicates = TSqlStatement::ExecuteScaler('select count(*) from persons where username = ?','s',$username);
                if ($duplicates > 0) {
                    $this->pageController->addErrorMessage("Another person has the username '$username.'");
                    return false;
                }
            }
            $person->add();
            $formData->pid = $person->getId();
            $dbAction = 'added';
        }

        if ($webAccount) {
            $newPassword = empty($formData->newPassword) ?
                '' : $formData->newPassword;

            if ($formData->accountStatus == 'existing') {
                $drupalUser = TFmaAccountManager::SynchronizeProfile($person);
                TDrupalAccountManager::SetPassword($drupalUser, $newPassword);
            }
            else if ($formData->accountStatus == 'new') {
                TFmaAccountManager::CreateNewSiteAccount($person, $newPassword);
            }

            TOldFmaSite::UpdateLogin($username, $newPassword);
        }


        $this->pageController->addInfoMessage("Person $dbAction.");

        return true;
    }

}
// TUpdatePerson



?>