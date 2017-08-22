<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/14/14
 * Time: 8:47 AM
 */

class UpdateMonthlyTasksRequest  {
    public  $month;
    public  $year;
    public  $tasks;
    public  $increment;
}


class UpdateWmTasksCommand extends TServiceCommand {

    protected function run()
    {
        $request = $this->GetRequest();
        if (!empty($request)) {
            TWmTask::UpdateTasks($request->tasks);
        }

        $month = $request->month;
        $year = $request->year;
        $month += $request->increment;
        if ($month > 12) {
            $month = 1;
            $year++;
        }
        else if ($month < 1) {
            $month = 12;
            $year--;
        }

        $response = TWmTask::GetMonthlyTasks($month, $year);
        $assignmentsOpen = TWmTask::CountMissingAssignments($response->tasks);
        if ($assignmentsOpen == 0) {
            $this->AddInfoMessage("No assignments needed.");
        }
        else {
            $this->AddWarningMessage("$assignmentsOpen assignments needed.");
        }

        $this->SetReturnValue($response);

    }
}