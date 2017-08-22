<?php
/** Class: TApproveMembership ***************************************/
/// post membership approval
/************************************************************/
class TBatchSiteEnroll extends TPageAction
{
    protected function run() {
        $count = 0;
        $sql = "select personId from persons where active=1 and password is not null and password <> '' and email is not null and email <> ''";
        $statement = TSqlStatement::ExecuteQuery($sql,'i');
        $statement->instance->bind_result($pid);
        while($statement->next()) {
            $count++;
            $this->processMember($pid);
        }



        $this->pageController->addInfoMessage(sprintf('%d processed',$count));
    }

    private function getFmaRoles($pid) {
        $result = array();
        $sql = 'SELECT authorized from authorizations where personId = ?';
        $statement = TSqlStatement::ExecuteQuery($sql, 'i', $pid);
        $statement->instance->bind_result($role);
        while ($statement->next()) {
            array_push($result,$role);
        }
        return $result;
    }

    private function processMember($pid) {
        $contact = TPerson::GetPerson($pid);
        if (!$contact->getId()) {
            $this->pageController->addErrorMessage("Can't find contact $pid");
            return false;
        }

        $email =  $contact->getEmail();
        if (empty($email)) {
            $this->pageController->addErrorMessage("No email address for contact $pid");
            return;
        }
        $firstName = $contact->getFirstName();
        $lastName = $contact->getLastName();
        $username = $contact->getUsername();
        $password = $contact->getPassword();
        $memberstatus = $contact->getMembershipStatus();
        $status =   $contact->getMemberStatusText();
        $fmaRoles = $this->getFmaRoles($pid);

        if (empty($firstName) && empty($lastName)) {
            return false;
        }


        $roles = array();

        if (in_array('developer',$fmaRoles) ||
             in_array('administrator',$fmaRoles) ||
             in_array('fnadmin',$fmaRoles) ||
             in_array('public announcements',$fmaRoles) )

             {
                 array_push($roles,'siteadmin');
             }
             else if (
             in_array('listadmin',$fmaRoles) ||
             in_array('emailadmin',$fmaRoles) )
             {
                array_push($roles,'listadmin');
             }

        $drupalUser = TDrupalAccountManager::FindUser($username);
        if ($drupalUser == false) {
            $drupalUser = TFmaAccountManager::CreateAccount($username, $firstName,
                    $lastName, $password, $email, $roles);
                 $this->pageController->addInfoMessage("Processed new account $username: $firstName $lastName: $status ($memberstatus)");
        }

        return true;
    }
}
