<?php
/** Class: TDatePickers ***************************************/
/// Scripting for date pickers
/**
*****************************************************************/
class TDatePickers extends TUIComponent
{
    private $ids;

    public function __construct() {
        $this->ids = array();
    }

    public function add($fieldId) {
        array_push($this->ids,$fieldId);
    }

    public function render() {
        if (sizeof($this->ids) == 0)
            return '';
        $datePickerScript = '$(document).ready(function(){$("#%s").datepicker();});'."\n";
        $result = '<script type="text/javascript" src="/sites/all/modules/jquery_ui/jquery.ui/ui/minified/ui.datepicker.min.js?t"></script>'."\n";
        $result .= '<script type="text/javascript">'."\n";
        foreach($this->ids as $fieldId)
            $result .= sprintf($datePickerScript,$fieldId);
        return $result.'</script>'."\n";
    }
}
