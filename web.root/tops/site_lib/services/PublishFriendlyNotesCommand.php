<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 6/6/2016
 * Time: 5:39 AM
 */


class PublishFriendlyNotesCommand extends TServiceCommand
{

    protected function run()
    {

        if (!TUser::Authorized('send fma mail')) {
            $this->AddErrorMessage('User not authorized to send messages.');
            return;
        }
        
        if(empty($_FILES)) {
            $this->AddErrorMessage("no files received");
            return;
        }

        $issue = TFriendlyNotesManager::GetIssueInformation($_REQUEST['publicationDate']);
        if ($issue === FALSE) {
            $this->AddErrorMessage('No valid publication date recieved.');
            return;
        }

        $file = $_FILES['file'];
        if ($file["error"] > 0) {
            $this->AddErrorMessage("Upload failed. Return Code: " . $file["error"]);
            return;
        };

        if ($issue->isUploaded) {
            unlink($issue->filePath);
        }

        move_uploaded_file($file["tmp_name"], $issue->filePath);

        // clean out directory
        if ($handle = OPENDIR($issue->uploadDir)) {
            while (FALSE !== ($file = READDIR($handle))) {

                $filePath = $issue->uploadDir."/".$file;
                if ($file != "." && $file != ".." && IS_FILE($filePath) && $filePath != $issue->filePath) {
                    unlink($filePath);
                }
            }
        }


        $this->AddInfoMessage("File uploaded for: $issue->issueDate");
        $this->SetReturnValue($issue);
    }
}