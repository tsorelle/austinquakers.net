<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/8/2015
 * Time: 5:15 PM
 */
print 'Send FMA Reminders start: '.date(DateTime::RFC1123)."\n";
$currentDate = date('Y-m-d');
$day = strtotime($currentDate);
if (date('w', $day) != 0) {
    $day = strtotime("next Sunday", $day);
}
$currentDate = date('Y-m-d', $day);
$dateText = date('F j', $day);

print "Sending FMA reminders for $currentDate\n";

$upcoming = TEvent::getFutureEvents(1);

if (empty($upcoming)) {
    print "No reminders to send today.\n\n";
}
else {
    $mailbox = TMailbox::Find('admin');
    $returnAddress = $mailbox->getEmail();

    print "\n";
    $eventLink = 'http://www.austinquakers.net/signup?eid=%d&pid=%d';
    $sentCount = 0;
    foreach($upcoming as $reminder) {
        $response = TEvent::getEventAndPersons($reminder->eventId,$reminder->sessionId);
        if ($response && $response->event) {
            if ( $response->persons) {
                $event = $response->event;
                $persons = $response->persons;
                $messageText = "This is your reminder from Friends Meeting of Austin.\n\n" .
                    "What: $event->title\n" .
                    "When: $event->when\n";
                if ($event->location) {
                    $messageText .= "Where: $event->location\n\n";
                }
                $messageText .= "\n";

                // note: if you use event->decription, convert to plain text using TText::HtmlToText()

                $messageText .= "To find out more, or to cancel your reminders, go to ";
                foreach ($persons as $person) {
                    if ($person->email) {
                        $to = "$person->name <$person->email>";
                        $subject = "Your Reminder from FMA";
                        $link = sprintf($eventLink, $event->eventId, $person->personId);
                        $bodyText = $messageText . $link;
                        $from = "FMA Reminders<$returnAddress>";
                        print "Sending message to $to\n";
                        TPostOffice::SendMessage($to, $from, $subject, $bodyText, $from);
                        $sentCount++;
                        if ($sentCount > 50) {
                            error_log("FMA reminder mails exceeded limit");
                            error_log("FMA reminder mails exceeded limit",1,"batchlog@austinquakers.org");
                        }
                    }
                }
            }
        }
    }

    if ($sentCount > 0) {
        error_log("Sent $sentCount FMA reminders", 1, "batchlog@austinquakers.org");
    }

}
print "\n";
print 'FDS Notifications end: '.date(DateTime::RFC1123)."\n";
