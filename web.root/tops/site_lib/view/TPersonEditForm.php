<?php

/** Class: TContactEditForm ***************************************/
/// builder for contact update form
/**
*****************************************************************/
class TPersonEditForm {

    public static function addPersonalInfoFields($fieldSet, $person) {
        $datePickerScript = new TDatePickers();
        $datePickerScript->add('dateOfBirth');
        $datePickerScript->add('deceased');
        $fieldSet->add($datePickerScript);
       	$fieldSet->addInputField('firstName' ,'First name:' ,'narrow','wide', $person->getFirstName());
       	$fieldSet->addInputField('middleName','Middle name:','narrow','wide', $person->getMiddleName());
       	$fieldSet->addInputField('lastName'  ,'Last name:'  ,'narrow','wide', $person->getLastName());
       	$fieldSet->addInputField('email'     ,'E-mail:'     ,'narrow','wide',  $person->getEmail());
       	$fieldSet->addInputField('phone'     ,'Phone:'     ,'narrow','wide',  $person->getPhone());
        $fieldSet->addInputField('workPhone',         'Work phone:',      'narrow','wide', $person->getWorkPhone());
        $fieldSet->addDropDown('membershipStatus',  'Membership:',
                                TFmaLookups::GetMemberStatuses(),
                                $person->getMembershipStatus(),'',0);
        $fieldSet->addCheckBoxField('junior', 'Junior:', $person->getJunior());
        $fieldSet->addInputField('dateOfBirth',       'Date of Birth:',   'narrow','wide', $person->getDateOfBirth());

        $fieldSet->addInputField('deceased',          'Deceased:',        'narrow','wide', $person->getDeceased());
        $fieldSet->addDropDown('directoryCode','Directory code',
                                TFmaLookups::GetDirectoryCodes(),
                                $person->getDirectoryCode(),'',0);
        $fieldSet->addInputField('otherAffiliation',  'Other Affiliation:','narrow','wide', $person->getOtherAffiliation());
        //$fieldSet->addInputField('residenceLocation', 'ResidenceLocation', 'narrow','wide',$person->getResidenceLocation());
        $fieldSet->addTextAreaField('notes'  ,'Notes:'  ,'narrow','wide',$person->getNotes(),4);
        $fieldSet->addInputField('sortkey',           'Sort key:',        'narrow','wide', $person->getSortkey());
        return $fieldSet;
    }


    public static function Build($formData, $buttonPanel) {
        TTracer :: Trace('TPersonEditForm::Build');
        $person = $formData->person;
        $isSiteAdmin = TUser::Authorized('update fma directory');
        $isNew = ($person->getId() < 1);
        $username = $person->getUserName();
        $webSitePanel = TFieldSet::Create("siteInfo", "Web site account",'leftLabels');
        if ($isNew || empty($username))
            $webSitePanel->addInputField('username', 'Web site username:'  ,'narrow','wide',  $person->getUsername());
        else  {
            $webSitePanel->addText('Web site username: <strong>'.$username.'</strong>');
            $webSitePanel->add(THtml::HiddenField('username',$username));
        }
        $password = '';
        //if  (TUser::IsSiteAdmin())
        //    $password = $person->getPassword();

        $webSitePanel->addInputField('identifier', 'New password:'  ,'narrow','wide', $password );
        if ($isSiteAdmin)  {
            $webSitePanel->addCheckBoxDiv('newAccount','New account?',
                'Check this to create a new web site account for the user name above.');
        }

        $subscriptionsPanel = TFieldSet::Create("subscriptions","Subscriptions", 'leftLabels');
        

        $contactInfoPanel = TFieldSet::Create("personEdit", "Person Information");
        self::addPersonalInfoFields($contactInfoPanel, $person);
        $wrapper = TDiv::Create('personEditForm');
        $wrapper->add($webSitePanel);

        $wrapper->add($contactInfoPanel);
        $wrapper->add($buttonPanel);
        return $wrapper;
    }

}
// end TContactEditForm


