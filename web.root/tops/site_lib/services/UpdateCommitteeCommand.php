<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/27/2016
 * Time: 11:34 AM
 */
class UpdateCommitteeCommand extends TServiceCommand
{

    protected function run()
    {
        $committeeUpdate = $this->GetRequest();
        if ($committeeUpdate == null) {
            $this->AddErrorMessage('No request received');
            return;
        }

        $committeeId = $committeeUpdate->committeeId;
        $committee = new TCommittee();
        if ($committeeId > 0) {
            $committee->select($committeeUpdate->committeeId);
            if ($committee->getId() == 0) {
                $this->AddErrorMessage('No committee found for id '.$committeeUpdate->committeeId);
                return;
            }
        }
        $committee->setName($committeeUpdate->name);
        $committee->setMailbox($committeeUpdate->mailbox);
        $committee->setActive($committeeUpdate->active);
        $committee->setIsStanding($committeeUpdate->isStanding);
        $committee->setIsLiaison($committeeUpdate->isLiaison);
        $committee->setMembershipRequired($committeeUpdate->membershipRequired);
        $committee->setDescription($committeeUpdate->description);
        $committee->setNotes($committeeUpdate->notes);
        if($committeeId == 0) {
            $committee->add();
        }
        else {
            $committee->update();
        }
        $result = $committee->toDTO();
        $this->SetReturnValue($result);
        
    }
}