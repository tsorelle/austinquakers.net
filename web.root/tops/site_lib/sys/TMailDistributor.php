<?
/** Class: TMailDistributor ***************************************/
/// Controller for sending mail to lists
/**
*****************************************************************/
class TMailDistributor
{
    private $lid;
    public function __construct($lid) {
        $this->lid = $lid;
    }

    function getRecipientAddress($pid)
    {
      $sql =
        'SELECT email, altEmail, e.personId, p.firstName, p.lastName '.
        'FROM emails e '.
        'LEFT OUTER JOIN persons p on e.personId = p.personId '.
        'WHERE listId = ? and p.personId = ?';

        $count = 0;
        $statement = TSqlStatement::ExecuteQuery($sql,'ii', $this->lid, $pid);
        $email=null; $altEmail=null; $pid=null; $firstName=null; $lastName=null;
        $statement->instance->bind_result($email, $altEmail, $pid, $firstName, $lastName);
        if ($statement->next()) {
            $address = empty($altEmail) ? $email : $altEmail;
            return "$firstName $lastName <$address>" ;
        }

        // echo "<p>no result from getRecipientAddress</p>";
        return '';
    }  //  getRecipientAddress


    function getReturnAddress() {
        return 'bounce@austinquakers.org';
    }

    function formatMessageText($text) {
        $lid = $this->lid;
        return
            stripslashes($text).
            "\n\nTo cancel your subscription, clink this link:\n".
            "http://www.austinquakers.org/unsubscribe.php?lid=$lid&pid=%d";
    }

    function queueMail($messageId) {

        $sql =
            'INSERT INTO mailmessagerecipients  (mailMessageId, personId, address) '.
            'SELECT '.$messageId.' as mailMessageId, e.personId, '.
        	 "CONCAT(p.firstName, ' ', p.lastName, ' <', email,'>') ".
             'FROM emails e LEFT OUTER JOIN persons p on e.personId = p.personId '.
             "WHERE listId = ? and eMail is not null and (altEmail is null or altEmail = '')";

        $count = TSqlStatement::ExecuteNonQuery($sql,'i',$this->lid);

        $sql =
            'INSERT INTO mailmessagerecipients  (mailMessageId, personId, address) '.
            'SELECT '.$messageId.' as mailMessageId, e.personId, '.
        	 "CONCAT(p.firstName, ' ', p.lastName, ' <', altEmail,'>') ".
             'FROM emails e LEFT OUTER JOIN persons p on e.personId = p.personId '.
             "WHERE listId = ? and (altEmail is not null and altEmail <> '')";
        $count += TSqlStatement::ExecuteNonQuery($sql,'i',$this->lid);

        return $count;

    }

    public function sendTestMessage($subject, $text) {
        $list = new TEList();
        $list->select($this->lid);
        $to = TUser::GetFullEmailAddress();
        TTracer::Trace("test message to ".htmlentities($to));
        $from = $list->getFromName().'<'.$list->getMailBox().'@austinquakers.org>';
        $bodyText =  $this->formatMessageText($text,1);
        $bounce = $this->getReturnAddress();
        TPostOffice::SendMessage($to, $from, $subject, $bodyText, $bounce);
    }

    public function sendMail($subject, $text)
    {
        error_reporting(E_ALL);
        $list = new TEList();
        $list->select($this->lid);
        $bounceAddress = $this->getReturnAddress();
        $address  = $list->getMailBox().'@austinquakers.org';
        $identity = $list->getFromName();
        $msgText =  $this->formatMessageText($text);

        $queue = new TEmailQueue();
        $queue->setSender("$identity <$address>");
        $queue->setReturnAddress($bounceAddress);
        $queue->setSubject($subject);
        $queue->setMessage($msgText);
        $queue->setListId($this->lid);
        $postedBy = TUser::GetShortName();
        TTracer::Trace("User = $postedBy");
        $queue->setPostedBy($postedBy);
        $postedDate = date( 'Y-m-d H:i:s');
        TTracer::Trace("Posted date = $postedDate");
        $queue->setPostedDate( $postedDate );

        $queue->add();
        $messageId = $queue->getId();

        $count = $this->queueMail($messageId);
        /*
        $queue = new TEmailQueue();
        $queue->select($messageId);


        $queue->setRecipientCount($count);
        $queue->update();
        */
        TEmailQueue::UpdateRecipientCount($messageId,$count);

        return $count;
    }


    public function __toString() {
        return 'TMailDistributor';
    }

}
// end TMailDistributor