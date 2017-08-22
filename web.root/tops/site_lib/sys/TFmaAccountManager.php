<?php
/** Class: TQuipAccountManager ***************************************/
/// Manager drupal user accounts for Quip
/*******************************************************************/
class TFmaAccountManager
{
    private static $synchronizingDrupal = false;

    public static function CreateAccount($username, $firstName,
        $lastName, $password, $email, $roles=null) {
        $result = TDrupalAccountManager::CreateUser(
            $username, $password, $email, $roles);
        return TDrupalAccountManager::UpdateProfileName($result, $firstName, $lastName);
    }


    public static function SynchronizeFma($drupalUser, $password='') {
        if (TDrupalAccountManager::$synchronizing)
            return;
        $firstName = isset($drupalUser->profile_firstname) ?
            $drupalUser->profile_firstname :
            '';
        $lastName = isset($drupalUser->profile_lastname) ?
            $drupalUser->profile_lastname :
            '';
        $email = isset($drupalUser->mail) ?
            $drupalUser->mail :
            '';

        if (empty($drupalUser->profile_firstname) && empty($drupalUser->profile_lastname))
            return;

        $sql = 'update persons set firstname = ?, lastname = ?, email = ? where username = ?';
        TSqlStatement::ExecuteNonQuery($sql,'ssss',$firstName,$lastName,$email,$drupalUser->name);

    }

    public static function SynchronizeProfile($person, $drupalUser = null) {
        if (empty($drupalUser)) {
            $drupalUser = TDrupalAccountManager::FindUser($person->getUsername());
            if ($drupalUser === false)
                return false;
        }
        TTracer::Trace('synchronizing');
        $roles = array();
        $fields = array();
        $fields['mail'] = $person->getEmail();
        $fields['roles'] = $roles;
        $result = TDrupalAccountManager::saveUser($drupalUser, $fields);
        return TDrupalAccountManager::UpdateProfileName($result,
            $person->getFirstName(), $person->getLastName());
    }

    public static function CreateNewSiteAccount($person, $password = '', $roles='') {
        TTracer::Trace('createNewSiteAccount');
        $username = $person->getUsername();
        $email = $person->getEmail();
        $firstName = $person->getFirstName();
        $lastName = $person->getLastName();
        $fullname = sprintf('%s %s',$firstName,$lastName);
        if (empty($password))
            $password = user_password(); // drupal api
        if (empty($roles))
            $roles = array();
        $drupalUser = self::CreateAccount(
                $username,
                $firstName,
                $lastName,
                $password,
                $email,
                $roles);

        if (empty($password)) {
            $loginLink = user_pass_reset_url($drupalUser); // drupal api
            $template =   TDrupalSnippet::Get('web account welcome');
            $template->setValue('username',$username);
            $template->setValue('loginLink', $loginLink);
            $address =  TEMailMessage::FormatAddress($email, $fullname);
            TTracer::Trace("Message: $email, $fullname, ".htmlentities($address));

            TPostOffice::SendMessageFromUs($address,'Welcome to the AustinQuakers.net web site',$template);

        }
        else {
            $template =   TDrupalSnippet::Get('web account welcome 1');
            $template->setValue('username',$username);
            $address =  TEMailMessage::FormatAddress($email, $fullname);
            TTracer::Trace("Message 1: $email, $fullname, ".htmlentities($address));
            TPostOffice::SendMessageFromUs($address,'Welcome to the AustinQuakers.net web site',$template);

            $template =   TDrupalSnippet::Get('web account welcome 2');
            $template->setValue('password',$password);
            $address =  TEMailMessage::FormatAddress($email, $fullname);
            TTracer::Trace("Message 3: $email, $fullname, ".htmlentities($address));
            TPostOffice::SendMessageFromUs($address,'More information about AustinQuakers.net',$template);

        }

        return $drupalUser;

    }

}
// end TQuipAccountManager



