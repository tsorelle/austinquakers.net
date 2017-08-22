<?php
class TDirectorySearch extends TPageAction
{

    private function getSearchRequest() {
        TTracer::Trace('getSearchRequest');
                $request = TRequest::GetInstance();

        $formData = new stdClass();

        $formData->addressName = $request->get('addressName','');
        $formData->lastName = $request->get('lastName','');
        $formData->firstName = $request->get('firstName');

        $formData->searchOption = $request->get('searchOpt','any');
        $currentPage = $request->get('pg',1);

        $nextPage = $request->get('pageButton',0);

        if ($nextPage == 0 && $request->includes("firstPageButton")) {
            $nextPage = 1;
        }
        if ($nextPage == 0 && $request->includes("lastPageButton")) {
            $nextPage = $request->get('pageCount',0);
        }
        if ($nextPage > 0)
            $currentPage = $nextPage;
        else if ($request->includes("nextButton"))
            $currentPage++;
        else if ($request->includes("prevButton"))
            $currentPage--;

        $formData->pageNumber = $currentPage;
        $formData->isValid = true;
        //TTracer::Trace("Returned   $memberName || $lastName  || $firstName|| $expiredSince || $expiresBy");


        if (empty($formData->addressName) &&
            empty($formData->lastName) &&
            empty($formData->firstName))  {
            $this->pageController->addErrorMessage('Please enter a search value.');
            $formData->isValid = false;
        }

        return $formData;

    }

    private function getReturnValues() {
        $request = TRequest::GetInstance();
        $result = new stdClass();

        $searchCmd = $request->get('rcmd',false);
        $searchParameters = $request->get('rprm',false);
        $searchType = $request->get('stype',false);
        $searchPrompt = $request->get('prompt',false);

        if ($searchCmd === false)
            $result->cmd = 'showPerson';
         else {
            $result->cmd = $searchCmd;
            $this->pageController->setFormVariable('rcmd', $result->cmd);
         }

         if ($searchParameters === false)
            $result->params = '';
         else {
            $this->pageController->setFormVariable('rprm', $searchParameters);
            $result->params = $searchParameters;
         }

         if ($searchType === false)
            $result->searchType = '';
         else {
            $result->searchType = $searchType;
            $this->pageController->setFormVariable('stype', $searchType);
         }
         if ($searchPrompt === false)
            $result->prompt = '';
         else {
            $this->pageController->setFormVariable('prompt', $searchPrompt);
            $result->prompt = $searchPrompt;
         }
        $result->showControls = ($result->searchType != 'p');
        return $result;
    }

    private function showSearchForm($returnValues,$formData = null) {
        TTracer::Trace('application.showSearchForm');
        $this->pageController->addLocalCssImport('/fma/style/pages', 'searchForm');
        $this->pageController->setPageTitle('Person Search');
        // prevent page timeout on back-button
        $this->pageController->useGetMethod();
        $formContent =TDirectorySearchForm::Build($returnValues,$formData);
        $this->pageController->addMainContent($formContent);
    }

    private function showSearchResult($returnValues,$searchResult) {
        // $this->pageController->setPageTitle('Search Results');
        TTracer::Trace('showSearchResult');
        $count = $searchResult->totalItems;
        if ($count == 0)  {
            $this->pageController->addInfoMessage('No results found.');
            return;
        }
        $this->pageController->addInfoMessage($count.' found.');
        $div = TDirectorySearchForm::ShowResultList($returnValues,$searchResult);
        $this->pageController->addMainContent($div);
    }



    protected function run() {

        $isPostBack = $this->argCount > 0 && $this->getArg() == "searchPersons";
        $returnValues = $this->getReturnValues();

        if ($isPostBack) {
            $formData = $this->getSearchRequest();
            $this->showSearchForm($returnValues, $formData);
            if ($formData->isValid) {
                $searchResult = TDirectorySearchList::Search($formData->firstName, $formData->lastName,
                            $formData->addressName, $formData->searchOption,
                            $formData->pageNumber);
                $this->showSearchResult($returnValues, $searchResult);
            }
        }
        else {
            $this->showSearchForm($returnValues);
        }
    }
}
// TViewPerson



