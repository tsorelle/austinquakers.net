<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/7/2015
 * Time: 5:30 AM
 */
class GetEventInfoCommand extends TServiceCommand
{

    protected function run()
    {
        $request = $this->GetRequest();
        $eventId = $request->eventId;
        if (!$eventId) {
            $this->AddErrorMessage('No event identifier received.');
            return;
        }
        $eventInfo = TEvent::getEventAndPersons($request->eventId);
        $user = TDrupalUser::GetCurrentUser();
        $eventInfo->canEdit = $user->isAuthorized('edit any event content') ? 1 : 0;
        $eventInfo->canSendMail = $user->isAuthorized('send fma mail') ? 1 : 0;
        $eventInfo->userPersonId = $user->getId();
        if ($eventInfo->event == null || !$eventInfo->event->title) {
            $this->AddErrorMessage('Could not locate event.');
        }
        else {
            if ($eventInfo->event->description) {
                $description = TText::HtmlToFlatText($eventInfo->event->description);
                $eventInfo->event->description = $description;
            }
            else {
                $eventInfo->event->description = '';
            }
            if ($eventInfo->event->repeatInfo) {
                // expand repeat rule to display text.
                $eventInfo->event->repeatInfo = date_repeat_rrule_description($eventInfo->event->repeatInfo);
            }
        }

        $this->SetReturnValue($eventInfo);
    }
}