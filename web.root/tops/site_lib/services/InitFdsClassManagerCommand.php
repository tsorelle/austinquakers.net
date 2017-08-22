<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/15/2015
 * Time: 4:55 PM
 */

class InitFdsClassManagerCommand extends TServiceCommand
{
    protected function run()
    {
        $response = new InitFdsClassManagerResponse();
        $response->ageGroups = TFmaLookups::GetFdsAgeGroups();
        $response->teachers = TFmaLookups::GetTeachers();

        $calendar = new TWeeksCalendar();
        $response->calendar = $calendar->getSelectList();
        $response->month = $calendar->getMonth();
        $response->year = $calendar->getYear();
        $response->displayMonth = $calendar->getMonthAndYear();
        $response->assignments = TFdsAssignment::GetAssignmentList($calendar->getStartDate(),$calendar->getEndDate());

        // $this->AddInfoMessage("Found " . $person->Name);
        $this->SetReturnValue($response);
    }
}