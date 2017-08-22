<?php
class TUser {
    private static $user;

    protected $id = 0;
    protected $firstName = '';
    protected $lastName  = '';
    protected $middleName  = '';
    protected $userName  = '';
    protected $fullName = '';
    protected $email  = '';
    protected $roles = array();
    protected $authenticated = false;
    protected $adminRole = 'site admin';

    function concatFullName($first, $last='', $middle='')
    {
        $this->fullName = $this->firstName;
        if (!empty($this->middleName))
            $this->fullName .= ' '.$this->middleName;
        if (!empty($this->lastName))
            $this->fullName .= ' '.$this->lastName;

    }  //  concatFullName

    public function getRoles()
    {
        return $this->roles;
    }  //  loadMembership

    function isMemberOf($groupName)
    {
        $roles = $this->getRoles();
        return in_array($groupName, $roles);
    }  //  memberOf

  function getId()
  {
    return $this->id;
  }  //  getId

  function isAuthenticated()
  {
    return $this->authenticated;
  }  //  isAuthenticated

  function isAuthorized($value='')
  {
    if ( $this->userName == "guest" ) {
      return false;
    }

    if ($this->isMemberOf($this->adminRole))
      return true;

    if ($this->isMemberOf($value))
        return true;


    return false;
  }  //  isAuthorized

  function getFirstName()
  {
    return $this->firstName;
  }  //  getFirstName

  function getLastName()
  {
    return $this->lastName;
  }  //  getLastName

  function getUserName()
  {
    return $this->userName;
  }  //  getUserName

  function getFullName($defaultToUsername=true)
  {
    if (!empty($this->fullName))
        return $this->fullName;

    $result = '';
    if (!empty($this->firstName)) {
        $result = $this->firstName;
    }
    if (!empty($this->middleName)) {
        if (!empty($result))
            $result .= ' ';
        $result .= $this->middleName;
    }
    if (!empty($this->lastName)) {
        if (!empty($result))
            $result .= ' ';
        $result .= $this->lastName;
    }

    if (empty($result) && $defaultToUsername)
        return $this->userName;

    return $result;
  }  //  getfullName

  function getUserShortName($defaultToUsername=true)
  {
      TTracer::Trace("Get short name, default=$defaultToUserName, username= ".$this->userName);
    $result = '';
    if (!empty($this->firstName)) {
        $result = $this->firstName; //  substr($this->firstName,0,1).'.';
    }
    if (!empty($this->middleName)) {
        if (!empty($result))
            $result .= ' ';
        $result .= substr($this->middleName,0,1).'.';
    }
    if (!empty($this->lastName)) {
        if (!empty($result))
            $result .= ' ';
        $result .= $this->lastName;
    }

    if (empty($result) && $defaultToUsername)
        $result = $this->userName;

    return $result;
  }  //  getfullName


  function getEmail()
  {
    return $this->email;
  }  //  getEmail

  public static function GetFullEmailAddress() {
      $user = self::GetCurrentUser();
      if ($user === false)
        return '';

      $name = $user->getUserShortName(false);
      $email = $user->getEmail();

      if (empty($name))
            return $email;
      return "$name<$email>";
  }


  function getGroups()
  {
    return $this->groups;
  }  //  getGroups

  function getAuthorizations()
  {
    return $this->authorizations;
  }  //  getAuthorizations

  public function isAdmin() {
      return ($this->isMemberOf($this->adminRole));
  }



  function dump()
  {
    echo "<br>User information: <br>";
    echo "firstName = $this->firstName<br>";
    echo "lastName  = $this->lastName<br>";
    echo "username  = $this->userName<br>";
    echo "email     = $this->email<br>";
    echo 'Roles     = ';
    print_r($this->getRoles());
    echo '<br>';
  }  //  dump

    public function __toString() {
        return $this->getFullName() . " ($this->userName)";
    }

    public static function SetCurrentUser($user) {
        TUser::$user = $user;
    }

    public static function GetCurrentUser() {
        if (isset(TUser::$user))
            return TUser::$user;
        return false;
    }

    public static function IsCurrentUser($username) {
        return TUser::$user->getUserName() == $username;
    }

    public static function IsAssigned() {
        return isset(self::$user);
    }

    public static function Create() {
        return TClassFactory::Create('user','TUser');
    }

    public static function IsSiteAdmin() {
        return TUser::$user->isAdmin();
    }

    public static function Authorized($auth) {
        return TUser::$user->isAuthorized($auth);
    }

    public static function MemberOf($group) {
        return TUser::$user->isMemberOf($group);
    }

    public static function GetUserPersonId() {
        return TUser::$user->getId();
    }


    public static function GetShortName($defaultToUsername = true) {
        return TUser::$user->getUserShortName($defaultToUsername);
    }

    public static function GetNetworkUserName() {
        return TUser::$user->getUserName();
    }

    public static function Authenticated() {
        return TUser::$user->isAuthenticated();
    }


}

