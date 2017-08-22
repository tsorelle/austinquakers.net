<?
/** Class: TFNotesMenu ***************************************/
/// menu for friendly notes admin
/**
*****************************************************************/
class TFNotesMenu
{
    public function __construct() {
    }

    public function __toString() {
        return 'TFNotesMenu';
    }

    public static function Build() {
        $result = TDiv::Create('fnotesMenu');
        $result->add('<h2>Email Distribution Menu</h2>');

        if (TUser::Authorized('send fma mail')) {
            $menu = new THtmlMenu();
            $menu->addCommandItem('mailer','List Subscribers','fnsubscribers');
            $menu->addCommandItem('mailer','Send Friendly Notes','fnshowform');
            $menu->addCommandItem('mailer','Address labels','fnaddresses');
            /*
            $menu->addCommandItem('mailer','','fn');
            $menu->addCommandItem('mailer','','fn');
            $menu->addCommandItem('mailer','','fn');
            $menu->addCommandItem('mailer','','fn');
            */
            $result->add($menu);

        }
        else {
            $result->add('<p>User not authorized to send e-mail.<p>');
        }
        return $result;
    }
}
// end TFNotesMenu