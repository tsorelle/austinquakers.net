<?php
/** Class: TSearchForm ***************************************/
/// builds contact search form
/**
*****************************************************************/
class TDirectorySearchForm
{

    private static function BuildSearchPanel($formData, $showAddressField) {
        TTracer::Trace('BuildTopPanel');
        $searchPanel = new TFieldSet('searchCriteria','Search Values');

        $searchPanel->addInputField('firstName' ,'First name:' ,'mediumWidth','wide', isset($formData) ? $formData->firstName : '');
        $searchPanel->addInputField('lastName' ,'Last name:' ,'mediumWidth','wide', isset($formData) ? $formData->lastName : '');
        if ($showAddressField)
            $searchPanel->addInputField('addressName' ,'Name on address:' ,'mediumWidth','wide', isset($formData) ? $formData->addressName : '');
        return $searchPanel;
    }

    private static function BuildButtonPanel($showButtons) {
        TTracer::Trace('buildButtonPanel');
        $buttonPanel = new TFieldSet('searchButtons','','inlineButtons');
        $buttonPanel->add(new TActionButton('search','searchPersons','Search' ));
        if ($showButtons && TUser::Authorized('update fma directory')) {
            $buttonPanel->add(new TActionButton('addPerson','addPerson','Add Person' ));
            $buttonPanel->add(new TActionButton('addAddress','addAddress','Add Address' ));
        }
        // $buttonPanel->add(new TActionButton('showall', 'showAll' ,'Get Everyone'));
        return $buttonPanel;
    }

    private static function BuildSearchOptionPanel($formData) {
        TTracer::Trace('buildSearchOptionPanel');
        $selected = $formData == null ? 'any' : $formData->searchOption;
        $result = new TFieldSet('searchOptions','');
        $result->add(new TRadioButtonDiv('searchOpt', 'any', 'Partial match anywhere', '', $selected == 'any'));
        $result->add(new TRadioButtonDiv('searchOpt', 'start', 'Partial match at beginning', '', $selected == 'start'));
        $result->add(new TRadioButtonDiv('searchOpt', 'end', 'Partial match at end', '', $selected == 'end'));
        $result->add(new TRadioButtonDiv('searchOpt', 'exact', 'Exact match', '', $selected == 'exact'));

        return $result;
    }

    public static function Build($returnValues, $formData=null) {
        TTracer::Trace('TDirectorySearchForm::Build');

        if ($returnValues->prompt) {
            $promptPanel = TDiv::Create('promptPanel');
            $promptPanel->add('<h3>Search '.$returnValues->prompt.'</h3>');
        }
        $searchPanel = TDirectorySearchForm::BuildSearchPanel($formData,$returnValues->showControls);
        $searchOptionPanel = TDirectorySearchForm::BuildSearchOptionPanel($formData);
        // $resultOptionPanel = TDirectorySearchForm::BuildResultOptionPanel($formData);

        $buttonPanel = TDirectorySearchForm::BuildButtonPanel($returnValues->showControls);

        $optionsPanel = TDiv::Create('optionsPanel');
        $optionsPanel->add($searchOptionPanel);
        // $optionsPanel->add($resultOptionPanel);

        $formPanel = TDiv::Create('searchForm');
        $formPanel->add($searchPanel);
        $formPanel->add($optionsPanel);

        $div = TDiv::Create('personSearchForm');
        if ($promptPanel)
            $div->add($promptPanel);
        $div->add($formPanel);
        $div->add($buttonPanel);
        $pg = THtml::HiddenField('pg',$formData->pageNumber);
        $cmdField = THtml::HiddenField('cmd','searchPersons');
        $div->add($pg );
        $div->add($cmdField);
        return $div;
    }

    public static function BuildPager($searchResult) {
        $pages = ceil($searchResult->totalItems / $searchResult->itemsPerPage);
        $pgCount = THtml::HiddenField('pageCount',$pages);

        if ($pages == 1)
            return '';
        $result = TDiv::Create('pager');
        $result->add($pgCount);

        $startPage =  $searchResult->pageNumber - 2;
        if ($startPage < 1)
            $startPage = 1;
        $lastPage = $startPage + 8;
        if ($lastPage > $pages)
            $lastPage = $pages;

        if ($pages > 5 && $startPage > 1)
            $result->add('<input type="submit" name="firstPageButton" value=" First " />' );

        if ($searchResult->pageNumber > 1) {
            $result->add('<input type="submit" name="prevButton" value=" << " />' );
        }

        for($i=$startPage; $i <= $lastPage; $i++)
           $result->add('<input type="submit" name="pageButton" value="'.$i.'" />' );

        if ($searchResult->pageNumber < $pages) {
            $result->add('<input type="submit" name="nextButton" value=" >> " />' );
        }
        if ($pages > 5 && $lastPage < $pages)
            $result->add('<input type="submit" name="lastPageButton" value=" Last " />' );


        $result->add("&nbsp;&nbsp;&nbsp;Page $searchResult->pageNumber of $pages&nbsp;&nbsp;&nbsp;");

        return $result;

    }

    public static function ShowResultList($returnValues, $searchResult) {
        TTracer::ShowArray($returnValues);
        $list = $searchResult->list;
        $div = TDiv::Create("searchResults");

        $ulLeft = new TBulletList();
        $ulRight = new TBulletList();
        $left = true;

        if (empty($returnValues->params))
            $returnParams = '';
        else {
            // To avoid query string confusion return parameters use
            // plus for ampersand and colon for equal sign
            $returnParams = '&'.str_replace('+','&',$returnValues->params);
            $returnParams = str_replace(':','=',$returnParams);
        }

        foreach($list as $item) {

            $result = '';
            $personId = $item->getPersonId();
            $addressId = $item->getAddressId();
            if (!empty($personId)) {
                $name = $item->getDisplayName();
                $result = sprintf('<a href="/directory?cmd=%s&pid=%s%s">%s</a>',
                    $returnValues->cmd,
                    $personId,
                    $returnParams,
                    $name);
                if (!empty($addressId)) {
                   // $result .= sprintf('; <a href="/directory?cmd=showAddress&aid=%s">Address</a>',$addressId);
                }
            }
            else {
                    $name = $item->getAddressName();
                $result = sprintf('<a href="/directory?cmd=showAddress&aid=%s">%s</a>',$addressId,$name);
            }
            if ($left)
                $ulLeft->addLine($result);
            else
                $ulRight->addLine($result);
            $left = !$left;
        }

        $columns = TMultiColumn::CreateTwoColumn();
        $columns->addLeft($ulLeft);
        $columns->addRight($ulRight);

        $pager =  self::BuildPager($searchResult);
        $fieldSet = TFieldSet::Create('searchResultSet','Results');
        $fieldSet->add($columns);
        $fieldSet->add($pager);

        return $fieldSet;
    }


}
// end TDirectorySearchForm



