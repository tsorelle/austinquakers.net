<?php
/** Class: TAddressDisplayPanel ***************************************/
/// Panel to display on Address form
/**
*****************************************************************/
class TAddressDisplayPanel
{
    public function __construct() {
    }

    public function __toString() {
        return 'TAddressDisplayPanel';
    }

    public static function buildExtraPersonsList($formData) {
        if (isset($formData->person))
            $pid = $formData->person->getId();
        else
            $pid = 0;
        $isEditor= TUser::Authorized('update fma directory');
        $userPersonId = TUser::GetUserPersonId();
        $canAddNewPerson = ($isEditor || ($pid  == $userPersonId));
        $ul = new TBulletList();
        foreach ($formData->otherPersons as $item){
            if ($item->id != $pid)  {
                if ($item->id == $userPersonId)
                    $canAddNewPerson = true;
                $ul->addLine(
                    sprintf('<a href="/directory?cmd=showPerson&pid=%s">%s</a>',
                        $item->id,$item->name));
                $lineCount++;
            }
        };

        if ($isEditor)
            $ul->addLine(
                sprintf('<a href="/directory?cmd=search&rcmd=addAddressPerson&rprm=aid:%d&stype=p&prompt=person+for+address">Add another person to address.</a>',
                        $formData->address->getId()));


        if ($canAddNewPerson)
           $ul->addLine(
                   sprintf('<a href="/directory?cmd=addPerson&aid=%d">Add new person at address.</a>',
                        $formData->address->getId()));

        if (isset($formData->person))
            $title = 'Others at Address';
        else
            $title = 'Persons at Address';
        $result = new TFieldSet('othersAtAddress',$title);
        $result->add($ul);
        return $result;
    }



    public static function Build($address, $canEdit) {
        $fieldSet = TFieldSet::Create('addressDisplayPanel', 'Address');
        $addressText = $address->getAddressName().'<br/>';
        $value = $address->getAddress1();
        if (!empty($value))
            $addressText .= $value.'<br/>';
        $city = $address->getCity();
        $state = $address->getState();
        $zip = $address->getPostalCode();
        $country = $address->getCountry();

        if (!empty($state)) {
            if (!empty($city))
                $city .= ', ';
            $city .= $state.' ';
        }

        if (!empty($zip))
            $city .= $zip;
        if (!empty($country))
            $city .= '<br/>'.$country;

        $addressDiv = new TDiv();
        $addressDiv->add($addressText.$city);
        $fieldSet->add($addressDiv);

        $phone = $address->getPhone();
        if (!empty($phone))
            $fieldSet->addLabeledText('Phone:',$phone);
        $notes = $address->getNotes();

        if ((!empty($notes)) and TUser::Authorized('view fma directory notes'))
           $fieldSet->addLabeledText('Notes:',$notes);
        $recordStamp = new TDiv('recordStamp');
        $recordStamp->add(TRecordStamp::get($address));
        $fieldSet->add($recordStamp);

        $aid = $address->getId();
        $buttonPanel = new TDiv('actionLinks');// new TFieldSet('personEditButtons','','inlineButtons');
        if ($canEdit) {
            $buttonPanel->add('['.TCommandLink::Create('editAddress','Edit',$hint='Edit this address', $params="aid=$aid").']');
            $buttonPanel->add('['.TCommandLink::Create('deleteAddress','Delete',$hint='Delete this address', $params="aid=$aid").']');
            $fieldSet->add($buttonPanel);
        }
        $buttonPanel->add('['.TCommandLink::Create('showMap','Map',$hint='Show this address on a map', $params="aid=$aid").']');


        return $fieldSet;

    }
}
// end TAddressDisplayPanel