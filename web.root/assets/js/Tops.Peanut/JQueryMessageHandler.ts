/**
 * Created by Terry on 12/17/2015.
 */
///<reference path='../typings/jquery/jquery.d.ts' />
///<reference path='Peanut.d.ts' />
/// <reference path="Peanut.ts" />

module Tops {
    /**
     * based on http://www.jacklmoore.com/notes/jquery-modal-tutorial/
     */
    class PeanutModal {
        public static instance : PeanutModal;
        overlay = jQuery('<div id="overlay"></div>');
        modal = jQuery('<div id="modal"></div>');
        content = jQuery('<div id="modal-content"></div>');
        closeElement = jQuery('<a id="close" href="#">close</a>');
        opened = false;

        public static initialize() {
            PeanutModal.instance = new PeanutModal();
            PeanutModal.instance.init();
            return PeanutModal.instance;
        }

        init() {
            var me = this;
            me.modal.append(me.content);
            me.modal.hide();
            me.overlay.hide();
            me.content.empty();
            jQuery('body').append(me.overlay,me.modal);
        }

        center = () => {
            var me = this;
            var top, left;

            top = Math.max(jQuery(window).height() - me.modal.outerHeight(), 0) / 2;
            left = Math.max(jQuery(window).width() - me.modal.outerWidth(), 0) / 2;
            me.modal.css( {
                    top:top + jQuery(window).scrollTop(),
                    left:left + jQuery(window).scrollLeft()
                }

            );
        };

        open(settings: any) {
            var me = this;

            me.content.empty();
            me.content.append(
                settings.content
            );

            if (settings.loader) {

                var loader = jQuery('<img src="'+settings.loader+'">');
                me.content.append(loader);

            }

            me.modal.css({
                width: settings.width || 'auto',
                height: settings.height || 'auto'
            });

            me.center();

            jQuery(window).bind('resize.modal', me.center);

            me.modal.show();
            me.overlay.show();

        }

        close = () => {
            var me = this;
            me.modal.hide();
            me.overlay.hide();
            me.content.empty();
            jQuery(window).unbind('resize.modal');

        };

        closeClick = (e: any) => {
            var me = this;
            e.preventDefault();
            me.close();
        };

    }


    export class JQueryMessageHandler implements IMessageHandler {
        waitModal : any;
        applicationPath : string;

        initialize(application:Tops.IPeanutClient, successFunction?:()=>void):void {
            var me = this;
            me.waitModal =  PeanutModal.initialize();
            me.applicationPath = application.applicationPath;
            successFunction();
        }

        getInfoMessages(messages:IServiceMessage[]):string[] {
            var result = new Array<string>();

            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                if (message.MessageType == Tops.Peanut.infoMessageType)
                    result[j++] = message.Text;
            }

            return result;
        }


        getNonErrorMessages(messages:IServiceMessage[]):string[] {
            var result = new Array<string>();

            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                if (message.MessageType != Tops.Peanut.errorMessageType)
                    result[j++] = message.Text;
            }

            return result;
        }


        getErrorMessages(messages:IServiceMessage[]):string[] {
            var result = [];
            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                if (message.MessageType == Tops.Peanut.errorMessageType)
                    result[j++] = message.Text;
            }

            return result;
        }


        getErrorMessagesWithBR(messages:IServiceMessage[]):string {
            var me = this;
            var errors = me.getErrorMessages(messages);
            var errorString = "";
            if (errors.length > 0) {
                for (var i = 0; i < errors.length; i++) {
                    errorString = errorString + (i > 0 ? "<br>" : "") + errors[i];
                }
            }
            return errorString;
        }

        getNonErrorMessagesWithBr(messages:IServiceMessage[]):string {
            var me = this;
            var infos = me.getInfoMessages(messages);
            var infoString = "";
            if (infos.length > 0) {
                for (var i = 0; i < infos.length; i++) {
                    infoString = infoString + (i > 0 ? "<br>" : "") + infos[i];
                }
            }
            return infoString;
        }


        getErrorMessagesAsUL(messages:IServiceMessage[]):string {
            var me = this;
            var errors = me.getErrorMessages(messages);
            var errString = me.messagesToUL(errors);
            return errString;
        }

        getInfoMessagesAsUL(messages:IServiceMessage[]):string {
            var me = this;
            var infos = me.getInfoMessages(messages);
            var infoString = me.messagesToUL(infos);
            return infoString;
        }


        messagesToUL(messages:string[]):string {
            if (messages.length > 0) {
                if (messages.length == 1) {
                    return messages[0];
                }
                else {
                    var i = 0;
                    var resultString = "<ul>";
                    while (i < messages.length) {
                        resultString = resultString + "<li>" + messages[i] + "</li>";
                        i++;
                    }
                    resultString = resultString + "</ul>";
                    return resultString;
                }
            }
            return "";
        }

        getMessagesAsUL(messages:IServiceMessage[], errClass:string, infoClass:string, warningClass?:string):string {
            // Use this if all messages in a single block with class types
            var me = this;
            if (!messages)
                return "";
            if (!warningClass)
                warningClass = infoClass;
            var result = "<ul>";
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                var className = errClass;
                if (message.MessageType == Peanut.infoMessageType)
                    className = infoClass;
                else if (message.MessageType == Peanut.warningMessageType)
                    className = warningClass;
                result = result + "<li class='" + className + "'>" + message.Text + "</li>";
            }

            return result + "</ul>";
        }

        getMessagesText(messages:IServiceMessage[]):string[] {
            var result = new Array<string>();
            var j = 0;
            for (var i = 0; i < messages.length; i++) {
                var message = messages[i];
                result[j++] = message.Text;
            }
            return result;
        }


        showServiceMessages(messages: Tops.IServiceMessage[]): void {
            var me = this;
            var errorText = me.getErrorMessagesAsUL(messages);
            var infoText = me.getInfoMessagesAsUL(messages);

            me.showErrorMessage(errorText);
            me.showInfoMessage(infoText);
        }

        hideServiceMessages(): void {
            var me = this;
            me.showErrorMessage(null);
            me.showInfoMessage(null);
        }

        showError(errorMessage: string): void {
            // peanut uses this to display exceptions
            var me = this;

            if (errorMessage)
                me.showErrorMessage(errorMessage);
            else
                me.showMessage(null);
        }

        showMessage(messageText: string): void {
            var me = this;
            me.showInfoMessage(messageText);
        }

        // Application level message display functions
        showErrorMessage(messageText: string): void {
            var me = this;
            me.setMessage(messageText, "error");
        }

        showInfoMessage(messageText: string): void {
            var me = this;
            me.setMessage(messageText, "info");
        }

        setMessage(message: string, prefix: string) {
            if (message) {
                jQuery("#" + prefix + "Text").html(message);
                jQuery("#" + prefix + "Messages").show();
            }
            else {
                jQuery("#" + prefix + "Text").html("");
                jQuery("#" + prefix + "Messages").hide();
            }
        }

        showWaiter(message: string = "Please wait . . .") {
            var me = this;
            // message = message + ' (test 2) ';
            var loaderUrl =  Peanut.JoinUrlPath(me.applicationPath,'assets/img/ajax-loader.gif');
            message = '<span style="padding-bottom: 20px; margin-right:20px; font-size: 16px; font-weight: bold">'+message+'</span> ';
            me.waitModal.open({content: message, height: 60, loader: loaderUrl});
        }


        hideWaiter(timeout: number = 0) {
            var me = this;
            me.waitModal.close();
        }


        /** Implemented for KoMessageHandler but not in this version. **/
        setProgress(count:number):void {
            // not implemented
        }

        showProgress(message:string):void {
            // not implemented
        }
        showWarning(messageText:string):void {
            var me = this;
            me.showMessage(messageText);
        }

        setErrorMessage(messageText:string):void {
            var me = this;
            me.showErrorMessage(messageText);
        }

        setInfoMessage(messageText:string):void {
            var me = this;
            me.showInfoMessage(messageText)
        }

        setWarningMessage(messageText:string):void {
            var me = this;
            me.showWarning(messageText);
        }



    }
}