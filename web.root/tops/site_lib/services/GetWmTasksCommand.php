<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/14/14
 * Time: 8:44 AM
 */


class GetMonthlyTasksRequest {
    public $month;
    public $year;
}


class GetWmTasksCommand extends  TServiceCommand {

    protected function run()
    {
        $request = $this->GetRequest();
        if (empty($request)) {
            $this->SetReturnValue(TWmTask::GetMonthlyTasks());
        }
        $response = TWmTask::GetMonthlyTasks($request->month, $request->year);
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