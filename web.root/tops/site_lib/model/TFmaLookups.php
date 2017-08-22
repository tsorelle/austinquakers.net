<?php
/** Class: TFmaLookups ***************************************/
/// fma lookup tables
/**
*****************************************************************/
class TFmaLookups extends TLookupTableManager
{
    private static $instance;

    private $directoryCodes;
    private $memberStatuses;
    private $addressTypes;
    /**
     * @var TCachedItem
     */
    private $ageGroups;
    /**
     * @var TCachedItem
     */
    private $teachers;

    public function __construct() {

    }

    public function __toString() {
        return 'TFmaLookups';
    }

    private function getDirectoryCodeList() {
        if (!isset($this->directoryCodes))
            $this->directoryCodes = $this->getList('directorycodes', 'directoryId', 'description');
        return $this->directoryCodes;

    }

    private function getAddressTypeList() {
        if (!isset($this->addressTypes))
            $this->addressTypes = $this->getList(
            'addresstypes', 'addressTypeID', 'displayText', 'addressTypeDescription', true);
        return $this->addressTypes;
    }

    private function getMemberStatusList() {
        if (!isset($this->memberStatuses))
            $this->memberStatuses = $this->getList('memberstatus', 'memberStatusID', 'statusDescription', 'NULL',true);
        return $this->memberStatuses;
    }

    /**
     * @return LookupListItem[]|null
     */
    private function getFdsAgeGroupList() {
        $result = isset($this->ageGroups) ? $this->ageGroups->getValue() : null;
        if ($result === null) {
            $result = $this->getLookupList('fdsagegroups', 'id', 'name', 'description', true);
            $this->ageGroups = new TCachedItem($result,60 * 5);
        }
        return $result;
    }

    /**
     * @return LookupListItem[]|null
     */
    private function getTeacherList()
    {
        $result = isset($this->teachers) ? $this->teachers->getValue() : null;
        if ($result === null) {
            $result = array();
            $users = TDrupalUser::GetUsersInRole('teacher');
            foreach ($users as $user) {
                $item = new LookupListItem();
                $item->value = $user->id;
                $item->text = $user->displayName;
                $item->title = $user->fullName;
                array_push($result, $item);
            }
            $this->teachers = new TCachedItem($result, 60 * 5);
        }
        return $result;

    }



    public static function GetDirectoryCodes() {
        if (!isset(self::$instance))
            self::$instance = new TFmaLookups();
        return self::$instance->getDirectoryCodeList();
    }

    public static function GetMemberStatuses() {
        if (!isset(self::$instance))
            self::$instance = new TFmaLookups();
        return self::$instance->getMemberStatusList();
    }

    public static function GetAddressTypes() {
        if (!isset(self::$instance))
            self::$instance = new TFmaLookups();
        return self::$instance->getAddressTypeList();
    }

    /**
     * @return LookupListItem[]|null
     */
    public static function GetFdsAgeGroups() {
        if (!isset(self::$instance))
            self::$instance = new TFmaLookups();
        return self::$instance->getFdsAgeGroupList();
    }

    /**
     * @return LookupListItem[]|null
     */
    public static function GetTeachers() {
        if (!isset(self::$instance))
            self::$instance = new TFmaLookups();
        return self::$instance->getTeacherList();
    }

}
