<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/15/2015
 * Time: 4:56 PM
 */

class UpdateFdsClassesCommand  extends TServiceCommand
{

    /**
     * @var UpdateFdsAssignmentsResponse
     */
    private $response;

    protected function run()
    {
        /**
         * @var UpdateFdsAssignmentsRequest
         */
        $request = $this->GetRequest();

        if (empty($request)) {
            $this->AddErrorMessage('no request received');
            return;
        }
        $this->performUpdates($request);
        $this->getAssignments($request);
    }

    private function performUpdates($request) {
        if (!empty($request->updates)) {
            foreach ($request->updates as $assignment) {
                TFdsAssignment::UpdateAssignment($assignment);
            }
        }
    }

    private function getAssignments($request) {
        $response = new UpdateFdsAssignmentsResponse();
        // $response->ageGroups = TFmaLookups::GetFdsAgeGroups();
        // $response->teachers = TFmaLookups::GetTeachers();

        $calendar = new TWeeksCalendar($request->month,$request->year);
        $response->calendar = $calendar->getSelectList();
        $response->month = $calendar->getMonth();
        $response->year = $calendar->getYear();
        $response->displayMonth = $calendar->getMonthAndYear();
        $response->assignments = TFdsAssignment::GetAssignmentList($calendar->getStartDate(),$calendar->getEndDate());

        $this->SetReturnValue($response);

    }

}