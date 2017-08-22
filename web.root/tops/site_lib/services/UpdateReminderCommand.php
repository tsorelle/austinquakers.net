<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/7/2015
 * Time: 5:31 AM
 */
class UpdateReminderCommand extends TServiceCommand
{

    protected function run()
    {
        $request = $this->GetRequest();
        $eventId = $request->eventId;
        if (!$eventId) {
            $this->AddErrorMessage('Cannot update: No event identifier received.  Ask the web clerk for help.');
            return;
        }
        if (!is_numeric($eventId)) {
            throw new Exception("Invalid eventId '$eventId'");
        }
        if (!$request->action) {
            $this->AddErrorMessage("Cannot update: Action parameter not found. Ask the web clerk for help.");
        }
        $personId = $request->personId;
        if ($personId) {
            if (!is_numeric($personId)) {
                throw new Exception("Invalid personId '$personId'");
            }
        }
        else {
            $user = TDrupalUser::GetCurrentUser();
            if (!$user->isAuthenticated()) {
                $this->AddErrorMessage("Cannot update. Please sign-in.");
                return;
            }
            $personId = $user->getId();
            if (!$personId) {
                 $this->AddErrorMessage('Please log in as a registered user.');
                return;
            }
        }

        if ($request->action == 'cancel') {
            $result = 'cancelled';
            TReminder::removeReminder($personId,$eventId);
        }
        else {
            $result = 'set';
            TReminder::addReminder($personId,$eventId);
        }

        $this->AddInfoMessage("Your reminder has been $result.");
        $persons = TEvent::getEventPersons($eventId);
        $this->SetReturnValue($persons);
    }
}