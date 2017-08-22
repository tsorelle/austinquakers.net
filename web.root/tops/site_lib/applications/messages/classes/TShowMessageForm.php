<?php
/** Class: TShowMessageForm ***************************************/
/// show form for email messages
/************************************************************/
class TShowMessageForm extends TPageAction
{
    protected function run() {
        TTracer::Trace('TShowMessageForm::run');
        $formData = $this->getArg();
        // TTracer::ShowArray($formData);

        $addressPanel = TFieldSet::Create("addressPanel", 'Use this form to send a message to FMA' ,'clearBoth noTop');
        if ($formData->recipientName) {
            $this->pageController->setFormVariable('recipientName',$formData->recipientName);
            $this->pageController->setFormVariable('recipient',$formData->recipient);
            $heading = "Send a message to the $formData->recipientName.";
        }
        else {
            $heading = TDrupalSnippet::Get('contact us heading');
            $listData = TMailbox::GetMailboxList(1);
            // TTracer::ShowArray($listData);
            $recipientList = THtmlList::CreateDropDownList($listData,
                                    $formData->recipient,'recipient');
            $addressPanel->addComponent('To:','short',$recipientList);
        }

        $addressPanel->addInputField('fromName','Your name:','short','extraWide',$formData->fromName);
        $addressPanel->addInputField('email','Your e-mail:','short','extraWide',$formData->email);
        $addressPanel->addInputField('subject','Subject:','short','extraWide',$formData->subject);
        $addressPanel->addTextAreaField('messageText','Message:','short','extraWide',$formData->messageText,6);
        if (!TUser::Authenticated()) {
            $addressPanel->addText('<p class="formText">To help us deter spam, please answer this question: What is the first name of George Fox?</p>');
            $addressPanel->addInputField('spamCatcher','Answer:','short','extraWide','');
        }

//        $buttonPanel = new TFieldSet('messageButtons','','inlineButtons');
//        $buttonPanel->add(new TActionButton('send', 'send','Send Message' ));
        $buttonPanel = TDiv::Create('buttons','buttonPanel');
        $buttonPanel->add(new TActionButton('send', 'sendMessage','Send Message' ));
        $addressPanel->add($buttonPanel);


        if ($heading)
            $this->pageController->addMainContent('<h2>'.$heading.'</h2>');
        $this->pageController->addMainContent($addressPanel);
//        $this->pageController->addMainContent($buttonPanel);

    }

    public function __toString() {
        return 'TShowMessageForm';
    }
}
