<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/20/2015
 * Time: 7:03 AM
 */

class TFdsTeachers {
    /**
     * @var TCachedItem
     */
    private static $teacherList;

    public static function getTeacherList()
    {
        $result = isset(self::$teacherList) ? self::$teacherList->getValue() : null;
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
            self::$teacherList = new TCachedItem($result, 60 * 5);
        }
        return $result;

    }


}