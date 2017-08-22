<?
/** Class: TEmailQueueStatusTable ***************************************/
/// display status off current queues
/**
*****************************************************************/
class TEmailQueueStatusTable
{
    public function __construct() {
    }

    public function __toString() {
        return 'TEmailQueueStatusTable';
    }

    public static function Build($messages) {
        $result = TDiv::Create("messageQueueStatus");
        $result->add(THtml::Header(2,"Outgoing Messages"));
        if (sizeof($messages) == 0) {
            $result->add("No outgoing messages pending.");
        }
        else {
            $table = new THtmlTable('messageQueueStatusTable');
            $table-> addColumnTitles('List,Time Sent,Sender,Subject, Messages Sent');
            foreach($messages as $record) {
                $row = THtmlTable::CreateRow();
                $row->addCell($record->listName);
                $row->addCell($record->sendtime);
                $row->addCell($record->sender);
                $row->addCell($record->subject);
                $row->addCell($record->sentCount.' of '.$record->recipientCount);
                $table->add($row);
            }
            $result->add($table);
        }
        return $result;
    }
}
// end TEmailQueueStatusTable