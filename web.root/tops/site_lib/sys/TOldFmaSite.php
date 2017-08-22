<?php
/** Class: TOldFmaSite ***************************************/
/// backward compatibility class
/**
*****************************************************************/
class TOldFmaSite
{
    public function __construct() {
    }

    public function __toString() {
        return 'TOldFmaSite';
    }

    public static function UpdateLogin($username, $password)
    {
        TTracer::Trace("Updating fma login for $username/$password");
        if (empty($password))
            return;
        $sql = 'update persons set password = ? where username = ?';
        TSqlStatement::ExecuteNonQuery($sql,'ss',$password,$username);


        $pwdPath = '/home/austinqu/.htpasswds/public_html/friends/passwd';

        if ( isset($_ENV["OS"] ))
            $os = $_ENV["OS"];
        else
            $os = PHP_OS;

        $isWindows = (strtoupper(substr($os,0,3)=='WIN'));

        if ($isWindows) {
          $serverDir = getenv("apache_server_root");
          if ($serverDir) {
              $serverDrive = substr($serverDir,0,2);
          }
          else
              $serverDrive = "c:";
          $this->pwdPath = $serverDrive.$this->pwdPath;
        }

        $sql = "select username, password from persons ".
			 "where username is not null and password is not null;";
        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($username,$password);
        $fp=fopen( $pwdPath,"w");
        flock($fp,2);
        while ($statement->next()) {
            if (!$isWindows)
                $password = crypt($password);
            $content=$username.":$password\n";
            fputs($fp,$content);
        }
        flock($fp,3);
        fclose($fp);
    }
}
// end TOldFmaSite