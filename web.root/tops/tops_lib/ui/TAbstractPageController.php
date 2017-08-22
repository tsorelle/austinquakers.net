<?php
/** Class: TAbstractPageController *****************************/
/// Abstract Base class for a page controller
/**
A page controller provides the rendering engine for full or partial pages
 (e.g. Drupal).

Application scripts (see TApplication) store content and page
directives in a controller. Page templates use them to get context
information and render the content.

Page template files are reponsible for using TPageController methods
to retrieve and display content.
*****************************************************************/
abstract class TAbstractPageController
{
    private $textOnly;
    private $browser;
    private $deviceType;
    private $mainContent;
    private $scriptBlock;
    private $formAction;
    private $formMethod = 'post';
    private $formName;
    private $properties = array();
    private $formVariables = array();
    private $pageTemplate;
    protected $styleSheets;
    protected $styleSheetImports;
    private $scriptReferences = array();
    protected $cssPath;


    public function __construct() {}

    /// Add an information message to display
    abstract public function addInfoMessage($value);

    /// Add an error message to display
    abstract public function addErrorMessage($value);

    /// Return post-back URL for form tag.
    abstract protected function getDefaultFormAction();

    /// Add style sheet link tags
    abstract public function addCssLinks($media, $hrefList);


    public function addScriptReference($src) {
        array_push($this->scriptReferences, $src);
    }

    public function printScriptReferences() {
        foreach ($this->scriptReferences as $src)
            print '<script src="'.$src.'" type="text/javascript"></script>'."\n";
    }

    /// Add stylesheet imports. Ignored by old browsers
    public function addCssImports($media, $hrefList) {

        if (!isset($this->styleSheetImports))
            $this->styleSheetImports = new TStyleSheetImportList();
        $argCount = func_num_args();
        $args = func_get_args();
        array_shift($args);
        foreach($args as $href)        {
            $this->styleSheetImports->add($media,$href);
        }
    }

    public function setCssPath($path) {
        $this->cssPath = $path;
    }

    public function addStyleSheet($pageName) {
        $this->addCssImports('screen',"$this->cssPath/$pageName.css");
    }


    /// Set a property to be used by the page template
    public function setProperty($name,$value) {
        $this->properties[$name] = $value;
    }

    /// Get a property to be used by the page template
    public function getProperty($name) {
        if (isset($this->properties[$name]))
            return $this->properties[$name];
        return '';
    }


    public function setTextOnly($value = true) {
        $this->textOnly = $value;
    }

    /// Get browser information
    public function getBrowser() {
        if (!isset($this->browser)) {
            require_once 'tops_lib/sys/TBrowser.php';
            $this->browser = new TBrowser();
        }
        $this->deviceType = $this->browser->getDeviceType();
        if (!isset($this->textOnly))
            $this->textOnly = ($this->deviceType == UNSUPPORTED_BROWSER || $this->deviceType == SMALL_DEVICE);
        return $this->browser;
    }


    /// Return device information from browser object
    public function getDeviceType() {
        if (!isset($this->deviceType))
            $this->getBrowser();
        return $this->deviceType;
    }

    public function getTextOnly() {
        if (!isset($this->textOnly))
            $this->getBrowser();
        return $this->textOnly;
    }

    /// Render warning for unsupported devices
    public function renderBrowserWarning() {
        if ($this->getDeviceType() == UNSUPPORTED_BROWSER)
            return '<p><b>Note: </b>This page will look a lot better if you upgrade your browser to the most recent version of '.
                'Microsoft Internet Explorer, FireFox or Safari.</p>';
        return '';
    }

    /// Add content to main section
    /**
    *  Item added should be a string, or a renderable component.
    *  See TUIComponent.  If additional strings and components
    *  were added to this component, the entire set will be
    *  rendered recursively.
    */
    public function addMainContent($value) {
        if (!isset($this->mainContent))
            $this->mainContent   = new TContentCollection();
        $this->mainContent->add($value);
    }


    /// Add a path to an include file to be inserted in the main section.
    public function addMainContentFile($path) {
        if (!TFilePath::Exists($path))
            throw new Exception("Include file not found: $path");
        if (!isset($this->mainContent))
            $this->mainContent   = new TContentCollection();
        $this->mainContent->addFile($path);
    }


    /// Set browser page title
    public abstract function setPageTitle($value);
    public abstract function getPageTitle();

    /// Add code for a script to be inserted in the script block section.
    public function addScript($script) {
        if (!isset($this->scriptBlock))
            $this->scriptBlock = new TScriptBlock();
       $this->scriptBlock->add($script);
    }

    /// Render the script block
    public function renderScriptBlock() {
        return (isset($this->scriptBlock)) ?
            $this->scriptBlock->render() : '';
    }

    /// Remove form tags
    public function noForm() {
        unset($this->formMethod);
    }

    /// Indicate form method GET/POST the default is 'get'
    public function setFormMethod($method) {
        $this->formMethod = $method;
    }

    /// Give the form a name
    public function setFormName($name) {
        $this->formName = $name;
    }

    /// Render begin tag for form.
    /**
    * By default all content is inclosed in form tags.  The
    * form, by default posts back to the current page
    */
    public function renderBeginForm() {
        if (!isset($this->formMethod))
            return '';
        if (!isset($this->formAction))
            $this->formAction = $this->getDefaultFormAction();

        $result = "\n".'<form class="tops" '.
            (isset($this->formName) ? " name=\"$this->formName\"" : '').
            (isset($this->formAction) ? " action=\"$this->formAction\"" : '').
            " method=\"$this->formMethod\">\n";
        foreach($this->formVariables as $key =>$value)
            $result .= sprintf('<input type="hidden" name="%s" value="%s" />'."\n", $key, $value);
        return $result;
    }

    /// Close the form tag
    public function renderEndForm() {
        return isset($this->formMethod) ? "\n</form>\n" : '' ;
    }

    /// Return content array or empty array
    public function getMainContent() {
        return isset($this->mainContent) ? $this->mainContent->getContent() : array();
    }

    /// Set the action attribute of the form
    public function setFormAction($value) {
        $this->formAction = $value;
    }

    /// get name of page template
    public function getPageTemplate() {
      return $this->pageTemplate;
    }

    /// set name of page template
    public function setPageTemplate($value) {
      $this->pageTemplate = $value;
    }

    /// Return full path to page template file
    public function getPageTemplatePath() {
        return TClassLib::GetSiteFile("content/$this->pageTemplate.php");
    }

    /// render css import statements
    public function renderCssImports($media='screen') {
        return isset($this->styleSheetImports) ?
            $this->styleSheetImports->renderImports($media) : '';
    }

    /// render inline css
    public function renderCssBlocks() {
        $result = isset($this->styleSheetImports) ?
            $this->styleSheetImports->renderImportBlocks() : '';
        return $result;
    }


    public function addLocalCssImport($cssPath=null, $pageId=null) {
        TTracer::Trace("Adding css: $cssPath");
        if (isset($cssPath)) {
            $fileName = isset($pageId) ?
                $pageId.'.css' :
                str_ireplace('.php','.css',basename($_SERVER['SCRIPT_NAME']));
            $cssPath .= '/'.$fileName;
            TTracer::Trace("cssPath = $cssPath");

            $filePath = TFilePath::Expand($cssPath);
            if ($filePath === false)  {
                TTracer::Trace('Bad file path for css');
                return;
            }

        }
        else {
            $cssPath = str_ireplace('.php','.css',$_SERVER['SCRIPT_NAME']);
            $filePath = $_SERVER['DOCUMENT_ROOT']."/$cssPath";
        }
        TTracer::Trace("Adding css: $filePath");
        if (TFilePath::Exists($filePath))
            $this->addCssImports('screen',$cssPath);
        else
            TTracer::Trace("style sheet '$filePath' not found.");
    }

    /// Add a variable to be rendered as a hidden field
    public function setFormVariable($key, $value) {
        $this->formVariables[$key] = $value;
    }

    /// Retrieve value of a form variable
    public function getFormVariable($key,$default=null) {
        if (!isset($this->formVariables[$key])) {
            if (isset($default))
                return $default;
            throw new Exception("Form variable '$key' not assigned.");
        }
        return $this->formVariables[$key];
    }


}   // finish class TBasePageController

