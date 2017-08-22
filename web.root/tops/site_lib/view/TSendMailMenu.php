<?
/** Class: TSendMailMenu ***************************************/
/// display menu for e-mailings
/**
*****************************************************************/
class TSendMailMenu
{
    public function __construct() {
    }

    public function __toString() {
        return 'TSendMailMenu';
    }

    private static function getListLink($listName, $listId) {
        return sprintf('<li><a href="/?q=mailer&cmd=showForm&lid=%s">%s</a></li>',$listId, $listName) ;
    }

    public static function Build($elists) {
        $result = TDiv::Create('mailingsMenu');
        $result->add('<h2>Email Distribution Menu</h2>');
        if (TUser::Authorized('send fma mail')) {
            $menu = new TBulletList();
            foreach ($elists as $list) {
                if ($list->code != 'fmanotes' ) {
                    $menu->addText(self::getListLink($list->name,$list->id));
                }
                else {
                    $menu->addText("<li><a href='/publishnewsletter'>Friendly Notes</a></li>");
                }
            }


            /*
            $menu->addText(self::getListLink("Meeting Announcements",2));
            $menu->addText(self::getListLink("Peace and Social Concerns",3));
            $menu->addText(self::getListLink("Weekly Bulletin",4));
            */

            // $menu->addText('<li><a href="/?q=mailer&cmd=fnmenu">Friendly Notes</a></li>');
                    //self::getListLink("Friendly Notes",1));
            // $menu->addText('<li><a href="/?q=mailer&cmd=test">Test</a></li>');
            $result->add($menu);

        }
        else {
            $result->add('<p>User not authorized to send e-mail.<p>');
        }
        return $result;
    }
}
// end TSendMailMenu