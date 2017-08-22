<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/7/2016
 * Time: 6:13 AM
 */
class GetFriendlyNotesMessageCommand extends TServiceCommand
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
/*        if (!isset($request->publicationDate)) {
            $this->AddErrorMessage('No publication date recieved.');
            return;
        }
*/
        $issue = TFriendlyNotesManager::GetIssueInformation($request);
        if ($issue === FALSE) {
            $this->AddErrorMessage('No valid publication date recieved.');
            return;
        }

        if (!$issue->isUploaded) {
            $this->AddErrorMessage("No file has been uploaded for ".$issue->issueDate);
            return;
        }
        $this->SetReturnValue($issue);

    }
}