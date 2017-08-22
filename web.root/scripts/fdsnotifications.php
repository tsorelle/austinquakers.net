<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/27/2015
 * Time: 1:27 PM
 *
 * Send notifications for FDS schedule.
 * Command line syntax, for chron
 *
 *  /ramdisk/bin/php5 -q /home1/austinqu/public_html/austinquakers.net.root/scripts/runscript.php fdsnotifications
 *
 */
print 'FDS Notifications start: '.date(DateTime::RFC1123)."\n";
$assignmentDate = date('Y-m-d');
$day = strtotime($assignmentDate);
if (date('w', $day) != 0) {
    $day = strtotime("next Sunday", $day);
}
$assignmentDate = date('Y-m-d', $day);
$dateText = date('F j', $day);

print "Sending FDS reminders for $assignmentDate\n";


$messages = TFdsAssignment::GetNotificationsList($assignmentDate);
if (empty($messages)) {
    print "No reminders to send this week.\n\n";
}
else {
    $mailbox = TMailbox::Find('fdsclerk');
    $firstDayClerk = $mailbox->getEmail();
    $sentCount = 0;
    print "\n";
    foreach($messages as $message) {
        $to = $message->address;
        $subject = "Your First Day School Reminder: $assignmentDate";
        $bodyText = "This is a reminder that you are scheduled to ".$message->role." the ".$message->ageGroup." class ".
            "at Friends Meeting of Austin on ".$dateText.".\n\n".
            "Thank you for your help.\n";
        $from = "FMA First Day School<$firstDayClerk>";

        print "Sending message to $to\n";

        // print("$to;\n $from;\n $subject;\n $bodyText\n\n");

        TPostOffice::SendMessage($to, $from, $subject, $bodyText, $from);
        $sentCount++;
        if ($sentCount > 50) {
            error_log("FMA reminder mails exceeded limit");
            error_log("FMA reminder mails exceeded limit",1,"batchlog@austinquakers.org");
        }
    }
    error_log("Sent $sentCount FMA reminders",1,"batchlog@austinquakers.org");
}
print "\n";
print 'FDS Notifications end: '.date(DateTime::RFC1123)."\n";