<?php
/** Class: TMailboxListing ***************************************/
/// build table of mailboxes
/**
*****************************************************************/
class TMailboxListing
{
    public function __construct() {
    }

    public function __toString() {
        return 'TMailboxListing';
    }

    public static function Build($list, $postbackUrl) {
        TTracer::Trace('Build');
                $table = THtmlTable::Create();
        $newBoxUrl = sprintf($postbackUrl,'addMailbox');
        $result = TDiv::Create('mailboxList');
        $header = '<h3>Mailboxes</h3><p><a href="%s">Add a new mail box</a><p></h3>';
        $result->add(sprintf($header,$newBoxUrl));

        if (empty($list))
            return $result;
        $table->addColumnTitles('Mailbox,Name,Address,Description,&nbsp;');

        $urlFormat = $postbackUrl.'&mailboxId=%d';

        foreach ($list as $box) {
            $row = THtmlTable::CreateRow();

            $cell = THtmlTable::CreateLinkCell($box->mailboxCode,sprintf($urlFormat,'editMailbox',$box->mailboxId));
            $row->add($cell);
            $row->add(THtmlTable::CreateCell($box->name));
            $row->add(THtmlTable::CreateCell($box->email));
            $row->add(THtmlTable::CreateCell($box->description));
            $cell = THtmlTable::CreateLinkCell('Delete',sprintf($urlFormat,'dropMailbox',$box->mailboxId));
            $row->add($cell);

            $table->addRow($row);
        }

        $result->add($table);
        return $result;
    }

}
// end TMailboxListing


?>