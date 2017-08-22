<?php
/** Class: TDrupalPageController ***************************************/
/// Page Controller for Drupal TOPS Module
/*******************************************************************/
class TDrupalPageController extends TAbstractPageController
{
    private static $instance;
    private static $publicPages;
    private static $drupalPath;
    private $baseUrl;


    public static function IsPagePublic() {
        if (drupal_is_front_page() || arg(0) == 'user')
            return true;

        $q = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
        if ($q == 'hgrequest') {
            return true;
        }

        // requires flag module, use another method if not installed.
        $flag = flag_get_flag('public');
        if ($flag) {
            $nid = TDrupalPage::GetPageNodeId();
            return ($flag->is_flagged($nid));
        }


        /*
        $pages = self::GetPublicPages();
        $path = self::GetDrupalPath();
        $result = (array_search($path,$pages) !== false);
        */

       //TTracer::ShowArray($pages);
        // TTracer::Trace("Page path = $path");
        // TTracer::Trace("PageSearchResult= ($result) ".($result ? 'true' :'false'));

        return false;
    }



    private static function GetPublicPages() {
        if (!isset(self::$publicPages)) {
            $custom = TConfiguration::GetPublicPages();
            self::$publicPages = TConfiguration::GetPublicPages();
        }

        return self::$publicPages;
    }

    public static function GetDrupalPath() {
        if (!isset(self::$drupalPath))
            self::$drupalPath = strtolower($_GET['q']);
        return self::$drupalPath;
    }

    public function __construct() {
        $this->baseUrl = $_SERVER['SCRIPT_NAME'].'?q='.$_GET['q'];
    }

    /**
    *  Prevents page time out on back button.
    *  Set the form method to 'get' and put the
    *  drupal path parameter in a hidden field.
    */
    public function useGetMethod() {
        $this->setFormMethod('get');
        $this->setFormVariable('q',$_GET['q']);
    }

// *** Abstact function implementations
    protected function getDefaultFormAction() {
        return $this->baseUrl;
    }

    public function addInfoMessage($value) {
        drupal_set_message($value);
    }
    public function addErrorMessage($value) {
        drupal_set_message($value,'error');
    }

    public function setPageTitle($value) {
        drupal_set_title($value);
    }

    public function getPageTitle() {
        return drupal_get_title();
    }

    public function addCssLinks($media='all', $hrefList) {
        // Can't get this to work under Drupal, maybe later
        $argCount = func_num_args();
        $args = func_get_args();
        array_shift($args);
        foreach($args as $href) {
          drupal_add_css($href,NULL,$media); // , $preprocess = TRUE)
        }
    }

// ***

    public function addBreadCrumb($text,$href,$hint=NULL) {
        // drupal version is static
        TNavigator::AppendBreadCrumb($text,$href,$hint);
    }

     public function addBreadCrumbCommand($text,$cmd,$hint=NULL) {
         $href = $this->getPostBackUrl(empty($cmd) ? '' : 'cmd='.$cmd);
         $this->addBreadCrumb($text,$href,$hint);
     }

    public function getPostBackUrl($params=NULL) {
        if (empty($params))
            return $this->baseUrl;
        return $this->baseUrl.'&'.$params;
    }

    public static function PrintCssImports() {
        if (isset(TDrupalPageController::$instance)) {
            $result = TDrupalPageController::$instance->renderCssBlocks();
            print $result;
        }
    }

    public static function PrintScriptImports() {
        if (isset(TDrupalPageController::$instance)) {
            TDrupalPageController::$instance->printScriptReferences();
        }
    }


    public function redirectErrorMessage($message,$path='') {
        drupal_set_message($message,'error');
        $this->redirectLocal($path);
    }

    public function redirectInfoMessage($message,$path) {
        drupal_set_message($message);
        $this->redirectLocal($path);
    }
    /// Redirect to drupal path
    public function redirectLocal($path='') {
        if ($_SERVER["SCRIPT_NAME"] == "/index.php") {
            header(sprintf('Location:http://%s/?q=%s',$_SERVER["HTTP_HOST"],$path));
            exit;
        }
        TTracer::Trace('No redirect, script = '.$_SERVER["SCRIPT_NAME"]);
    }

    /*
    public static function PrintScriptBlock() {
        if (isset(TDrupalPageController::$instance)) {
            print TDrupalPageController::$instance->renderCs();
        }
    }
    */

    public static function SetInstance($instance) {
        TDrupalPageController::$instance = $instance;
    }

    public static function GetInstance() {
        TTracer::Trace('GetInstance');
        if (!isset(self::$instance)) {
          self::$instance = TClassFactory::Create(
            'pagecontroller','TDrupalPageController','tops_lib/drupal/sys');
        }
        return self::$instance;
    }
}
// TDrupalPageController

