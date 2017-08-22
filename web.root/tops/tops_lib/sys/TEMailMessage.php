<?php
class TEMailMessage {
    private $messageBody;
    private $alternateBodyText;
    private $recipientList;
    private $fromAddress;
    private $replyTo;
    private $subject;
    private $returnAddress = '';
    private $contentType;


    public function addRecipient($recipient, $name=null)
    {
       $recipient = TEMailMessage::FormatAddress($recipient, $name);

        if ( $this->recipientList )
        {
            $this->recipientList = $this->recipientList.", ".$recipient;
        }
        else
            $this->recipientList = $recipient;
       //TTracer::Trace("Recipients: $this->recipientList");
    }  //  addRecipient

    public function setRecipient($recipient, $name=null)
    {
        $this->recipientList = TEMailMessage::FormatAddress($recipient, $name);
       //TTracer::Trace("Recipients: $this->recipientList");
    }  //  setRecipient

    public function setFromAddress($sender, $name=null)
    {
      $this->fromAddress = TEMailMessage::FormatAddress($sender, $name);
    }  //  setFromAddress

    public function setReturnAddress($address, $name=null)
    {
      $this->returnAddress = TEMailMessage::FormatAddress($address, $name);
    }  //  setReturnAddress

    public function setReplyTo($address, $name=null)
    {
      $this->replyTo = TEMailMessage::FormatAddress($address, $name);
    }  //  setReturnAddress

    public function setSubject($value)
    {
        $this->subject = stripslashes($value);
    }  //  setSubject



    public function setMessageBody($value)
    {
        $value = str_replace ("\r\n", "\n", $value);
        $this->messageBody = stripslashes($value);
    }  //  setMessageBody


    public function setHtmlMessageBody($text)
    {
        TTracer::Trace('setHtmlMessageBody');
        $this->setMessageBody($text);
        $this->contentType = TContentType::$Html;
    }  //  setAlternateBodyText


    public function setAlternateBodyText($text)
    {
        $this->alternateBodyText = $text;
        $this->contentType = TContentType::$MultiPart;
    }  //  setAlternateBodyText

    public static function FormatAddress($email, $name='') {
        if (!empty($name))
            return sprintf('"%s" <%s>',$name,$email);
        return $email;
    }


    public function getSubject() {
        return $this->subject;
    }
    public function getFromAddress() {
        return $this->fromAddress;
    }

    public function getRecipients() {
        return $this->recipientList;
    }

    public function getReturnAddress() {
        if (empty($this->returnAddress))
            return $this->fromAddress;
        return $this->returnAddress;
    }

    public function getReplyTo() {
        if (empty($this->replyTo))
            return $this->fromAddress;
        return $this->replyTo;
    }

    public function getContentType() {
        if (!isset($this->contentType))
            $this->contentType = TContentType::$Text;
        return $this->contentType;
    }

    public function getMessageBody() {
        return $this->messageBody;
    }

    public function getTextPart() {
        if (empty($this->alternateBodyText))
            return strip_tags($this->messageBody);
        return $this->alternateBodyText;
    }
} // TMailMessage

