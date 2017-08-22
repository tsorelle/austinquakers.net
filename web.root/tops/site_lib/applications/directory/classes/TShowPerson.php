<?php
/** Class: TShowPerson ***************************************/
/// Show person form
/**
*****************************************************************/
class TShowPerson extends TPageAction
{
    private function getFormData($pid, $aid) {
        if (empty($pid))
            $pid = 0;
        if (empty($aid))
            $aid = 0;

        $result = new stdclass();
        if ($pid > 0) {
             $person = TPerson::GetPerson($pid);
             if ($person->getId() == 0) {
                $this->pageController->addErrorMessage("Person ($pid) not found.");
                return false;
             }
            $result->person = $person;
            $aid = $person->getAddressId();
        }
        if (!empty($aid)) {
            $result->address = TAddress::GetAddress($aid);
            $result->otherPersons = TPerson::getPersonsAtAddress($aid);
        }

        return $result;

    }

    protected function run() {
        TTracer::Trace('TShowPerson.run');
        if ($this->argCount == 0) {
            // $formData = $formController->getFormData();
            $request = TRequest::GetInstance();
            $formData = new stdclass();
            $pid = $request->get('pid',0);
            $aid = $request->get('aid',0);
            $formData = $this->getFormData($pid,$aid);
        }
        else {
            $oldForm = $this->getArg();
            $formData = $this->getFormData($oldForm->pid,$oldForm->aid);
        }


        $canEdit = TUser::Authorized('update fma directory');
        $showPerson = (isset($formData->person));
        if ($showPerson) {
            if (TUser::IsCurrentUser($formData->person->getUserName()))
                $canEdit = true;
            $minPersonsToShow = 1;
        }
        else
            $minPersonsToShow = 0;

        $showAddress = (isset($formData->address));

       // TTracer::ShowArray($formData);
        $form = new TDiv("personDisplayForm");
        if ($showPerson) {
            $personPanel = TPersonDisplayPanel::Build($formData->person,$canEdit);
        }
        if ($showAddress) {
            $addressPanel = TAddressDisplayPanel::Build($formData->address,$canEdit);
            $personsList = TAddressDisplayPanel::buildExtraPersonsList($formData);

        }

TTracer::Assert($showPerson,"showPerson");
TTracer::Assert($showAddress,"showAddress");



        if ($showPerson && $showAddress) {
            $columns = TMultiColumn::CreateTwoColumn();
            $columns->addLeft($personPanel);
            $columns->addRight($addressPanel);
            //if ($showOthers)
              $columns->addRight($personsList);
            $form->add($columns);
        }
        else if ($showPerson || $showAddress) {
            if ($showPerson) {
                $form->add($personPanel);
            }
            else {
                $form->add($addressPanel);
                $form->add($personsList);
            }
        }
        else
            TTracer::Trace("ERROR: No person or address to show.");

        $this->pageController->addMainContent($form);
    }

}
