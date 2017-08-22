<?php
/*****************************************************************
Class:  TDrupalData
Description:
*****************************************************************/
class TDrupalData
{
    public function __construct() {
    }

    public function __toString() {
        return 'TDrupalData';
    }

    public static function GetNodeIdByTitle($nodeType, $title) {
        $query =
            db_query("SELECT nid from {node} where type = '%s' and title = '%s'",
                $nodeType, $title);
        $nid = db_result($query);
        if (empty($nid))
            return 0;
        return $nid;
    }

    public static function GetNodeLinkByTitle($nodeType, $title) {
        $nid = TDrupalData::GetNodeIdByTitle($nodeType, $title);
        if (empty($nid))
            return $title;
        return '<a href="'."/?q=node/$nid".'">'.$title.'</a>';
    }

    public static function GetUidForUserName($name) {
        return db_result(db_query("SELECT uid FROM {users} WHERE name = '%s'", $name));

    }
}
// end TDrupalData



