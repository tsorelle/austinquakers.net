<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/9/2015
 * Time: 4:01 PM
 */
class SendEventInvitationsCommand extends TServiceCommand
{

    protected function run()
    {
        $user = TDrupalUser::GetCurrentUser();
        if (!$user->isAuthorized('send fma mail')) {
            $this->AddErrorMessage('Sorry, you are not authorized to send messages.');
            return;
        };
        /*
                event: Tops.EventInfo;
                listId: number;
                testMessageOnly : number;
                comment: string;
         */
        $request = $this->GetRequest();
        if (!$request) {
            throw new Exception('No request received');
        }

        $subject = $request->event->title;
        $when = $request->event->when;
        $eventTypeText = $request->event->type == 'calendar' ? 'meeting service opportunity' : 'event';
        $signupActon = ($request->event->type == 'calendar') ? 'set a reminder' : 'sign up';
        $eventId = $request->event->eventId;
        $link = "http://www.austinquakers.net/signup?eid=$eventId&pid=%d";

        $messageText = "Friends Meeting of Austin invites you to take part in this $eventTypeText.".
                "\n\nWhat: $subject\nWhen: $when \n";

        if ($request->event->location) {
            $messageText .= 'Where: '.$request->event->location."\n";
        }
        if ($request->event->description) {
            $description = TText::HtmlToText($request->event->description);
            $messageText .= "\nDescription: $description\n";
        }
        if ($request->comment) {
            $messageText .= $request->comment."\n";
        }
        $messageText .= "\nTo $signupActon click this link:\n$link\n";

        $distributor = new TMailDistributor($request->listId);
        if ($request->testMessageOnly) {
            $distributor->sendTestMessage($subject, $messageText);
            $this->AddInfoMessage("Test message sent.");
        }
        else {
            $messageCount = $distributor->sendMail($subject, $messageText);
            $this->AddInfoMessage("$messageCount messages are queued for sending.");
        }
    }
}