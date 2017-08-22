<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/25/2016
 * Time: 6:13 AM
 */
class GetCommitteeListCommand extends TServiceCommand
{
    private function addCommittee(array &$list, $name, $value, $active)
    {
        $name = trim(utf8_encode($name));
        if (!empty($name) && !empty($value)) {
            $item = TNameValuePair::Create($name, $value);
            $item->active = !empty($active);
            array_push($list, $item);
        }
    }

    protected function run()
    {
        if (!TDrupalUser::Authenticated()) {
            $this->AddErrorMessage('Please log in to view this content.');
            $this->SetReturnValue(array());
        }

        $list = array();
        $name = '';
        $value = '';
        $active = 1;
        $sql = "SELECT name AS 'Name', committeeId AS 'Value', active FROM committees ORDER BY `name`";
        $statement = TSqlStatement::ExecuteQuery($sql);
        $statement->instance->bind_result($name,$value,$active);
        while ($statement->next()) {
            $this->addCommittee($list, $name, $value, $active);
        }
        $result = new stdClass();
        $result->list = $list;
        $result->canEdit = TDrupalUser::Authorized('update fma committee directory');
        $this->SetReturnValue($result);
    }
}