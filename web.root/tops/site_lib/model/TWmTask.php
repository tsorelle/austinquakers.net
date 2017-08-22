<?php
require_once("tops_lib/model/TEntityObject.php");
class WmTask {
    public $wmtaskId;
    public $taskDate;
    public $greeter;
    public $closer;
}

class WmTaskView extends  WmTask {
    public $dayOfMonth;
}

class GetMonthlyTasksResponse {
    public $month;
    public $year;
    public $displayMonth;
    public $tasks; // array of WmTaskView
}


class TWmTask extends TEntityObject {
    public function  __construct()
    {
        $this->tableName = 'wmtasks';
        $this->idFieldName = 'wmtaskId';
        $this->addField('wmtaskId',INT_FIELD);
        $this->addField('taskDate',DATE_FIELD);
        $this->addField('greeter',STRING_FIELD);
        $this->addField('closer',STRING_FIELD);
    }  //  TWmTask

    function getTaskDate() {
        return $this->get('taskDate');
    }
    function setTaskDate($value) {
        $this->setFieldValue('taskDate',$value);
    }

    function getGreeter() {
        return $this->get('greeter');
    }
    function setGreeter($value) {
        $this->setFieldValue('greeter',$value);
    }

    function getCloser() {
        return $this->get('closer');
    }
    function setCloser($value) {
        $this->setFieldValue('closer',$value);
    }

    public static function CountMissingAssignments($tasks) {
        $assignmentsOpen = 0;
        foreach($tasks as $task ) {
            if (empty($task->greeter)) {
                $assignmentsOpen++;
            }
            if (empty($task->closer)) {
                $assignmentsOpen++;
            }
        }
        return $assignmentsOpen;
    }

    public static function GetMonthlyTasks($month=0,$year=0) {
        $result = new  GetMonthlyTasksResponse();
        $result->tasks = array();


        $currentMonth = date('m');
        $currentYear = date('Y');

        if ($year == 0)
            $year = $currentYear;
        else if ($year < 1000)
            $year += 2000;

        if ($month == 0)
            $month = $currentMonth;
        else {
            if ($month > 12) {
                $month = 1;
                $year++;
            }
            else if ($month < 1) {
                $month = 12;
                $year--;
            }
        }

        $result->month = $month;
        $result->year = $year;

        // start with first sunday of the month
        $sunday = mktime(0,0,0,$month,1,$year);


        $result->displayMonth = date("F",$sunday);
        if (date('w',$sunday)!= 0)
            $sunday = strtotime("next Sunday",$sunday);
        for ($i=0; $i<5; $i++ ) {
            if ($month == date('m',$sunday)) {
                $task = new WmTaskView();
                $task->dayOfMonth = date('M-d',$sunday);

                $task->taskDate = date('Y-m-d', $sunday);
                $taskEntity = new TWmTask();
                $taskEntity->search("taskDate = '$task->taskDate' ");
                $task->wmtaskId = $taskEntity->getId();
                if ($task->wmtaskId > 0) {
                    $task->greeter = $taskEntity->getGreeter();
                    if ($task->greeter == null)
                        $task->greeter = '';
                    $task->closer = $taskEntity->getCloser();
                    if ($task->closer == null)
                        $task->closer = '';
                }
                else {
                    $task->wmtaskId = 0;
                    $task->greeter = '';
                    $task->closer = '';
                }

                $result->tasks[$i] = $task;

                $sunday = strtotime("next Sunday",$sunday);
            }
        }

        return $result;
    }


    public static function UpdateTasks(array $tasks) {

        foreach ($tasks as $task) {
            $taskEntity = new TWmTask();
            if ($task->wmtaskId > 0) {
                $taskEntity->select($task->wmtaskId);
                $taskEntity->setCloser($task->closer);
                $taskEntity->setGreeter($task->greeter);
                $taskEntity->update();
            }
            else {
                $taskEntity->setCloser($task->closer);
                $taskEntity->setGreeter($task->greeter);
                $taskEntity->setTaskDate($task->taskDate);
                $taskEntity->add();
            }
        }
    }


} // end class

