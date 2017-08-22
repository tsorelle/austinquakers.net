<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/26/2016
 * Time: 7:55 AM
 */
class GetCommitteeAndMembersCommand extends TServiceCommand
{

    protected function run()
    {
        $committeeId = $this->GetRequest();
        if (empty($committeeId)) {
            $this->AddErrorMessage("Committee ID not received");
            return;
        }
        $result = new stdClass();
        
        $result->committee = TCommittee::GetCommitteeDTO($committeeId);
        if ($result->committee == null) {
            $this->AddErrorMessage("Committee not found for id $committeeId");
            return;
        }
        $result->members = TCommitteeMember::GetAllMembers($committeeId);
        $this->SetReturnValue($result);
    }
}