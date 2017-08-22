<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/30/2016
 * Time: 9:39 AM
 */
class UpdateCommitteeTermCommand extends TServiceCommand
{

    /**
     * Request:
     * interface ITermOfService {
     *     personId : any;
     *     committeeId: any;
     *     committeeMemberId: any;
     *     statusId: any;
     *     startOfService: string;
     *     endOfService: string;
     *     dateRelieved: string;
     *     roleId: any;
     *     notes: string;
     *  }
     */
    protected function run()
    {
        $request = $this->GetRequest();
        if ($request == null) {
            $this->AddErrorMessage('No request received.');
            return;
        }

        $term = new TCommitteeMember();

        if ($request->committeeMemberId == 0) {
            $this->assignTerm($term,$request);
            $term->add();
        }
        else {
            $term->select($request->committeeMemberId);
            if ($term->getId() == 0) {
                $this->AddErrorMessage("No committee member term found for id $request->committeeMemberId");
                return;
            }
            if ($request->statusId == 4) {
                // withdrawn? delete
                $term->delete();
            }
            else {
                $this->assignTerm($term, $request);
                $term->update();
            }
        }

        $result = TCommitteeMember::GetAllMembers($request->committeeId);
        $this->SetReturnValue($result);
    }

    private function assignTerm(TCommitteeMember $term, $request)
    {
        $term->setPersonId($request->personId);
        $term->setCommitteeId($request->committeeId);
        $term->setStatus($request->statusId);
        $term->setStartOfService($request->startOfService);
        $term->setEndOfService($request->endOfService);
        $term->setDateRelieved($request->dateRelieved);
        $term->setRoleId($request->roleId);
        $term->setNotes($request->notes);
    }
}