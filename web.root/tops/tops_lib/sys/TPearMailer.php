<?php
require_once "Mail.php"; // from PEAR
/*****************************************************************
Class:  TPearMailer
Description:
*****************************************************************/
class TPearMailer
{
    private $mailMethod;
    private $host;
    private $username;
    private $password;
    private $pearMailer;
    private $enabled = true;

    public function __construct() {
        $errorSettings = error_reporting();
        error_reporting(E_ERROR | E_WARNING | E_PARSE); // surpress strict warnings from pear mail.
        //TTracer::Trace('TPearMailer::Construct');
        $config = TConfiguration::GetSettings();
        $this->mailMethod = $config->getValue('smtp','method','mail');
        $this->host = $config->getValue('smtp','host','localhost');
        $this->username = $config->getValue('smtp','username',false);
        $this->password = $config->getValue('smtp','password',false);
        $this->enabled = $config->getValue('smtp','enabled',true);
        $auth = (!empty($username));
        if ($this->mailMethod != 'smtp')
            $this->pearMailer = Mail::factory($this->mailMethod);
        else
            $this->pearMailer = Mail::factory('smtp',
              array (
              'host' => $this->host,
                'auth' => $auth,
                'username' => $this->username,
                'password' => $this->password));

        error_reporting($errorSettings);
    }

    public function send($message) {
        //TTracer::Trace('send');
        //TTracer::ShowArray($message);
        $errorSettings = error_reporting();
        error_reporting(E_ERROR | E_WARNING | E_PARSE); // surpress strict warnings from pear mail.

        $from = $message->getFromAddress();
        $to = $message->getRecipients();
        $headers = array (
          'From' => $from,
          'To' => $to,
          'Subject' => $message->getSubject(),
          'Date' => date("r"));
        $returnAddress = $message->getReturnAddress();
        if (!empty ($returnAddrss)) {
            $headers['Return-Path'] = $returnAddress;
        }
        $headers['Reply-To'] = $message->getReplyTo();
        switch ( $message->getContentType() )
        {
            case  TContentType::$Text  :
                $body = $message->getMessageBody();
//                TTracer::Trace("Content type: text");
                break;

            case  TContentType::$Html :
//                TTracer::Trace("Content type: html");
                $headers['MIME-Version'] = '1.0';
                $headers['Content-type'] = 'text/html; charset=iso-8859-1';
                $body =  '<html><body>'.$message->getMessageBody().'</body></html>';
                break;

            case TContentType::$MultiPart :
                require_once('Mail/mime.php');
                $mime = new Mail_mime($crlf);
                $mime->setTXTBody($message->getTextPart());
                $mime->setHTMLBody($message->getMessageBody());
                // $mime->addAttachment($file, 'text/plain');
                //do not ever try to call these lines in reverse order
                $body = $mime->get();
                break;

            default:
                error_reporting($errorSettings);
                trigger_error('Unsupported e-mail content type: '.$message->contentType,E_USER_ERROR);
                return;
        }

        // Send messge

//        TTracer::Trace("method: $this->mailMethod");
//        TTracer::ShowArray($this->pearMailer);
 //       TTracer::Trace("to: $to");

/*
print '<pre style="background-color:white;text-align:left">'.
$body.'</pre><hr/>';
*/


        if ( $this->enabled )
        {
            $mail = $this->pearMailer->send(
                $to,
                $headers,
                $body);


             if (PEAR::isError($mail)) {
                 $result = $mail->getMessage();
                 error_reporting($errorSettings);
                 return $result;
             }
        }
        else
            TTracer::Trace('Mail disabled...');

        error_reporting($errorSettings);
        return true;
    }

}
// end TPearMailer

