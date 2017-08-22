<?
/** Class: TSendMailFormController ***************************************/
/// Handle send mail form
/**
*****************************************************************/
class TSendMailFormController
{
    private static $instance;
    private $pageController;
    private $request;

    public function __construct($pageController) {
        $this->pageController = $pageController;
        $this->request = TRequest::GetInstance();
    }

    public function validateFormData($formData) {
        if (empty($formData->subject)) {
            $this->pageController->addErrorMessage('Subject is required.');
            $formData->isValid = false;
        }
        if (empty($formData->messageText)) {
            $this->pageController->addErrorMessage('Message text is required.');
            $formData->isValid = false;
        }
    }

    public function getFormData() {
        $formData = new stdclass();
        $formData->isValid = true;
        $lid =  $this->request->get('lid',3);
        $this->pageController->setFormVariable('lid',$lid);
        if ($lid == 0) {
            $this->pageController->addErrorMessage("Error: No list ID");
        }
        else {
            $elist = new TEList();
            if ($elist->select($lid)) {
                $formData->listName = $elist->getListName();
            }
            else {
                $this->pageController->addErrorMessage("List $lid not found.");
                $lid = 0;
            }
        }
        $formData->lid = $lid;
        $formData->isValid = ($lid > 0);
        $formData->subject = $this->request->get('subject','');
        $formData->messageText = $this->request->get('messageText','');
        return $formData;
    }

    public function __toString() {
        return 'TSendMailFormController';
    }
}
// end TSendMailFormController