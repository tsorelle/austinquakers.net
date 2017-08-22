<?php
/** Class: TApplication ***************************************/
/// Base class for an application script
/**
* An application script is a controller used to drive the logic
* of an application.  In TOPS, an application is a set of related
* forms and functions to perform a set of related tasks.
*****************************************************************/
abstract class TApplication extends TPageAction
{
    protected $pageController;
    protected $contentPath;
    protected $classPath;
    protected $commonContentPath;
    protected $request;


    public function __construct()  {
    }

    protected function setApplicationName($appName) {
        $this->actionId = $appName;
        $SITE_LIB=TClassLib::GetSiteLib();
        $this->contentPath = "$SITE_LIB/applications/$appName/content";
        $this->commonContentPath = "$SITE_LIB/content";
        $this->classPath = "$SITE_LIB/applications/$appName/classes";
    }


    protected function addSharedContent($fileName) {
        $this->pageController->addMainContentFile(
            $this->commonContentPath.$fileName);

    }
    protected function addContentFile($fileName) {
        $this->pageController->addMainContentFile("$this->contentPath/$fileName");
    }

    protected function getContentFile($fileName,$default='') {
        $result = @file_get_contents("$this->contentPath/$fileName");
        if ($result === false)
            return $default;
        return $result;
    }

    public function setCssPath($path) {
        $this->pageController->setCssPath($path);
    }


    public function addStyleSheet($pageName) {
        $this->pageController->addStyleSheet($pageName);
    }

    public function setFormVariable($name, $value) {
        $this->pageController->setFormVariable($name, $value);
    }

    public function addErrorMessage($text) {
        TTracer::Trace('addErrorMessage');
        $this->pageController->addErrorMessage($text);
    }

    public function addErrorMessages($errors) {
        TTracer::Trace('addErrorMessages');
        foreach($errors as $message)
            $this->pageController->addErrorMessage($message);
        return (sizeof($errors) > 0);
    }

    protected function setPropertyFromRequest($name) {
        $value = TRequest::GetValue($name);
        $this->pageController->setProperty($name, $value);
        return $value;
    }

    public function getPostBackUrl($params=null) {
        return $this->pageController->getPostBackUrl($params);
    }

    /// Begin execution of the application's main routine
    public function execute() {
        $this->request = TRequest::GetInstance();
        if (func_num_args() > 0) {
            $defaultCommand = func_get_arg(0);
            $cmd = $this->request->getCommand($defaultCommand);
        }
        else
            $cmd = $this->request->getCommand();
        $this->run($cmd);
        return $this->pageController;
    }

    /// Executes an sub-routine implemented as a TPageAction object.
    /**
    * The TPageAction object is instatiated on the basis on
    * the $actionName parameter. This class definition resides
    * in the classPath subdirectory of the application. Typically
    * the application script resides in a directory named for the
    * application and its page actions reside in a subdirectory
    * named 'classes'.
    *
    * Any number of additional arguments may be passed in the function call.
    */
    public function executeAction($actionName) {
        $className = 'T'.ucfirst($actionName);
        $argCount = func_num_args();
        if ($argCount > 1) {
            $argCount--;
            $args = func_get_args();
            array_shift($args);
            // TTracer::ShowArray($args);
        }
        else {
            $argCount = 0;
            $args = array();
        }
        require_once("$this->classPath/$className.php");
        eval('$action = new '.$className.'($this,$argCount,$args);');
        return $action->execute();
    }

}
// end TApplication



