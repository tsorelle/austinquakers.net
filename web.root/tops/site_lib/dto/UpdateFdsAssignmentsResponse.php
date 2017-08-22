<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/21/2015
 * Time: 9:29 AM
 */

class UpdateFdsAssignmentsResponse {
    public $month;
    public $year;
    public $displayMonth;

    /**
     * @var FdsAssignmentDto[];
     */
    public $assignments;

    /**
     * $var WeekListItem[]
     */
    public $calendar;
}