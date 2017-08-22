<?
/** Class: TSendMailForm ***************************************/
/// Enter email list message
/**
*****************************************************************/
class TSendMailForm
{
    public function __construct() {
    }

    public static function Build($formData) {
        TTracer::Trace("Build Send Mail form");
        $result = TDiv::Create('sendMailForm');
        $result->add(THtml::Header(2,"Send messages"));
        $result->add(THtml::Header(3,"List: ".$formData->listName));
        $addressPanel = TFieldSet::Create("addressPanel", 'Use this form to send a message to the list' ,'clearBoth noTop');
        $addressPanel->addInputField('subject','Subject:','short','extraWide',$formData->subject);
        $addressPanel->addTextAreaField('messageText','Message:','short','extraWide',$formData->messageText,12);
        $buttonPanel = TDiv::Create('buttons','buttonPanel');
        $buttonPanel->add(new TActionButton('test','sendTest','Test Message' ));
        $buttonPanel->add(new TActionButton('send','send','Send Messages' ));
        $buttonPanel->add(new TActionButton('cancel','menu','Cancel' ));
        $addressPanel->add($buttonPanel);
        $result->add($addressPanel);
        return $result;


//        $this->pageController->addMainContent($heading);
//        $this->pageController->addMainContent($addressPanel);



    }

    public function __toString() {
        return 'TSendMailForm';
    }
}
// end TSendMailForm