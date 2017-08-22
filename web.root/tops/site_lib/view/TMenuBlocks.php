<?php
/** Class: TMenuBlocks ***************************************/
///
/**
*****************************************************************/
class TMenuBlocks
{
    public function __construct() {
    }

    public function __toString() {
        return 'TMenuBlocks';
    }

    public static function GetModeratorMenu() {
        $result = TCollapsible::Create('committee_links_block','Tasks...');
        $result->add( menu_tree('menu-moderator'));
        return $result;
    }
}
// end TMenuBlocks