<?
/** Class: TSubscriptionsForm ***************************************/
///
/**
*****************************************************************/
class TSubscriptionsForm
{
    public function __construct() {
    }

    public static function Build($formData) {
        $wrapper = new TDiv();
         $fieldSet = TFieldSet :: Create('subscriptionEditForm',
            'Subscriptions for '.$formData->personName );
         $fieldSet->addInputField('email', 'E-mail address:'  ,'','',  $formData->email);
         $table = new THtmlTable();
         $table->addColumnTitles("&nbsp,List name,Alternate address (this list only)");
         foreach ($formData->subscriptions as $item) {
             $row = new TTableRow();
             $row->addCell(THtml::CheckBox("list".$item->elistId, $item->selected));
             $row->addCell($item->listName);
             $row->addCell(THtml::TextField('altEmail'.$item->elistId,$item->altEmail));
             $table->addRow($row);
         }
         $fieldSet->add($table);
         $divCk = new TDiv();
         $divCk->add(THtml::CheckBox("fnByMail",$formData->fnByMail));
         $divCk->add("&nbsp;Get Friendly Notes by postal mail");
         $fieldSet->add($divCk);

        $buttonPanel = new TFieldSet('subscriptionFormButtons','','inlineButtons');
        $buttonPanel->add(new TActionButton('update', 'updateSubscriptions','Save' ));
        if ($formData->returnForm == 'user')
            $buttonPanel->add(new TActionButton('cancel', 'showUserMenu' ,'Cancel'));
        else
            $buttonPanel->add(new TActionButton('cancel', 'showPerson' ,'Cancel'));

        $wrapper->add($fieldSet);
        $wrapper->add($buttonPanel);
        return $wrapper;
    }

    public function __toString() {
        return 'TSubscriptionsForm';
    }
}
// end TSubscriptionsForm