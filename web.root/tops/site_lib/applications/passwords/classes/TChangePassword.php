<?
/** Class: TChangePassword ***************************************/
/// update password on both sites
/************************************************************/
class TChangePassword extends TPageAction
{
    protected function run() {
        global $user;
        TTracer::Trace('TChangePassword::run');
        $password = TRequest::GetValue('pwd','');
        TTracer::Trace("pwd = $password for user: ".$user->name);
        if (strlen($password) < 5) {
            $this->addErrorMessage('Password is too short.');
            return false;
        }

       TDrupalAccountManager::SetPassword($user, $password);
       TOldFmaSite::UpdateLogin($user->name, $password);


        $this->pageController->addInfoMessage('Your password has been changed. ');
        $this->pageController->redirectLocal("/userinfo");
        return true;

    }

    public function __toString() {
        return 'TChangePassword';
    }
}
