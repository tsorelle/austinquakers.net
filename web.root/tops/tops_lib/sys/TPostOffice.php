<?php
/*****************************************************************
class TPostOffice
                                9/5/2007 6:13AM
*****************************************************************/
class TPostOffice
{
    private static $Mailer;


    public static function GetMailboxAddress($addressId) {
        static $mailboxes;
        if (empty($mailboxes))
            $mailboxes = TMailbox::GetAddresses();
        if (!isset($mailboxes[$addressId]))
            throw new Exception("Mailbox code '$addressId' not defined.");
        return $mailboxes[$addressId];
    }

    public static function CreateMessageToUs($addressId='support')
    {
        TTracer::Trace('CreateMessageToUs');
        $result = new TEMailMessage();

        $recipients = explode(',',$addressId);
        foreach ($recipients as $addressId) {
            $mailbox = self::GetMailboxAddress($addressId);
            $result->addRecipient($mailbox->address,$mailbox->name);
        }
        return $result;
    }  //  newSystemEmailMessage

    public static function CreateMessageFromUs($addressId='support',$subject=null,$body=null,$contentType='text')
    {

        // TTracer::Trace("CreateMessageFromUs($addressId) address: $address; name: $identity");
        $bounce = self::GetMailboxAddress('bounce');
        $result = new TEMailMessage();
        $mailbox = self::GetMailboxAddress($addressId);
        $result->setFromAddress($mailbox->address, $mailbox->name);
        $result->setReturnAddress($bounce->address);
        if (!empty($subject))
            $result->setSubject($subject);
        if (!empty($body)) {
            if ($contentType == 'text')
                $result->setMessageBody($body);
            else {
                $result->setHtmlMessageBody($body);
                if ($contentType != 'html')
                    // otherwise assume contentType contains mult-part plain text
                    $result->setAlternativeText($contentType);
            }
        }
        return $result;
    }  //  newEmailMessageFromUs


    public static function Send($message) {
        TTracer::ShowArray($message);
        TTracer::Trace('Send to: '.htmlentities($message->getRecipients()));
        if (!isset(TPostOffice::$Mailer))
             TPostOffice::$Mailer =
                TClassFactory::Create(
                    'mailer','TPhpMailer','tops_lib/sys');
        return TPostOffice::$Mailer->send($message);
    }

    public static function SendMessage($to, $from, $subject, $bodyText, $bounce = null) {
        //TTracer::Trace('SendMessage');
        $message = new TEMailMessage();
        $message->setRecipient($to);
        $message->setFromAddress($from);
        $message->setSubject($subject);
        $message->setMessageBody($bodyText);
        if ($bounce)
            $message->setReturnAddress($bounce);
        TPostOffice::Send($message);
    }

    public static function SendMessageToUs($fromAddress, $subject, $bodyText, $addressId='admin') {
        $message = TPostOffice::CreateMessageToUs($addressId);
        $message->setFromAddress($fromAddress);
        $message->setSubject($subject);
        $message->setMessageBody($bodyText);
        $message->setReturnAddress($fromAddress);
        TPostOffice::Send($message);
    }

    public static function SendMessageFromUs($recipients, $subject, $bodyText, $addressId='admin' ) {
       // TTracer::Trace('SendMessageFromUs');
        $message = TPostOffice::CreateMessageFromUs($addressId, $subject, $bodyText, 'text');
        $message->setRecipient($recipients);
        TPostOffice::Send($message);
    }

    public static function SendHtmlMessageFromUs($recipients, $subject, $bodyText, $addressId='support' ) {
        $message = TPostOffice::CreateMessageFromUs($addressId, $subject, $bodyText, 'html');
        $message->setRecipient($recipients);
        TPostOffice::Send($message);
    }

    public static function SendMultiPartMessageFromUs($recipients, $subject, $bodyText, $textPart = 'html', $addressId='support' ) {
        $message = TPostOffice::CreateMessageFromUs($addressId, $subject, $bodyText, $textPart);
        $message->setRecipient($recipients);
        TPostOffice::Send($message);
    }

    public static function IsValidEmail($address) {
        if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $address)) {
          return true;
        }
        return false;
    }

    public static function checkForBadContent($text,$checkForUrls=true)
    {
        $text = strtolower($text);
        if ($checkForUrls && strstr($text,'http:'))
              return true;
        $badWords = array(
              'adipex',
              'cialis',
              'c i a l i s',
              'viagra',
              'v i a g r a',
              'xenical',
              'casino',
              'xxx',
              'penis',
              'tits',
              'ringtones',
              'ring tones',
              'home loan',
              'home loans',
              'home-loan',
              'home-loans',
              'home equity',
              'home-equity',
              'debt consolidation',
              'payday loans',
              'payday loan',
              'payday-loans',
              'payday-loan',
              'porn',
              'adult-sex',
              'nude pic',
              'nude-pic',
              'sex clip',
              'sex-clip',
              'hentai'
              );
        foreach($badWords as $word)
            if (preg_match('/\b'.$word.'\b/',$text))
                return true;

        return false;
    }  //  hasBadContent

    public static function checkForSpam($name, $email, $subject, $body, $spamTrap=false)
    {
        if (!empty($spamTrap))
            return true;


        if (isset($_SERVER['HTTP_REFERER'])) {
            $url = strtolower($_SERVER['HTTP_HOST']);
            $url = ereg_replace("www.", "", $url);
            if (!ereg($url,strtolower($_SERVER['HTTP_REFERER'])))
                //Security Violation: Unauthorized referer
                return true;
        }

        if (
            ereg("\n|\r", $name)||
            ereg("\n|\r", $subject) ||
            (strlen($name) > 100) ||
             (strlen($subject) > 150)
            )
            return true;


        if (($email=="") || (!ereg("^([[:alnum:]\_\.\-]+)(\@[[:alnum:]\.\-]+\.+[[:alpha:]]+)$", $email)) || (strlen($email)>100))
            // Invalid recipient field
            return true;

        if ((ereg("\n|\r", $name)) || (ereg("\n|\r", $subject)) || (strlen($name)>100))
            // Invalid recipient name field
            return true;

        if (($email!="") && (!ereg("^([[:alnum:]\_\.\-]+)(\@[[:alnum:]\.\-]+\.+[[:alpha:]]+)$", $email)))
            // invalid email
            return true;;

        if ((ereg("\n|\r", $subject)) || (strlen($subject)>100))
            return true;

        if (self::checkForBadContent($name) || self::checkForBadContent($email)
            || self::checkForBadContent($subject) || self::checkForBadContent($body,false))
            return true;

        /*
        list($mailaddress,$maildomain) = split('@',$email);
        $spammers = new TQuery();
        $isSpammer = $spammers->returnValue(
            'SELECT COUNT(*) as spammer from spammers WHERE '.
            "(domain='$maildomain' and blockdomain = 1) ".
            "or (address='$mailaddress' and domain='$maildomain') ".
            "or name='$name' ");
        if ($isSpammer)
            return true;
        */
        return false;
    }  //  isSpam



}   // finish class TPostOffice

