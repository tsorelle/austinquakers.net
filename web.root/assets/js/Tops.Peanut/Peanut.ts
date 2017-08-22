/**
 * Created by Terry on 5/26/14.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
///<reference path='Peanut.d.ts' />
///<reference path='Debugging.ts' />
module Tops {
    export class KeyValueDTO implements Tops.INameValuePair {
        public Name: string;
        public Value: string;
    }

    /**
     * Constants for scym entities editState
     */
    export class editState {
        public static unchanged : number = 0;
        public static created : number = 1;
        public static updated : number = 2;
        public static deleted : number = 3;
    }


    export class Dates {
        public static getCurrentDateString(format: string = null) {
            var d = new Date();
            if (format == 'isodate') {
                var s = d.toISOString();
                s = s.substring(0,10);
            }
            else if (format == 'usdate') {
                return Dates.toUsDate(d);
            }
            return d.toDateString();
        }

        public static toIsoDate(d : Date) {
            var s = d.toISOString();
            return s.substring(0,10);
        }

        public static toUsDate(d : Date) {
            var s = d.getMonth() + '/' + d.getDate() + '/' + d.getFullYear();
            return s;
        }

        public static formatDateString(s : string,format: string = 'default') {
            if (!s) {
                return '';
            }

            var d = new Date(s);

            switch (format) {
                case 'isodate' :
                    return Dates.toIsoDate(d);
                case 'usdate' :
                    return Dates.toUsDate(d);
            }

            return d.toDateString();
        }
    }


    export interface ITest {
        testing: string;
    }

    export class ILookupListItem {
        public value : any;
        public text : string;
        public title : string;
    }

    /**
     * Use for testing. Normally IServiceResponse is returned from a service
     */
    export class fakeServiceResponse implements IServiceResponse {
        constructor(returnValue: any) {
            var me=this;
            me.Value = returnValue;
            me.Data = returnValue;
        }

        Messages: IServiceMessage[] = [];
        Result: number = 0;
        Value: any;
        Data: any;
    }

    export class HttpRequestVars {
        private static instance : HttpRequestVars;
        private requestVars = [];

        constructor() {
            var me = this;
            var href = window.location.href;
            var params = href.slice(href.indexOf('?') + 1).split('&');
            for (var i = 0; i < params.length;i++) {
                var parts = params[i].split('=');
                var key = parts[0];
                me.requestVars.push(key);
                me.requestVars[key] = parts[1];
            }
        }

        public getValue(key: string) {
            var me = this;
            var value = me.requestVars[key];
            if (value) {
                return value;
            }
            return null;
        }

        public static Get(key : string, defaultValue : any = null) {
            if (!HttpRequestVars.instance) {
                HttpRequestVars.instance = new HttpRequestVars();
            }
            var result = HttpRequestVars.instance.getValue(key);
            return (result === null) ? defaultValue : result;
        }
    }

    export class Peanut {

        public static debugging() {
            if (Tops.Debugging) {
                return Tops.Debugging.isEnabled();
            }
            return false;
        }

        constructor(public clientApp:IPeanutClient) {
        }
        private foo:any;
        private serviceType:string = 'php';

        static allMessagesType:number = -1;
        static infoMessageType:number = 0;
        static errorMessageType:number = 1;
        static warningMessageType:number = 2;

        static serviceResultSuccess:number = 0;
        static serviceResultPending:number = 1;
        static serviceResultWarnings:number = 2;
        static serviceResultErrors:number = 3;
        static serviceResultServiceFailure:number = 4;
        static serviceResultServiceNotAvailable:number = 5;

        parseErrorResult(result:any):string {
            var me = this;
            var errorDetailLevel = 4; // verbosity control to be implemented later
            var responseText = "An unexpected system error occurred.";
            try {
                // WCF returns a big whopping HTML page.  Could add code later to parse it but for now, just status info.
                if (result.status) {
                    if (result.status == '404')
                        return responseText + " The web service was not found.";
                    else {
                        responseText = responseText + " Status: " + result.status;
                        if (result.statusText)
                            responseText = responseText + " " + result.statusText
                    }
                }
            }
            catch (ex) {
                responseText = responseText + " Error handling failed: " + ex.toString;
            }
            return responseText;

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
            var _peanut = this;
            var result = new Array<string>()

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


        hideServiceMessages():void {
            var _peanut = this;
            if (_peanut.clientApp.viewModel) {
                var viewModel:any = _peanut.clientApp.viewModel;
                if (typeof (viewModel.hideServiceMessages) !== undefined && viewModel.hideServiceMessages != null) {
                    viewModel.hideServiceMessages();
                    return;
                }
            }

            _peanut.clientApp.hideServiceMessages();
        }

        showServiceMessages(serviceResponse:IServiceResponse):void {
            var _peanut = this;
            if (serviceResponse == null || serviceResponse.Messages == null || serviceResponse.Messages.length == 0)
                return;

            // var vm = _peanut.getCurrentViewModel();
            if (_peanut.clientApp.viewModel) {
                var viewModel:any = _peanut.clientApp.viewModel;

                if (typeof (viewModel.showServiceMessages) !== undefined && viewModel.showServiceMessages != null) {
                    viewModel.showServiceMessages(serviceResponse.Messages);
                    return;
                }
            }

            _peanut.clientApp.showServiceMessages(serviceResponse.Messages);
        }

        handleServiceResponse(serviceResponse:IServiceResponse):boolean {
            var _peanut = this;
            _peanut.showServiceMessages(serviceResponse);
            return true;
        }

        showExceptionMessage(errorResult:any):string {
            var _peanut = this;
            var errorMessage = _peanut.parseErrorResult(errorResult);
            _peanut.clientApp.showError(errorMessage);
            return errorMessage;
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

        postServiceForm(serviceName: string, formData: FormData, successFunction?: (serviceResponse: IServiceResponse) => void,
                errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
            var _peanut = this;

            formData.append('serviceCode',serviceName);
            var serviceUrl =  _peanut.clientApp.serviceUrl; // Drupal 8/7: tops/service, Drupal 6 or PHP: 'topsService.php';

            var result =
                jQuery.ajax({
                    url: serviceUrl,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                }).done(
                    function(serviceResponse) {
                        _peanut.showServiceMessages(serviceResponse);
                        if (successFunction) {
                            successFunction(serviceResponse);
                        }
                    }
                ).fail(
                        function(jqXHR, textStatus ) {
                            var errorMessage = _peanut.showExceptionMessage(jqXHR);
                            if (errorFunction)
                                errorFunction(errorMessage);
                        });


            return result;

        }


        executeRPC(requestMethod: string, serviceName: string, parameters: any = "",
                   successFunction?: (serviceResponse: IServiceResponse) => void,
                   errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
            var _peanut = this;

            // peanut controller requires parameter as a string.
            if (!parameters)
                parameters = "";
            else  {
                parameters = JSON.stringify(parameters);
            }

            var serviceRequest = {"serviceCode": serviceName, "request": parameters};
            // for future implementaiton of security token
            // var serviceRequest = { "serviceCode" : serviceName, "topsSecurityToken": _peanut.securityToken,  "request" : parameters};

            var serviceUrl =  _peanut.clientApp.serviceUrl; // Drupal 8/7: tops/service, Drupal 6 or PHP: 'topsService.php';

            var result =
                jQuery.ajax({
                        type: requestMethod, // "POST",
                        data: serviceRequest,
                        dataType: "json",
                        cache: false,
                        url: serviceUrl
                    })
                    .done(
                        function(serviceResponse) {
                            _peanut.showServiceMessages(serviceResponse);
                            if (successFunction) {
                                successFunction(serviceResponse);
                            }
                        }
                    )
                    .fail(
                        function(jqXHR, textStatus ) {
                            var errorMessage = _peanut.showExceptionMessage(jqXHR);
                            if (errorFunction)
                                errorFunction(errorMessage);
                        });


            return result;
        }


        // Execute a peanut service and handle Service Response.
        executeService(serviceName: string, parameters: any = "",
                       successFunction?: (serviceResponse: IServiceResponse) => void,
                       errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
            var _peanut = this;
            return _peanut.executeRPC("POST", serviceName, parameters, successFunction, errorFunction);
        }

        /*
         *  After implementation of security tokens use this version of get
         // GET is no longer supported. This method is for backward compatibility but is identical to execute service
         getFromService(serviceName: string, parameters: any = "",
         successFunction?: (serviceResponse: IServiceResponse) => void,
         errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
         var _peanut = this;
         return _peanut.executeRPC("POST", serviceName, parameters, successFunction, errorFunction);
         }

         *
         */

        getFromService(serviceName: string, parameters: any = "",
                               successFunction?: (serviceResponse: IServiceResponse) => void,
                               errorFunction?: (errorMessage: string) => void) : JQueryPromise<any> {
                    var _peanut = this;

                    // peanut controller requires parameter as a string.
                    if (!parameters)
                        parameters = "";

                    var serviceResponse: IServiceResponse;
                    var serviceParameters = {"request" :  parameters};
                    var serviceRequest = { "serviceCode" : serviceName, "request" : parameters};
                    var serviceUrl =  _peanut.clientApp.serviceUrl; // 'topsService.php';

                    var result =
                        $.ajax({
                            type: "GET",
                            data: serviceRequest,
                            dataType: "json",
                            cache: false,
                            url: serviceUrl
                        })
                            .done(
                            function(serviceResponse) {
                                _peanut.showServiceMessages(serviceResponse);
                                if (successFunction) {
                                    successFunction(serviceResponse);
    }
                            }
                        )
                            .fail(
                            function(jqXHR, textStatus ) {
                                var errorMessage = _peanut.showExceptionMessage(jqXHR);

                                if (errorFunction)
                                    errorFunction(errorMessage);
                            });

                    return result;
                }

        /*
         * Utility routines
         */

        getRequestParam(name){
            if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
                return decodeURIComponent(name[1]);
            return null;
        }

        public static ValidateEmail(email: string) {
            if (!email || email.trim() == '') {
                return false;
            }
            return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
        }

        public static JoinUrlPath(base: string, relative: string) {
            if (base.slice(-1) === '/') {
                return base + relative; 
            }
            return base + '/' + relative;
        }
    }

    /**
     * based on http://www.jacklmoore.com/notes/jquery-modal-tutorial/
     */
    export class PeanutModal {
        public static instance : PeanutModal;
        overlay = $('<div id="overlay"></div>');
        modal = $('<div id="modal"></div>');
        content = $('<div id="modal-content"></div>');
        closeElement = $('<a id="close" href="#">close</a>');
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

            top = Math.max($(window).height() - me.modal.outerHeight(), 0) / 2;
            left = Math.max($(window).width() - me.modal.outerWidth(), 0) / 2;
            me.modal.css( {
                    top:top + $(window).scrollTop(),
                    left:left + $(window).scrollLeft()
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

                var loader = $('<img src="'+settings.loader+'">');
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

}
