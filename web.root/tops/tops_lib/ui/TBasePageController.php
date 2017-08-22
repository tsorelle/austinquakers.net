<?php
/*****************************************************************
Base class for a page controller intended to handle full page.
 Scripts store content and page directives in a controller.
 Page templates use them to get context
information and render the content.
                                9/9/2007 6:20AM
*****************************************************************/
class TBasePageController extends TAbstractPageController
{
    private $infoMessages;
    private $errorMessages;
    private $messageSeparator = '<br/>';
    private $pageTitle = 'untitled page';
    protected $subNavigationControl;

    public function __construct() {}

// *** Abstact function implementations
    protected function getDefaultFormAction() {
        return $_SERVER['SCRIPT_NAME'];
    }

    public function addInfoMessage($value) {
        if (!isset($this->infoMessages))
            $this->infoMessages  = TDiv::Create('infoMessages');
        $this->infoMessages->add($value);
    }
    public function addErrorMessage($value) {
        if (!isset($this->errorMessages))
            $this->errorMessages = TDiv::Create('errorMessages');// new TContainer();

        if (is_string($value))
            $value .= $this->messageSeparator;
        $this->errorMessages->add($value);
    }

   public function setPageTitle($value) {
        $this->pageTitle = $value;
    }

    public function getPageTitle() {
        return $this->pageTitle;
    }


    public function addCssLinks($media, $hrefList) {
        if (!isset($this->styleSheets))
            $this->styleSheet = new TStyleSheetList();
        $argCount = func_num_args();
        $args = func_get_args();
        array_shift($args);
        foreach($args as $href)
            $this->styleSheets->addLink($href,$media);
    }

    public function addCssImports($media, $hrefList) {
        if (!isset($this->styleSheetImports))
            $this->styleSheetImports = new TStyleSheetImportList();
        $argCount = func_num_args();
        $args = func_get_args();
        array_shift($args);
        foreach($args as $href)
            $this->styleSheetImports->add($media,$href);
    }




// ***


    public function renderSubNavigation() {
        return isset($this->subNavigationControl) ?
            $this->subNavigationControl : '';
    }

    public function renderErrorMessages() {
        return isset($this->errorMessages) ?
            $this->errorMessages->render()  : '';
    }

    public function renderInfoMessages() {
        return isset($this->infoMessages) ?
            $this->infoMessages->render()  : '';
    }


    public function setErrorMessageSeparator($value) {
        $this->messageSeparator = $value;
    }


    public function setSubNavigation($control) {
        $this->subNavigationControl = $control;
    }

}   // finish class TBasePageController

