<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/12/2015
 * Time: 2:07 PM
 */
class GetRoomCalendarCommand extends TServiceCommand
{

    protected function run()
    {
        $id = $this->GetRequest();
        if (!$id) {
            $this->AddErrorMessage('No room or resource id received.');
        }
        else {
            $calendar = TEvent::getUpcomingRoomAndResourceUsage($id);
            $this->SetReturnValue($calendar);
        }
    }
}