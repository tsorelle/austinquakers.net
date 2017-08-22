<?php
/** Class: TDrupalPage ***************************************/
///
/**
*****************************************************************/
class TDrupalPage
{
    public function __construct() {
    }

    public function __toString() {
        return 'TDrupalPage';
    }

    public static function GetPageNodeType() {
        $node = TDrupalPage::GetPageNode();
        if (!empty($node))     {
            return $node->type;
        }
        return null;

    }

    public static  function GetPageNodeId() {
        $path =  drupal_get_normal_path( $_GET['q']);
        if (!empty($path)) {
            $parts = split('/', strtolower( $path));
            if ($parts[0] == "node" && is_numeric($parts[1])) {

                return $parts[1];
            }
        }
        return 0;
    }

    public static function GetPageNode() {
        $nodeId = self::GetPageNodeId();
        if ($nodeId > 0)
            return node_load($nodeId);

        return null;
    }
}
// end TDrupalPage