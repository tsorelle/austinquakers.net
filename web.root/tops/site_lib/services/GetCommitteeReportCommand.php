<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/31/2016
 * Time: 8:21 AM
 */
class GetCommitteeReportCommand extends TServiceCommand
{
    protected function run()
    {
        $report = TCommittee::GetReport();
        $this->SetReturnValue($report);
    }
}