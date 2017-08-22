<?php
/** Class: TMembershipForm ***************************************/
/// Builds form for membership update
/**
*****************************************************************/
class TMembershipForm
{
   private static function GetCategoryButtons($formData) {
        $items = TMembershipCategories::Get($formData->categories);
        $controls = new TControlSet();
        $controls->addRadioButton('membershipType', 1, 'Individual', '',
            ($formData->membershipType == 1));
        $controls->addRadioButton('membershipType', 2, 'Quaker organization', '',
            ($formData->membershipType == 2));
        $controls->addRadioButton('membershipType', 3, 'Other organization', '',
            ($formData->membershipType == 3));
        $controls->addCheckBoxList($items);
        return $controls;
    }

    public static function Build($templateName,$formData,$submitButtons) {
        TTracer::Trace('Build');
        $template = TDrupalSnippet::Get($templateName);
        $template->setValue('memberCategories',TMembershipForm::GetCategoryButtons($formData));
        $template->setValue('memberName',$formData->memberName);
        $template->setValue('website', $formData->website);
        $template->setValue('buttons',$submitButtons);
        return $template;
    }
}
// end TMembershipForm



