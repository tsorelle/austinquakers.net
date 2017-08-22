<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/7/2016
 * Time: 8:56 AM
 */
class SendToEmailListCommand extends TServiceCommand
{

    protected function run()
    {
        if (!TUser::Authorized('send fma mail')) {
            $this->AddErrorMessage('User not authorized to send messages.');
            return;
        }
        
        $request = $this->GetRequest();
        if ($request == null) {
            $this->AddErrorMessage('No request recieved.');
            return;
        }
        
        if (!isset($request->subject)) {
            $this->AddErrorMessage('No message text recieved.');
            return;
        }

        if (!isset($request->messageText)) {
            $this->AddErrorMessage('No message text recieved.');
            return;
        }

        if (!isset($request->listCode)) {
            $this->AddErrorMessage('No list code recieved.');
            return;
        }

        $elist = new TEList();
        $elist->search("listCode = '$request->listCode'");
        $listId = $elist->getId();
        
        if (empty($listId)) {
            $this->AddErrorMessage("Cannot find email list '$request->listCode'");
            return;
        }

        $test = !empty($request->test);

        $distributor = new TMailDistributor($listId);
        if ($request->test) {
            $distributor->sendTestMessage($request->subject, $request->messageText);
            $address = TUser::GetFullEmailAddress();
            $address = str_replace('<',' "',$address);
            $address = str_replace('>','" ',$address);
            $resultMessage = 'Test message sent to '.$address;
            $this->AddInfoMessage($resultMessage);
        }
        else {
            $messageCount = $distributor->sendMail($request->subject, $request->messageText);
            $resultMessage = "Messages were added to the e-mail queue for $messageCount recipients.";
            $this->AddInfoMessage($resultMessage);
        }
        
    }
}