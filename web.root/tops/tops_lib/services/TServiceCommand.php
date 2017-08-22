<?php
/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 2/25/14
 * Time: 3:13 PM
 */


abstract class TServiceCommand {
    private  $context;
    private  $request;


    abstract protected function run();

    protected function AddErrorMessage($text) {
        $this->context->AddErrorMessage($text);
    }

    public function AddInfoMessage($text) {
        $this->context->AddInfoMessage($text);
    }

    public function AddWarningMessage($text) {
        $this->context->AddWarningMessage($text);
    }

    public function SetReturnValue($value) {
        $this->context->SetReturnValue($value);
    }

    public function GetRequest() {
        return $this->request;
    }

    public function IsAuthorized() {
        return true;
    }

    public function Execute($request) {
        $this->context = new TServiceContext();
        $this->request = $request;
        if ($this->IsAuthorized())
            $this->run();
        else
            $this->AddErrorMessage("Sorry, you are not authorized to use this service.");
        return $this->context->GetResponse();
    }
}
