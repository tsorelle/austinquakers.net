<?
/** Class: TDataValidator ***************************************/
///
/**
*****************************************************************/
class TDataValidator
{
    public function __construct() {
    }

    public static function IsValidEmail($email) {
        return ereg("^([[:alnum:]\_\.\-]+)(\@[[:alnum:]\.\-]+\.+[[:alpha:]]+)$", $email);
    }

    public function __toString() {
        return 'TDataValidator';
    }
}
// end TDataValidator