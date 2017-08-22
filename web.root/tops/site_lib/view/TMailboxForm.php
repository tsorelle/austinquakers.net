<?php
/** Class: TMailboxForm ***************************************/
/// build data entry form for mailbox
/**
*****************************************************************/
class TMailboxForm
{
    public function __construct() {
    }

    public function __toString() {
        return 'TMailboxForm';
    }

    public static function Build($mailBox) {
        TTracer::Trace('Build');

        $editPanel = TFieldSet::Create("mailboxEdit", "Mail Box",'leftLabels');
        $editPanel->addInputField('mailboxCode', 'Mailbox code'  ,'narrow','wide',$mailBox->getMailboxCode());
        $editPanel->addInputField('name','Name'  ,'narrow','wide',  $mailBox->getName());
	    $editPanel->addInputField('email', 'Address'  ,'narrow','wide',  $mailBox->getEmail());
        $editPanel->addInputField('description', 'Description'  ,'narrow','wide',  $mailBox->getDescription());
        $editPanel->addCheckBoxField('selectionList',"Show on contact form:", $mailBox->getSelectionList());

        $buttonPanel = new TFieldSet('membershipButtons','','inlineButtons');
        $buttonPanel->add(new TActionButton('submit', 'updateMailbox' ,'Update' ));
        $buttonPanel->add(new TActionButton('cancel', 'showList' ,'Cancel'));

        $result = TDiv::Create('mailBoxForm');
        $result->add($editPanel);
        $result->add($buttonPanel);

        return $result;
    }

}
// end TMailboxForm


?>