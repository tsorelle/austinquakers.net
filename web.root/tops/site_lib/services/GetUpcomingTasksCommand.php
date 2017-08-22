<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/10/2015
 * Time: 12:04 PM
 *
 * Used by meetingServicesViewModel
 */
class GetUpcomingTasksCommand extends TServiceCommand
{

    protected function run()
    {
        $tasks = TEvent::getTaskSchedule('%W %M %e,%Y');
        $this->SetReturnValue($tasks);
    }
}