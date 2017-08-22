/**
 * Created by Terry on 12/17/2015.
 */
///<reference path='Peanut.d.ts' />
/// <reference path="Peanut.ts" />

module Tops {

    class waitMessage {
        private static waitDialog : any = null;
        private static waiterType : string = 'spin-waiter';
        private static templates = Array<string>();

        public static addTemplate(templateName: string, content: string) {
            waitMessage.templates[templateName] = content;
        }


        public static setWaiterType(waiterType: string) {
            waitMessage.waiterType = waiterType;
            waitMessage.waitDialog = jQuery(waitMessage.templates[waiterType]);
            return waitMessage.waitDialog;
        }

        public static show(message: string = 'Please wait ...', waiterType : string = 'spin-waiter') {
            var div = waitMessage.setWaiterType(waiterType);
            var span =  div.find('#wait-message');
            span.text(message);
            div.modal();
        }

        public static setMessage(message: string) {
            if (waitMessage.waitDialog) {
                var span = waitMessage.waitDialog.find('#wait-message');
                span.text(message);
            }
        }

        public static setProgress(count: number, showLabel: boolean = false) {
            if (waitMessage.waiterType == 'progress-waiter') {
                var bar = waitMessage.waitDialog.find('#wait-progress-bar');
                var percent = count + '%';
                bar.css('width', percent);
                if (showLabel) {
                    bar.text(percent);
                }
            }
        }

        public static hide() {
            if (waitMessage.waitDialog) {
                waitMessage.waitDialog.modal('hide');
            }
        }
    }

    class messageManager implements IMessageManager {
        static instance:messageManager;

        static errorClass: string = "service-message-error";
        static infoClass: string = "service-message-information";
        static warningClass: string = "service-message-warning";

        public errorMessages = ko.observableArray([]);
        public infoMessages = ko.observableArray([]);
        public warningMessages = ko.observableArray([]);


        public addMessage = (message:string, messageType:number):void => {
            switch (messageType) {
                case Tops.Peanut.errorMessageType :
                    this.errorMessages.push({type: messageManager.errorClass, text: message});
                    break;
                case Tops.Peanut.warningMessageType:
                    this.warningMessages.push({type: messageManager.warningClass, text: message});
                    break;
                default :
                    this.infoMessages.push({type: messageManager.infoClass, text: message});
                    break;
            }
        };

        public setMessage = (message:string, messageType:number):void => {

            switch (messageType) {
                case Tops.Peanut.errorMessageType :
                    this.errorMessages([{type: messageManager.errorClass, text: message}]);
                    break;
                case Tops.Peanut.warningMessageType:
                    this.warningMessages([{type: messageManager.warningClass, text: message}]);
                    break;
                default :
                    this.infoMessages([{type: messageManager.infoClass, text: message}]);
                    break;
            }
        };

        public clearMessages = (messageType:number = Tops.Peanut.allMessagesType):void => {
            if (messageType == Tops.Peanut.errorMessageType || messageType == Tops.Peanut.allMessagesType) {
                this.errorMessages([]);
            }
            if (messageType == Tops.Peanut.warningMessageType || messageType == Tops.Peanut.allMessagesType) {
                this.warningMessages([]);
            }
            if (messageType == Tops.Peanut.infoMessageType || messageType == Tops.Peanut.allMessagesType) {
                this.infoMessages([]);
            }
        };

        public clearInfoMessages = () : void => {
            this.infoMessages([]);
        };

        public clearErrorMessages = () : void => {
            this.errorMessages([]);
        };
        public clearWarningMessages = () : void => {
            this.warningMessages([]);
        };

        public setServiceMessages = (messages:Tops.IServiceMessage[]):void => {
            var count = messages.length;
            var errorArray = [];
            var warningArray = [];
            var infoArray = [];
            for (var i = 0; i < count; i++) {
                var message = messages[i];
                switch (message.MessageType) {
                    case Tops.Peanut.errorMessageType :
                        errorArray.push({type: messageManager.errorClass, text: message.Text});
                        break;
                    case Tops.Peanut.warningMessageType:
                        warningArray.push({type: messageManager.warningClass, text: message.Text});
                        break;
                    default :
                        infoArray.push({type: messageManager.infoClass, text: message.Text});
                        break;
                }
            }
            this.errorMessages(errorArray);
            this.warningMessages(warningArray);
            this.infoMessages(infoArray);
        };
    }

    export class KoMessageHandler implements IMessageHandler {
        initialize(application:Tops.IPeanutClient, successFunction?:()=>void): void {
            var me = this;
            messageManager.instance = new messageManager();
            application.registerComponent('messages-component', messageManager.instance, function () {
                me.loadWaitMessageTemplate(application,'spin-waiter', function () {
                    me.loadWaitMessageTemplate(application,'progress-waiter', function () {
                        if (successFunction) {
                            successFunction();
                        }
                    })
                });
            });
        }


        private loadWaitMessageTemplate(application: IPeanutClient, templateName: string, successFunction: () => void) {
            application.getHtmlTemplate(templateName, function (htmlSource: string) {
                waitMessage.addTemplate(templateName, htmlSource);
                successFunction();
            });
        }

        showServiceMessages(messages: Tops.IServiceMessage[]): void {
            messageManager.instance.setServiceMessages(messages);
        }

        hideServiceMessages(): void {
            messageManager.instance.clearMessages();
        }

        showError(errorMessage: string): void {
            // peanut uses this to display exceptions
            if (errorMessage) {
                messageManager.instance.addMessage(errorMessage,Peanut.errorMessageType);
            }
            else {
                messageManager.instance.clearMessages(Peanut.errorMessageType);
            }
        }

        showMessage(messageText: string): void {
            if (messageText) {
                messageManager.instance.addMessage(messageText,Peanut.infoMessageType);
            }
            else {
                messageManager.instance.clearMessages(Peanut.infoMessageType);
            }
        }

        showWarning(messageText: string): void {
            if (messageText) {
                messageManager.instance.addMessage(messageText,Peanut.warningMessageType);
            }
            else {
                messageManager.instance.clearMessages(Peanut.warningMessageType);
            }
        }

        // Application level message display functions
        setErrorMessage(messageText: string): void {
            if (messageText) {
                messageManager.instance.setMessage(messageText,Peanut.errorMessageType);
            }
            else {
                messageManager.instance.clearMessages(Peanut.errorMessageType);
            }
        }

        setInfoMessage(messageText: string): void {
            if (messageText) {
                messageManager.instance.setMessage(messageText,Peanut.infoMessageType);
            }
            else {
                messageManager.instance.clearMessages(Peanut.infoMessageType);
            }
        }

        setWarningMessage(messageText: string): void {
            if (messageText) {
                messageManager.instance.setMessage(messageText,Peanut.warningMessageType);
            }
            else {
                messageManager.instance.clearMessages(Peanut.infoMessageType);
            }
        }


        public showWaiter(message: string = "Please wait . . .") {
            waitMessage.show(message);
        }

        public hideWaiter() {
            waitMessage.hide();
        }

        public showProgress(message: string = "Please wait . . .") {
            waitMessage.show(message, 'progress-waiter');
        }

        public setProgress(count: number) {
            waitMessage.setProgress(count);
        }


    }
}

