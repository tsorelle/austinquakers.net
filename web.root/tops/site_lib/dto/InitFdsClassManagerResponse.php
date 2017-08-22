<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/17/2015
 * Time: 5:48 AM
 */

class InitFdsClassManagerResponse {

    public $month;
    public $year;
    public $displayMonth;

    /**
     * @var LookupListItem[]
     */
    public $ageGroups;
    /**
     * @var LookupListItem[]
     */
    public $teachers;

    /**
     * @var FdsAssignmentDto[];
     */
    public $assignments;

    /**
     * $var WeekListItem[]
     */
    public $calendar;
}