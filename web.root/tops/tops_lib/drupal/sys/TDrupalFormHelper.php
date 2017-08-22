<?php
/** Class: DrupalFormHelper ***************************************/
/// routines for form validation
/**
*****************************************************************/
class TDrupalFormHelper
{
    private $allowLinks;

    public function __construct() {
        $allowLinks = false;
    }

    public function __toString() {
        return 'DrupalFormHelper';
    }

    private function validateValue($value) {
        if (!$allowLinks && strstr($value,'http:')) {
            form_set_error('', t('Sorry, web links are not allowed in this form.'));
            return false;
        }
        return true;

    }

    public static function ValidateNoLinks($form_state) {
        $allowLinks = false;
        return self::Validate($form_state);
    }

    public static function Validate($form_state) {
        //TTracer::Trace('start check');
        // TTracer::ShowArray($form_state);
        foreach($form_state['values']['submitted'] as $value) {
            if (!self::validateValue($value))
                return false;
        }
        return true;
        // TTracer::Trace('end check');
    }

}
// end DrupalFormHelper
