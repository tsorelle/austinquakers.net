<?
/** Class: TSubscriptionFormController ***************************************/
/// controller for email subscription form
/**
*****************************************************************/
class TSubscriptionFormController
{
    private static $instance;
    private $pageController;
    private $request;

    public function __construct($pageController) {
        $this->pageController = $pageController;
        $this->request = TRequest::GetInstance();
    }

    public function setFormVariables($formData) {
        $this->pageController->setFormVariable('pid', $formData->pid);
        $this->pageController->setFormVariable('personName',$formData->personName);
        $this->pageController->setFormVariable('aid',$formData->addressId);
    }

    public function getFormData($pid) {
        $result = TSubscriptions::GetRecipientInfo($pid);
        if ($result !== false) {
            $result->subscriptions = TSubscriptions::GetSubscriptions($pid);
            $this->setFormVariables($result);
        }

      // TTracer::ShowArray($result);

        return $result;
    }


    public function getRequestData() {
        $valid = true;
        $result = new StdClass();
        $result->pid = $this->request->get('pid',0);
        if (empty($result->pid))
            exit('Invalid person ID');
        $result->personName =  $this->request->get('personName','');
        $result->email = $this->request->get('email','');
        if (empty($result->email)) {
            $valid = false;
            $this->pageController->addErrorMessage('E-Mail address is required.');
        }
        else if (!TDataValidator::IsValidEmail($result->email)) {
            $valid = false;
            $this->pageController->addErrorMessage('Invalid e-mail address: '.$result->email);
        }
        $result->addressId = $this->request->get('aid',0);
        $result->fnByMail = $this->request->get('fnByMail',0);
        $result->subscriptions = array();
        $subscriptions = TSubscriptions::GetSubscriptions($pid);
        $i = 0;
        foreach($subscriptions as $subscription) {
            $selected = $this->request->get('list'.$subscription->elistId,0);
            $subscription->selected = (!empty($selected));
            $subscription->altEmail = $this->request->get('altEmail'.$subscription->elistId,'');
            if (!empty($subscription->altEmail) && (!TDataValidator::IsValidEmail($subscription->altEmail))) {
                $valid = false;
                $this->pageController->addErrorMessage('Invalid e-mail address: '.$subscription->altEmail);
            }
            $result->subscriptions[$i++] = $subscription;
        }
       $result->isValid = $valid;
       // TTracer::ShowArray($result);
       return $result;

    }


    public function __toString() {
        return 'TSubscriptionFormController';
    }
}
// end TSubscriptionFormController