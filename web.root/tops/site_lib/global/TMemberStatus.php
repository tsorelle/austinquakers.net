<?php
/** Class: TMemberStatus ***************************************/
/// lookup table for person.memberStatus
/**
*****************************************************************/
class TMemberStatus
{
    public function __construct() {
    }

    public function __toString() {
        return 'TMemberStatus';
    }

    public static function getStatusDescription($id) {
        $sql = 'SELECT statusDescription FROM memberstatus WHERE memberstatusID = ?';
        $result = TSqlStatement::ExecuteScaler($sql,'i',$id);
        if ($result === false)
            return '';
        return $result;
    }


}
// end TMemberStatus