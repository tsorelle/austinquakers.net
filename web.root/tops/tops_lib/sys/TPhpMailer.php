<?php
/*****************************************************************
Class:  TPhpMailer
Description: Uses PHP send() method. Does not support authenticated
SMTP external mail servers or file attachments.  Use TPearMailer for that.
*****************************************************************/
class TPhpMailer
{
    private $mimeBoundary;
    private $simpleBodyText = 'This is a multi-part mime message';

    private $enabled = true;

    public function __construct() {
                TTracer::Trace('PhpMailer::Construct');

        $config = TConfiguration::GetSettings();
        $this->enabled = $config->getValue('smtp','enabled',true);
    }

    private function setMimeBoundary()
    {
        $semi_rand = md5(time());
        $this->mimeBoundary = "==Multipart_Boundary_x{$semi_rand}x";
    }  //  setBoundaryId

    private function getMultiPartHeaders()
    {
        return(
            "\nMIME-Version: 1.0\n" .
            'Content-Type: multipart/alternative; '.
            'boundary="'.$this->mimeBoundary.'"'."\n\n".
            $this->simpleBodyText."\n");
    }  //  getMultiPartHeaders

    private function getMultiPartBody($message)
    {
        if (empty($this->alternateBodyText))
            $this->alternateBodyText = strip_tags($this->messageBody);
        return(
            '--'.$this->mimeBoundary."\n".
            'Content-Type: text/plain; charset=ISO-8859-1' ."\n".
            'Content-Transfer-Encoding: 8bit'."\n\n". // other examples use 7bit
            $message->getAlternateBodyText(). "\n".
            '--'.$this->mimeBoundary . "\n".
            'Content-Type: text/html; charset=ISO-8859-1' ."\n".
            'Content-Transfer-Encoding: 8bit'. "\n\n".
            '<html><body>'.$message->getMessageBody().'</body></html>'. "\n".
            '--' . $this->mimeBoundary . "--\n");
    }  //  getMultiPartBody


    public function send($message) {

        TTracer::Trace('php send');
        $from = $message->getFromAddress();
        $to = $message->getRecipients();
//        $message->getRecipientList(),
        $subject = $message->getSubject();
TTracer::Trace("SUBJECT = $subject");
        $returnAddress = $message->getReturnAddress();
        $replyTo =  $message->getReplyTo();

        $headers = "From: $from";
        $headers .= "\nReply-To: $replyTo";
        $headers .= "\nReturn-Path: $returnAddress";

        switch ( $message->getContentType() )
        {
            case  TContentType::$Text  :
                TTracer::Trace('content: text');
                $body = $message->getMessageBody();
                break;

             case  TContentType::$Html :
               $headers  .=  "\nMIME-Version: 1.0\n" .
                              "Content-type: text/html; charset=iso-8859-1";
                $body =  '<html><body>'.$message->getMessageBody().'</body></html>';
                break;

            case TContentType::$MultiPart :
                // trace('content: multipart');
                $this->setMimeBoundary();
                $headers .= $this->getMultiPartHeaders();
                $headers .= $this->getMultiPartBody($message);
                $body = '';
                // trigger_error('Multipart e-mail not supported yet.',E_USER_ERROR);
                break;

            default:
            TTracer::Trace('Unsupported e-mail content type');
                trigger_error('Unsupported e-mail content type: '.$this->contentType,E_USER_ERROR);
                return;
        }
TTracer::Assert($this->enabled,"enabled");
        if ( $this->enabled )
        {
            mail($to, $subject, $body, $headers);
        }
        else  {
            echo("<p>PHP Mailer disabled. Message:</p>");
            echo('Headers: ****************<br>');
            echo (nl2br($headers));
            echo '<br/>*******************<br/>';
            echo("Recipients: $to<br>");
            echo("From: $from<br>");
            echo("Reply: $replyTo<br>");
            echo("Subject: $subject<br>");
            echo("Bounce to: $returnAddress<br>");
            echo('Body: ****************<br>');
            echo nl2br($body);
            echo('<br>****************<br><br>');
        }
    } //  send


}
// end TPearMailer

