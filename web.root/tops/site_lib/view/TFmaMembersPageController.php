<?
// require_once "tops_lib/ui/TSimpleMenu.php";
// require_once "tops_lib/ui/TBasePageController.php";

 /*****************************************************************

                                9/9/2007 7:57AM
 *****************************************************************/
 class TFmaMembersPageController extends TDrupalPageController
 {
    private $headerSubtitle = '';
    private $returnPath = '';

    public function __construct() {
        parent::__construct();
        // page template not used under Drupal?
//        $this->setPageTemplate('membersTemplate');
    }

    public function getHeaderSubtitle() {
        return $this->headerSubtitle;
    }

    public function setHeaderSubtitle($value) {
        $this->headerSubtitle = $value;
    }
 }  // finish class


