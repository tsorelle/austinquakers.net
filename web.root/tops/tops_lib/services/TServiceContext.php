<?php

/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 2/22/14
 * Time: 9:57 AM
 */

// namespace tops\services;

/*
        static allMessagesType: number = -1;
        static infoMessageType: number = 0;
        static errorMessageType: number = 1;
        static warningMessageType: number = 2;

        static serviceResultSuccess: number = 0;
        static serviceResultPending: number = 1;
        static serviceResultErrors: number = 2;
        static serviceResultWarnings: number = 3;
        static serviceResultServiceFailure: number = 4;
        static serviceResultServiceNotAvailable: number = 5;
*/

class MessageType {
    const Info = 0;
    const Error = 1;
    const Warning = 2;
}

class ResultType {
    const Success = 0;
    const Pending = 1;
    const Warnings = 2;
    const Errors = 3;
    const ServiceFailure = 3;
    const ServiceNotAvailable = 4;
}

class TServiceContext {
    private  $response;

    public function __construct() {
        $this->response = new TServiceResponse();
        $this->response->Messages = array();
        $this->response->Result = ResultType::Success;
    }
    public function AddMessage($messageType,$text) {
        $message = new TServiceMessage();
        $message->MessageType = $messageType;
        $message->Text = $text;
        array_push($this->response->Messages, $message);
    }

    public function AddInfoMessage($text) {
        $this->AddMessage(MessageType::Info,$text);
    }

    public function AddWarningMessage($text) {
        $this->AddMessage(MessageType::Error,$text);
        if ($this->response->Result < ResultType::Warnings)
            $this->response->Result = ResultType::Warnings;
    }


    public function AddErrorMessage($text) {
        $this->AddMessage(MessageType::Error,$text);
        if ($this->response->Result < ResultType::Errors)
            $this->response->Result = ResultType::Errors;
    }

    public function AddServiceFatalErrorMessage($text) {
        $this->AddMessage(MessageType::Error,$text);
        $this->response->Result = ResultType::ServiceFailure;
    }

    public function ServiceNotAvailable() {
        $this->AddMessage(MessageType::Error,'The service is not available.');
        $this->response->Result = ResultType::ServiceNotAvailable;
    }

    public function GetResponse() {
        return $this->response;
    }

    public function SetReturnValue($value) {
        $this->response->Value = $value;
    }
} 