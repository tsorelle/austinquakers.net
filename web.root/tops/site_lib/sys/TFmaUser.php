<?php
/*tops_lib = TClassLib::GetTopsLib();
require_once ('tops_lib/drupal/sys/TDrupalUser.php');
*/
require_once ('tops_lib/drupal/sys/TDrupalUser.php');

class TFMAUser extends TDrupalUser {
    public function __construct($thisUser = null) {
        global $user;
        if (isset($thisUser)) {
            $this->loadDrupalUser($thisUser);
        }
        else
            $thisUser = $user;

       $statement = TSqlStatement::ExecuteQuery(
           'SELECT personID FROM persons WHERE username = ?','s',$thisUser->name);
        $statement->instance->bind_result($this->id);
        $statement->Next();
        // TTracer::Trace("fma user id for $thisUser->name: $this->id")  ;
    }

    public function getUserTitle() {
        if ($this->isAdmin())
            return 'System administrator';
        return '';
    }

    public static function CreateUser($user) {
        $fmauser = new TFMAUser($user);
        TUser::SetCurrentUser($fmauser);
        return $fmauser;
    }

    public static function CreateTestUser($userName, $email) {
        $roles = array();
        $numargs = func_num_args();
        if ($numargs > 2) {
            $roles = func_get_args();
            array_shift($roles);
            // past $username
            array_shift($roles);
            // past $email
        }

        $user = TDrupalUser::CreateMockDrupalUser($userName, $email, $roles);
        TUser::SetCurrentUser($user);
        return $user;
    }

}
// TQuipUser


