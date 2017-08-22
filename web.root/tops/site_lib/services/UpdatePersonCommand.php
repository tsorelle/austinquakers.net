<?php

class UpdatePersonCommand extends TServiceCommand {

    public function IsAuthorized() {
        return TDrupalUser::Authenticated();
    }

    protected function run() {
        $request = $this->GetRequest();
        $this->AddInfoMessage("Original $request->Name");
        $person = new PersonDto();
        $person->id = $request->id;
        $person->Name = $request->Name;
        $person->Gender = $request->Gender;
        $person->Age = $request->Age;
        $person->Status = "Updated";
        $this->SetReturnValue($person);
        $this->AddInfoMessage("Updated ".$person->Name);
    }
} 