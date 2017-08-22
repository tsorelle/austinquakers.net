<?php
/** Class: TMessageLog ***************************************/
/// tracks email list messages sent
/**
*****************************************************************/
class TMessageLog
{
    public function __construct() {
    }

    public function __toString() {
        return 'TMessageLog';
    }

    public static function PostEntry($lid, $messageId, $sender) {
        $sql = "INSERT INTO messagelog (sendTime,sender,elistId,messageId) VALUES (NOW(), ?, ?, ?)";
        TSqlStatement::ExecuteNonQuery($sql,"sii",$sender,$lid,$messageId);
    }

    public static function GetPendingMessageCounts() {
        $result = array();
        $sql = 'select elist.listName, msglog.sendtime, msglog.sender,mm.subject,'.
            'mm.sentCount, mm.recipientCount from messagelog msglog '.
            'join elists elist on msglog.elistid = elist.elistId '.
            'join mailmessages mm on mm.mailMessageId = msglog.messageId '.
            'where sentCount < recipientCount order by elist.listName, msglog.sendtime';

        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($listName, $sendtime, $sender,$subject,$sentCount, $recipientCount);
        $count = 0;
        while ($statement->Next()) {
            $record = new stdclass();
            $record->listName = $listName;
            $record->sendtime = $sendtime;
            $record->sender = $sender;
            $record->subject = $subject;
            $record->sentCount = $sentCount;
            $record->recipientCount = $recipientCount;
            $result[++$count] = $record;
        }
        return $result;

    }

    public static function GetMessageCount($messageId = 0) {
        $sql = 'select count(*) from mailmessagerecipients r '.
                'join  messagelog l on r.mailMessageId = l.messageId ';
        if (empty($messageId))
            return TSqlStatement::ExecuteScaler($sql);
        $sql .= ' where r.mailMessageId = ?';
        return TSqlStatement::ExecuteScaler($sql,'i',$messageId);
    }


}
// end TMessageLog