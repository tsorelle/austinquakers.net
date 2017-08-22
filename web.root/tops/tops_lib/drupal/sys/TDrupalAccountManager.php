<?php

/** Class: TDrupalAccountManager ***************************************/
/// Manage Drupal User Account
/**
*****************************************************************/
class TDrupalAccountManager {

    public static $synchronizing = false;

    public static function saveUser($drupalUser, $fields) {
//TTracer::trace('saveUser');
//TTracer::Assert(!empty($drupalUser),'user assigned.');
//TTracer::ShowArray($drupalUser);
        TDrupalAccountManager::$synchronizing = true;
        $result = user_save($drupalUser, $fields);
        TDrupalAccountManager::$synchronizing = false;
        return $result;
    }

    public static function saveProfile($changes, $drupalUser, $category) {
        TTracer::Trace('saveProfile');
        TDrupalAccountManager::$synchronizing = true;
        profile_save_profile($changes, $drupalUser, $category);
        TDrupalAccountManager::$synchronizing = false;
        return user_load($drupalUser->uid);
    }

    /// Update first and last name in profile
    /**
    * Assumes profiles set up as
    * Category: 'Personal Information'
    * Fields:   'profile_firstname', profile_lastname'
    */
   public static function UpdateProfileName($drupalUser, $firstName, $lastName) {
        $changes = array();
        $changes['profile_firstname'] = $firstName;
        $changes['profile_lastname'] = $lastName;
        return TDrupalAccountManager::saveProfile($changes, $drupalUser, 'Personal Information');
    }

    public static function UpdateRoles($drupalUser, $roles) {
        TTracer::Trace('UpdateRoles');
        $fields = array();
        $fields['roles'] = $roles;
        return TDrupalAccountManager::saveUser($drupalUser, $fields);
    }


    public static function SetPassword($drupalUser, $password) {
        TTracer::Trace("SetPassword");
        if (empty($password))
            return;
        $fields = array();
        $fields['pass'] = $password;
        return TDrupalAccountManager::SaveUser($drupalUser,$fields);
    }

    /// Update or create drupal user

    public static function CreateUser($username, $password, $email, $roles) {
        TTracer::Trace('createUser');
        $fields = array();
        $fields['uid'] = 0;
        $fields['name'] = $username;
        $fields['pass'] = $password;
        $fields['mail'] = $email;
        $fields['roles'] = $roles;
        $fields['status'] = 1;
       // TTracer::ShowArray($fields);
        return TDrupalAccountManager::saveUser(null, $fields);
    }

    public static function FindUser($username) {
        if (empty($username)|| $username == 'none')
            return false;
        // TTracer::Trace('FindUser');
        $search = array();
        $search['name'] = $username;
        $result = user_load($search);
        if (empty($result) || empty($result->uid))
            return false;
        return $result;
    }

    public static function DropUser($username) {
        $account = self::FindUser($username);
        if (!$account) {
            return;
        }

        $uid = $account->uid;
        // $account = user_load(array('uid' => $uid));
        sess_destroy_uid($uid);
        // no email notification!
        // _user_mail_notify('status_deleted', $account);
        db_query('DELETE FROM {users} WHERE uid = %d', $uid);
        db_query('DELETE FROM {users_roles} WHERE uid = %d', $uid);
        db_query('DELETE FROM {authmap} WHERE uid = %d', $uid);
        $variables = array('%name' => $account->name, '%email' => '<'. $account->mail .'>');
        watchdog('user', 'Deleted user: %name %email.', $variables, WATCHDOG_NOTICE);
        // user_module_invoke('delete', $edit, $account);

    }

}

