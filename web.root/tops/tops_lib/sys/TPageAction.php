<?php
/*****************************************************************
Class:  TPageAction
Description:
*****************************************************************/
class TPageAction extends TAction
{

    protected $owner;
    protected $pageController;

    public function __construct($owner,$argCount=0,$args=null) {
        $this->owner = $owner;
        $this->pageController = $owner->pageController;
        $this->setArgs($argCount,$args);
    }


    protected function setCssPath($path) {
        $this->owner->setCssPath($path);
    }

    protected function addSharedContent($fileName) {
        $this->owner->addSharedContent($fileName);
    }

    protected function addContentFile($fileName) {
        $this->owner->addContentFile($fileName);
    }

    public function addStyleSheet($pageName) {
        $this->owner->addStyleSheet($pageName);
    }

    protected function setPropertyFromRequest($name) {
        return $this->owner->setPropertyFromRequest($name);
    }

    public function addErrorMessage($text) {
        $this->owner->addErrorMessage($text);
    }


    protected function run() {
        TTracer::Trace("Page action '$this->actionId' for '$this->owner->actionId' not implemented.");
    }

    public function execute() {
        $result = $this->run();
        return $result;
    }


}
// end TPageAction



