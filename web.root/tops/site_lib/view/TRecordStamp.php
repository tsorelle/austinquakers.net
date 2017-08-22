<?php
/** Class: TRecordStamp ***************************************/
/// formants update message
/**
*****************************************************************/
class TRecordStamp
{
    public function __construct() {
    }

    public function __toString() {
        return 'TRecordStamp';
    }

    private static function getUserDisplayName($username) {
        if (!empty($username)) {
            $userFullName = TPerson::GetFullNameForUser($username);
            if (!empty($userFullName))
                return $userFullName;
        }
        return $username;
    }

    public static function get($entity) {

        $stamps = $entity->getModificationStamps();
 //   TTracer::ShowArray($stamps);
        $result = '';
        $hasCreateDate = isset($stamps->createdDate);
        $hasCreateUser = isset($stamps->createdUser);
        $hasCreateMessage = ($hasCreateDate || $hasCreateUser );
        $hasModDate = isset($stamps->modifiedDate);
        $hasModUser = isset($stamps->modifiedUser);
        if ($hasCreateMessage ) {
            $result = 'Created ';
            if ($hasCreateDate)
                $result .= 'on '.$stamps->createdDate;
            if ($hasCreateUser) {
                $result .= ' by '.self::getUserDisplayName($stamps->createdUser);
            }
            $result .= '. ' ;
        }
        if ($hasModDate || $hasModUser) {
            if ($hasCreateMessage)
                $result .= '<br/>';
            $result .= 'Updated ';
            if ($hasModDate)
                $result .= 'on '.$stamps->modifiedDate;
            if ($hasModUser) {
                $result .= ' by '.self::getUserDisplayName($stamps->modifiedUser);
            }
            $result .= '. ' ;
        }


         return $result;
    }

}
// end TRecordStamp