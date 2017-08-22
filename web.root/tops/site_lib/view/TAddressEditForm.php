<?php

/** Class: TAddressEditForm ***************************************/
/// builder for contact update form
/**
*****************************************************************/
class TAddressEditForm {

    public static function addAddressFields($fieldSet, $address) {
        $addressType = $address->getAddressType();
        if (empty($addressType))
            $addressType = 1;
        $directoryCode = $address->getDirectoryCode();
 TTracer::Assert(empty($addressType),'empty address type','address type is $addressType');
        $fieldSet->addDropDown('addressType',  'Adddress Type:',
                                TFmaLookups::GetAddressTypes(),
                                $addressType,'',0);

        $fieldSet->addInputField('addressName','Name on Address' ,'narrow','wide', 	$address->getAddressName());
        $fieldSet->addInputField('address1','Line 1' ,'narrow','wide', 	$address->getAddress1());
        $fieldSet->addInputField('address2','Line 2' ,'narrow','wide', 	$address->getAddress2());
        $fieldSet->addInputField('city','City' ,'narrow','wide', 		$address->getCity());
        $fieldSet->addInputField('state','State or province' ,'narrow','wide', 		$address->getState());
        $fieldSet->addInputField('postalCode','Postal code' ,'narrow','wide', 	$address->getPostalCode());
        $fieldSet->addInputField('country','Country' ,'narrow','wide', 	$address->getCountry());
        $fieldSet->addInputField('phone','Household phone' ,'narrow','wide', 		$address->getPhone());
        $fieldSet->addTextAreaField('notes'  ,'Notes:'  ,'narrow','wide',$address->getNotes(),4);
        $fieldSet->addCheckBoxField('fnotes', 'Newsletter by mail?', $address->getFnotes());
        $fieldSet->addCheckboxField('directoryCode','Show in directory?',
            empty($directoryCode) ? 1 : $directoryCode);
        $fieldSet->addInputField('sortkey','Sort key' ,'narrow','wide', 	$address->getSortkey());
        return $fieldSet;
    }


    public static function Build($formData, $buttonPanel) {
        TTracer :: Trace('TAddressEditForm::Build');
        $address = $formData->address;

        $addressPanel = TFieldSet::Create("addressEdit", "Address Information");
        self::addAddressFields($addressPanel,$address);
        $wrapper = TDiv::Create('addressEditForm');
        $wrapper->add($addressPanel);
        $wrapper->add($buttonPanel);
        return $wrapper;
    }

}
// end TContactEditForm


