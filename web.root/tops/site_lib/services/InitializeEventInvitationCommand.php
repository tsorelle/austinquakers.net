<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/9/2015
 * Time: 2:49 PM
 */
class InitializeEventInvitationCommand extends TServiceCommand
{

    protected function run()
    {
        // $request = $this->GetRequest();
        // $eventId = $request->eventId;
        $eventId = $this->GetRequest();
        if (!$eventId) {
            $this->AddErrorMessage('No event identifier received.');
            return;
        }
        $responseData = TEvent::getEventInfo($eventId);
        $user = TDrupalUser::GetCurrentUser();
        $responseData->canEdit = $user->isAuthorized('send fma mail');
        if (!$responseData->event->title) {
            $this->AddErrorMessage('Could not locate event.');
        }
        /*
        if ($responseData->event->description) {
            $responseData->event->description = TText::HtmlToText($responseData->event->description);
        }
        else {
            $responseData->event->description = '';
        }
        */
        if ($responseData->event->repeatInfo) {
            // expand repeat rule to display text.
            $responseData->event->repeatInfo = date_repeat_rrule_description($responseData->event->repeatInfo);
        }
        $mailingLists = array();
        $elists = TEList::getElists();
        foreach ($elists as $list) {
            if ($list->code != 'fmanotes') {
                $listItem = new stdClass();
                $listItem->text = $list->name;
                $listItem->value = $list->id;
                $listItem->title = 'Mailing list: ' . $list->name;
                array_push($mailingLists, $listItem);
            }
        }

        $responseData->mailingLists = $mailingLists;
        $this->SetReturnValue($responseData);
    }


}