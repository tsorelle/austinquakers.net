<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/24/2016
 * Time: 8:59 AM
 */
class FindPersonsCommand extends TServiceCommand
{

    /**
     * @param $value
     * @return mixed
     */
    private function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }


    protected function run()
    {
        $list = array();
        $found = array();
        $request = $this->GetRequest();

        if (!empty($request)) {
            $searchValues = explode(' ',$request );
            $sql = "SELECT  personId AS 'Value', FormatName(firstName,middleName,lastName) AS 'Name' FROM persons p ".
                   "WHERE firstName LIKE ? OR lastName LIKE ? OR middleName LIKE ?";
           // $i = 0; // for debugging
            foreach ($searchValues as $searchValue) {
                $searchValue = '%'.$searchValue.'%';
                $name = '';
                $value = '';
                $statement = TSqlStatement::ExecuteQuery($sql, 'sss', $searchValue,$searchValue,$searchValue);
                $statement->instance->bind_result($value,$name);
                while ($statement->next()) {
                    /* // for debugging
                    if ($i++ > 100) {
                        $this->SetReturnValue($list);
                        return;
                    }
                    */

                    $duplicate = array_search($value,$found);
                    if ($duplicate === false) {
                        $name = trim(utf8_encode($name));
                        if (!empty($name) && !empty($value)) {
                            TNameValuePair::Add($list, $name, $value);
                        }
                        array_push($found,$value);
                    }
                }
            }

        }
        $this->SetReturnValue($list);

    }
}