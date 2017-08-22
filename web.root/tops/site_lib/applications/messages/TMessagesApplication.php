<?php
/** Class: TMessagesApplication ***************************************/
/// contact-us page with e-mail form
/**
*****************************************************************/
class TMessagesApplication extends TApplication
{
    public function __construct() {
        $this->setApplicationName('messages');
        $this->pageController = TDrupalPageController::GetInstance();
        $this->pageController->setFormVariable('id', 'messages');
//        $this->pageController->setHeaderSubtitle('Contact us');
        $this->pageController->setPageTitle('Contact us');
    }

    protected function run($cmd=null) {
        if ($_SERVER['SCRIPT_NAME'] != '/index.php')
            return;

        TTracer::Trace('TMessagesApplication::run');
        TTracer::Trace("run: $cmd");
        switch ($cmd) {
            case 'showForm' :
                $this->showForm();
                break;
            case 'sendMessage' :
                $this->sendMessage();
                break;
            default :
                $this->pageController->addErrorMessage("Command '$cmd' is not implemented.");
                break;
        }
    }

    private function showForm($formData=null) {
        TTracer::Trace('showForm');
        if (!isset($formData))
            $formData = $this->emptyMessageForm();
        $this->executeAction('showMessageForm',$formData);
    }

    private function sendMessage() {
        TTracer::Trace('sendMessage');

        $formData = $this->getMessageRequest();
 // TTracer::ShowArray($formData);
 //   return;
        if ($formData->isValid) {
            $fromAddress = TEMailMessage::FormatAddress($formData->email,$formData->fromName);
            TPostOffice::SendMessageToUs(
               $fromAddress, $formData->subject,
               $formData->messageText, $formData->recipient);

            $this->pageController->addInfoMessage('Your message was sent. Thank you.');
            $formData = $this->emptyMessageForm();
            $this->pageController->redirectLocal();
        }
        else
            $this->executeAction('showMessageForm',$formData);
    }

    public function __toString() {
        return 'TMessagesApplication';
    }

    private function emptyMessageForm() {

        TTracer::Trace('emptyMessageForm');
        $formData = new stdClass();

        $request = TRequest::GetInstance();
        $formData->recipient =  $request->get('recipient','');
       // TTracer::Trace("Recipient = $recipient");
        if ($formData->recipient) {
            $mailbox = TMailbox::Find($formData->recipient);
            if ($mailbox->getId())
                $formData->recipientName = $mailbox->getName();
            else
                $formData->message = 'mailbox not found.';
        }
        else {
            $formData->recipient =  'clerks';
        }

        $formData->messageText = '';
        $formData->fromName = '';
        $formData->email = '';
        $formData->subject = '';
        return $formData;
    }

    private function getMessageRequest() {
        TTracer::Trace('getMessageRequest');
        $request = TRequest::GetInstance();
        $request->enableInputCleaning();

        $formData = new stdClass();
        $formData->messageText = $request->get('messageText','');
        $formData->fromName = $request->get('fromName','');
        $formData->subject = $request->get('subject','');
        $formData->email = $request->get('email','');
        $formData->recipient = $request->get('recipient','');
        $formData->recipientName = $request->get('recipientName','');
        $formData->isValid = true;

        if (empty($formData->recipient)) {
            throw new Exception('Recipient code not found in message request.');
        }
        if (empty($formData->fromName)) {
            $this->pageController->addErrorMessage('Please enter your name.');
            $formData->isValid = false;
        }
        if (empty($formData->email)) {
            $this->pageController->addErrorMessage('Please enter your email address.');
            $formData->isValid = false;
        }
        else if (!TPostOffice::IsValidEmail($formData->email)) {
            $this->pageController->addErrorMessage('The email address your entered is not valid.');
            $formData->isValid = false;
        }

        if (empty($formData->subject)) {
            $this->pageController->addErrorMessage('Please enter a subject.');
            $formData->isValid = false;
        }
        if (empty($formData->messageText)) {
            $this->pageController->addErrorMessage('Please enter a message');
            $formData->isValid = false;
        }

        if (!TUser::Authenticated()) {
            $spamCatcher = $request->get('spamCatcher','');
            if (empty($spamCatcher)) {
                $this->pageController->addErrorMessage('Please answer the security question.');
                $formData->isValid = false;
            }
            else if (trim( strtolower($spamCatcher)) != 'george') {
                $this->pageController->addErrorMessage('Your answer to the security question was not correct.');
                $formData->isValid = false;
            }

            if ($formData->isValid &&
                TPostOffice::CheckForSpam(
                    $formData->fromName, $formData->email, $formData->subject, $formData->messageText)) {
                $this->pageController->addErrorMessage('Questionable content entry was detected. If you feel this is in error, please write to Friends Meeting of Austin.');
                $formData->isValid = false;
            }
        }

        return $formData;
    }

}
