<?php

/** Class: TPersonDisplayPanel ***************************************/
/// Panel to display on person form
/**
*****************************************************************/
class TPersonDisplayPanel {

    public function __construct() {
    }

    public function __toString() {
        return 'TPersonDisplayPanel';
    }

    public static function Build($person,$canEdit = false) {
        TTracer :: Trace("TPersonDisplayPanel::Build()");
        // TTracer::ShowArray($person);
        $name = $person->getFullName();
        $fieldSet = TFieldSet :: Create('personDisplayPanel', $name);
        $deceased = $person->getDeceased();
        if ($deceased)
            $fieldSet->addLabeledText('Deceased:', $person->getDeceased());
        else {
            $emailAddress = $person->getEmail();
            if (!empty ($emailAddress)) {
                $email = sprintf('<a href="mailto:%s <%s>">%s</a>', $name, $emailAddress, $emailAddress);
                $fieldSet->addLabeledText('E-mail:', $email);
            }
            $phone = $person->getPhone();
            if (!empty ($phone))
                $fieldSet->addLabeledText('Personal phone:', $phone);
            $phone = $person->getWorkPhone();
            if (!empty ($phone))
                $fieldSet->addLabeledText('Work phone:', $phone);
        }
        $fieldSet->addLabeledText('Membership:', $person->getMemberStatusText());

        $dob = $person->getDateOfBirth();
        if (!empty($dob))
            $fieldSet->addLabeledText('Date of birth:',$dob);
        $affiliation = $person->getOtherAffiliation();
        if (!empty($affiliation))
            $fieldSet->addLabeledText('Other affiliation:',$affiliation);
        $notes = $person->getNotes();
        if ((!empty($notes)) and TUser::Authorized('view fma directory notes'))
            $fieldSet->addLabeledText('Notes:', $notes);

        $recordStamp = new TDiv('recordStamp');
        $recordStamp->add(TRecordStamp::get($person));
        $fieldSet->add($recordStamp);

        if ($canEdit) {
            $pid = $person->getId();
            $buttonPanel = new TDiv('actionLinks');// new TFieldSet('personEditButtons','','inlineButtons');
            $buttonPanel->add('['.TCommandLink::Create('editPerson','Edit',$hint='Edit person', $params="pid=$pid").']');
            $buttonPanel->add(' ['.TCommandLink::Create('movePerson','Move',$hint='Move person to another address', $params="pid=$pid").']');
            $buttonPanel->add(' ['.TCommandLink::Create('deletePerson','Delete',$hint='Delete person', $params="pid=$pid").']');
            $buttonPanel->add(' ['.TCommandLink::Create('showSubscriptionForm','Subscriptions',$hint='Update Subscriptions', $params="pid=$pid").']');
            $uid = TDrupalData::GetUidForUserName($person->getUserName());
            if (!empty($uid))
                $buttonPanel->add(sprintf('[<a title="Update web site account." href="/?q=user/%d/edit">Account</a>]',$uid));

            //new TActionButton('update','updatePerson','Update' ));
//         $buttonPanel->add(new TActionButton('showall', 'showAll' ,'Get Everyone'));
           $fieldSet->add($buttonPanel);

        }
        return $fieldSet;
    }

}
// end TPersonDisplayPanel
