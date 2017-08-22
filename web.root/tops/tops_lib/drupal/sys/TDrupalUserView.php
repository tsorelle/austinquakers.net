<?php
/*****************************************************************
Class:  TDrupalUserView
Description:
*****************************************************************/
class TDrupalUserView
{
    private static $instances = array();
    private $user;
    private $uid;
    private $pictures = array();

    public function __construct($uid = -1) {
        $this->user = TDrupalUser::GetUser($uid);
        $this->uid = $uid;
    }

    public function __toString() {
        if (isset($this->user))
            return sprintf('View for %s',$this->user->getFullName());
        else
            return 'user view';

    }

    public static function Create($object = null) {
//        TTracer::On();
//        TTracer::Trace("user view create");

        if (empty($object))
            return TDrupalAnonymousUserView::GetInstance();
        if (is_numeric($object))
            $uid = $object;
        else  {
            if (!isset($object->uid))
                return TDrupalAnonymousUserView::GetInstance();
            $uid = $object->uid;
        }
        if (isset(TDrupalUserView::$instances[$uid]))
            return TDrupalUserView::$instances[$uid];
        $result = new TDrupalUserView($uid);
        TDrupalUserView::$instances[$uid] = $result;
        return $result;
    }

    private function getProfileLink($text) {
//        TTracer::On();
//        TTracer::Trace('getProfileLink');
//        TTracer::Assert(user_access('access user profiles'),'can access provile');
//        TTracer::Assert(!empty($this->uid), "id = $this->uid", 'no user id');

        if (user_access('access user profiles') && !empty($this->uid)) {
            $attributes = array('title' => t('View user profile.'));
            $attributeArgs = array('attributes' => $attributes, 'html' => TRUE);
            $text = l($text, "user/$this->uid", $attributeArgs);
//            TTracer::Trace('made link');
        }
        return $text;
    }

    public function GetFullName() {
        return $this->user->getFullName();
    }

    private function renderPicture($align) {
        $pictureFile = $this->user->getPictureFile();

        if (!empty($pictureFile) && file_exists($pictureFile)) {
          $picture = file_create_url($pictureFile);
        }
        else if (variable_get('user_picture_default', '')) {
          $picture = variable_get('user_picture_default', '');
        }
        if (!isset($picture))
            return '';

        $userFullName = $this->user->getFullName();
        if (empty($userFullName))
            $userFullName = 'anonymous';

        $alt = t("@user's picture", array('@user' => $userFullName));
        $attributes = array('class' => 'userPicture');
        if (!empty($align))
            $attributes['align'] = $align;
        $result = theme('image', $picture, $alt, $alt, $attributes, FALSE);

        return $this->getProfileLink($result);
   }

    public function getSubmitMessage(
        $timestamp, $messageFormat, $dateFormat = 'l F j, Y') {

        $userFullName = $this->user->getFullName();
        if (empty($userFullName))
            $userFullName = 'an anonymous contributor';
        else if ($userFullName == 'admin') {
            $userFullName = '<a href="/?q=blog/1">the Web Clerk</a>';
        }
        else
            $userFullName = $this->getProfileLink($userFullName);
        $submittedDate = format_date($timestamp, 'custom', $dateFormat);
        return sprintf($messageFormat, $userFullName, $submittedDate);
    }

    public function getPicture($align='') {
        if (isset($this->pictures[$align]))
            return $this->pictures[$align];
        $result = $this->renderPicture($align);
        $this->pictures[$align] = $result;
        return $result;
    }

}
// end TDrupalUserView



