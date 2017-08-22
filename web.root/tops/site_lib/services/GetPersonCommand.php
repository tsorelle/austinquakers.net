<?php
/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 2/25/14
 * Time: 3:32 PM
 */

// namespace tops\services;

class GetPersonCommand extends TServiceCommand {

    protected  function run() {
        $person = new PersonDto();
        $person->id = $this->GetRequest();
        $person->Name = "Terry SoRelle";
        $person->Gender = "Male";
        $person->Age = 66;
        $person->Status = "Active";
        $this->AddInfoMessage("Found ".$person->Name);
        $this->SetReturnValue($person);
    }

} 