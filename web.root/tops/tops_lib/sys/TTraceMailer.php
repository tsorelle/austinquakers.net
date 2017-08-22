<?php
/*****************************************************************
Class:  TTraceMailer
Description: Dummy mailer for testing.
*****************************************************************/
class TTraceMailer
{
    public function send($message) {

        $from = $message->getFromAddress();
        $to = $message->getRecipients();
        $subject = $message->getSubject();
        $returnAddress = $message->getReturnAddress();
        $replyTo =  $message->getReplyTo();
        $message->getContentType();

        TTracer::Trace(
            htmlspecialchars("E-Mail - From: $from; To: $to; Subject: $subject")
        );

    } //  send


}
// end TPearMailer

