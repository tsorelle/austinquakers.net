<?php
require_once("tops_lib/sys/TUser.php");

class TDrupalUser extends TUser {
    static $users = array();
    static $currentUser;

    private   $drupalUser;
    protected $drupalProfile;
    protected $uid;
    protected $pictureFile = '';


    public function __construct($user = null) {
        if (isset ($user))
            $this->loadDrupalUser($user);
    }

    protected function loadDrupalUser($user) {
        $this->drupalUser = $user;
        $this->uid = $user->uid;
        if (isset ($user->roles)) {
            if (!in_array('anonymous user', $user->roles)) {
                $this->authenticated = true;
                $this->roles = $user->roles;
            }
        }
        if ($this->authenticated) {  // || $offLine) {
            if (isset ($user->name))    {
                $this->userName = $user->name;
                TTracer::Trace("setting user name: $this->userName")  ;
            }
            if (isset ($user->mail))
                $this->email = $user->mail;

            if ($user->uid == 1)
                array_push($this->roles,$this->adminRole);

            $this->loadDrupalProfile($user);

        }
        else if ($_SERVER['SCRIPT_NAME'] == '/cron.php') {
            $this->authenticated = true;
            $this->userName = 'cron';
        }
        else
            $this->userName = 'guest';
    }

    public function getPictureFile() {
        return $this->pictureFile;
    }

    public function isAuthorized($value='')
    {
        // TTracer::Trace("TDrupalUser.isAuthorized");
        if ( $this->userName == "guest" )
          return false;


        if ($this->isMemberOf($this->adminRole)) {
           //    TTracer::Trace('Authorized: administrator');
          return true;
        }


        if ($this->isMemberOf($value))
            return true;

        if (isset($this->drupalUser))  {
            // TTracer::Trace("Checking access for '$value'")  ;
            $result = user_access($value, $this->drupalUser);
            TTracer::Assert($result,'Authorized');
            return $result;
        }

        return false;
    }  //  isAuthorized


    protected function loadDrupalProfile($user) {
        if (function_exists('profile_load_profile')) {
            TTracer::Trace('Loading profile');
            profile_load_profile($user);
            if (!empty($user->profile_firstname))
               $this->firstName = $user->profile_firstname;
            if (!empty($user->profile_lastname))
               $this->lastName = $user->profile_lastname;
            if (!empty($user->picture))
                $this->pictureFile = $user->picture;
        }
        else
            TTracer::Trace('Not loading profile');
    }

    public static function CreateMockDrupalUser($userName, $email, $roles = null) {
        $user = new stdClass();
        $user->name = $userName;
        $user->mail = $email;

        if (!isset($roles)) {
            $roles = array('anonymous user');
        }
        $user->roles = $roles;
        return $user;
    }

    public static function GetUser($uid) {
        if (isset(TDrupalUser::$users[$uid]))
            return TDrupalUser::$users[$uid];
        $user = user_load($uid);
        $result = new TDrupalUser();
        $result->loadDrupalUser($user);
        TDrupalUser::$users[$uid] = $result;
        return $result;
    }

    /**
     * @return bool|TDrupalUser
     */
    public static function GetCurrentUser() {
        global $user;
        $currentUser = TUser::GetCurrentUser();
        if ($currentUser === false) {
            $currentUser = TUser::Create();
            $currentUser->loadDrupalUser($user);
            TDrupalUser::$users[$user->uid] = $currentUser;
            TUser::SetCurrentUser($currentUser);
        }
        return $currentUser;
    }

    public static function SetCurrent($user=null) {
        // TTracer::Trace("<b>Loading user $user->uid </b>");
        $currentUser = TUser::Create();
        if (isset($user))
            $currentUser->loadDrupalUser($user);
        TUser::SetCurrentUser($currentUser);
    }

    public static function GetFullUserName($uid, $maxLength=0) {
        $user = TDrupalUser::GetUser($uid);
        $fullName = $user->getFullName();
        if ($maxLength > 0 && strlen($fullName) > $maxLength) {
            $fullName = $user->getUserShortName();
            if (strlen($fullName) > maxLength)
                $fullName = substr($fullName, 0, $maxLength - 3).'...';
        }

        return $fullName;
    }

    /**
     * Return users in a role.
     *
     * Note: Unfortunately there is no Drupal API call for this in Drupal
     * This direct database method is tested in D6
     * In D7 this method should work but a call to users_load_multiple might be preferable.
     * This method will not work in D8
     * see http://drupal.stackexchange.com/questions/11175/get-all-users-with-specific-roles-using-entityfieldquery
     *
     * @param $roleName
     * @return stdClass[]
     * @throws DatabaseException
     */
    public static function GetUsersInRole($roleName) {
        $sql =
            'SELECT u.uid, u.name FROM users u INNER JOIN users_roles ur ON u.uid = ur.uid  '.
            'INNER JOIN role r ON r.rid = ur.rid '.
            'WHERE r.name = ?';

        $result = array();
        $uid = 0;
        $name = '';
        $statement = TSqlStatement::ExecuteDrupalQuery($sql, 's', $roleName);  ;
        $statement->instance->bind_result($uid,$name);
        while ($statement->next()) {
            $person = TPerson::FindByUserName($name);
            if ($person) {
                $item = new stdClass();
                $item->drupalId = $uid;
                $item->id = $person->getId();
                $item->displayName = $person->getFirstName().' '.$person->getLastName();
                $item->fullName = $person->getFullName();
                if (array_key_exists($item->displayName,$result)) {
                    $duplicate = $result[$item->displayName];
                    unset($result[$item->displayName]);
                    $duplicate->displayName = $duplicate->fullName;
                    $result[$duplicate->displayName] = $duplicate;
                    $item->displayName = $item->fullName;
                }
                $result[$item->displayName] = $item;
            }
        }
        return $result;
    }
}

