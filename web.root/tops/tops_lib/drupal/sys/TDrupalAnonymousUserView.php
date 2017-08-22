<?php
/*****************************************************************
Class:  TDrupalAnonymousUserView
Description:
*****************************************************************/
class TDrupalAnonymousUserView
{
    private static $instance;

    public function __construct() {
    }

    public function __toString() {
        return 'anonymous user';
    }

    public function getPicture($align='') {
        return '';
    }

    public function getSubmitMessage($timestamp, $messageFormat, $dateFormat = 'l F j, Y') {
        $submittedDate = format_date($timestamp, 'custom', $dateFormat);
        return sprintf($messageFormat,'anonymous user', $submittedDate);
    }

    public static function GetInstance() {
//           TTracer::Trace("get anonymous instance");

        if (!isset(TDrupalAnonymousUserView::$instance))
            TDrupalAnonymousUserView::$instance = new TDrupalAnonymousUserView();
        return TDrupalAnonymousUserView::$instance;
    }

}
// end TDrupalAnonymousUserView



