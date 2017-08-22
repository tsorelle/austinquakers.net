///<reference path="../Tops.Peanut/KoMessageHandler.ts"/>
/**
 * Created by Terry on 5/26/14.
 */
///<reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path="../typings/bootstrap/bootstrap.d.ts" />
/// <reference path="../typings/custom/head.load.d.ts" />
/// <reference path="../components/TkoComponentLoader.ts" />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path="../Tops.Peanut/JQueryMessageHandler.ts"/>

module Tops {

    // no message handler clsss
    
    // no waitmessage class
    
    
    // Class
    export class Application implements Tops.IPeanutClient {
        static versionNumber = "1.27";
        
        public setMessageHandler = (handler:Tops.IMessageHandler, successFunction?:()=>void) => {
            var me = this;
            me.messageHandler = handler;
            handler.initialize(me, successFunction);
        };

        private messageHandler : IMessageHandler;

        constructor(currentViewModel: any) {
            var me = this;
            me.viewModel = currentViewModel;
            me.peanut = new Tops.Peanut(me);
            Application.current = me;
        }

        static current: Application;
        applicationPath: string = "/";
        peanut: Tops.Peanut;
        viewModel: any;
        componentLoader: TkoComponentLoader = null;


        // Drupal 7/8
        // See modules/tops/tops.routing.yml and modules/tops/src/controller/TopsController.php
        // serviceUrl: string = "tops/service";

        // Drupal 6 or PHP
        serviceUrl: string = "topsService.php";


        public startup(applicationPath: string, messageHandlerType: string, successFunction?: () => void) {
            var me = this;
            me.setApplicationPath(applicationPath);
            me.serviceUrl = me.applicationPath + me.serviceUrl;
            var handlerScript =  this.applicationPath + 'assets/js/Tops.Peanut/' + messageHandlerType + 'MessageHandler.js'
                + '?tv=' + Application.versionNumber;
            head.load(handlerScript, function() {
                //rebuild
                me.messageHandler = messageHandlerType == 'Ko' ?
                    new KoMessageHandler() :
                    new JQueryMessageHandler();
                me.messageHandler.initialize(me, successFunction);
            });
        }

        public setPageTitle(title: string) {
            // jQuery('title').html(title);
            document.title = title;
        }

        public setPageHeading(text: string) {
            jQuery('h1.page-header').html(text);
            jQuery('h1.page-header').show();
        }
        
        public hidePageHeading() {
            jQuery('h1.page-header').hide();
        }

        // for backward compatibility
        public initialize(applicationPath: string, successFunction?: () => void) {
            var me = this;
            me.startup(applicationPath,'JQuery',successFunction);
        }

        setApplicationPath(path: string): void {
            var me = this;
            if (path) {
                me.applicationPath = "";
                if (path.charAt(0) != "/")
                    me.applicationPath = "/";
                me.applicationPath = me.applicationPath + path;
                if (path.charAt(path.length - 1) != "/") {
                    me.applicationPath = me.applicationPath + "/";
                }
            }
            else {
                me.applicationPath = "/";
            }

            var port = location.port;
            if ((!port) || port == '8080') {
                port = '';
            }
            else {
                port = ':' + port;
            }
            me.applicationPath = location.protocol + '//' + location.hostname + port + me.applicationPath;
        }

        //*********************************************
        //  COMPONENT HANDLING
        //*********************************************
        public getHtmlTemplate(name: string, successFunction: (htmlSource: string) => void) {
            var parts = name.split('-');
            var fileName = parts[0] + parts[1].charAt(0).toUpperCase() + parts[1].substring(1);
            var htmlSource =  this.applicationPath +
                    'assets/templates/' + fileName + '.html'
                    + '?tv=' + Application.versionNumber
                ;
            jQuery.get(htmlSource, successFunction);
        }

        private expandFileName(fileName: string ) {
            if (!fileName) {
                return '';
            }
            var fileExtension = fileName.substr((fileName.lastIndexOf('.') + 1));
            if (fileExtension) {
                switch (fileExtension.toLowerCase()) {
                    case 'css' :
                        return this.applicationPath + 'assets/css/' + fileName
                            + '?tv=' + Application.versionNumber
                            ;
                    case 'js' :
                        return this.applicationPath + 'assets/js/components/' + fileName
                            + '?tv=' + Application.versionNumber
                            ;
                }
            }
            return fileName;

        }
        public loadResources(names: any, successFunction?: () => void) {
            var me = this;
            var params : any = null;
            if (_.isArray(names)) {
                params = [];
                for(var i = 0; i < names.length; i++) {
                    var path = me.expandFileName(names[i]);
                    params.push(path);
                }
            }
            else {
                params = me.expandFileName(names);
            }
            head.load(params, successFunction);
        }

        public loadJS(names: any, successFunction?: () => void) {
            var params: any = null;
            if (_.isArray(names)) {
                params = [];
                for(var i = 0; i < names.length; i++) {
                    params.push(this.applicationPath + 'assets/js/components/' + names[i]
                        + "?tv=" + Application.versionNumber
                    );
                }
            }
            else {
                params = names;
            }
            head.load(params, successFunction);
        }


        public loadCSS(name: string, successFunction?: () => void) {
            head.load(this.applicationPath + 'assets/css/' + name
                + "?tv=" + Application.versionNumber
                , successFunction);
        }

        public loadComponent(name: string, successFunction?: () => void) {
            var me = this;
            if (me.componentLoader) {
                me.componentLoader.load(name, successFunction);
            }
            else
            {
                head.load(this.applicationPath + 'assets/js/components/TkoComponentLoader.js', function() {
                        me.componentLoader = new TkoComponentLoader(me.applicationPath);
                        me.componentLoader.load(name, successFunction);
                    }
                );
            }
        }

        public registerComponent(name: string, vm: any, successFunction?: () => void) {
            var me = this;

            me.getHtmlTemplate(name, function (htmlSource: string) {
                ko.components.register(name, {
                    viewModel: {instance: vm}, // testComponentVm,
                    template: htmlSource
                });
                if (successFunction) {
                    successFunction();
                }
            });
        }


        // *******************************************************
        // Service message handling - called by Peanut - delegated to message handler
        // ******************************************************

        private serviceMessageToString(message: IServiceMessage) {
            switch (message.MessageType) {
                case Peanut.errorMessageType: return 'Error: ' + message.Text;
                case Peanut.warningMessageType: return 'Warning: '+ message.Text;
                default:
                    return 'Message: '+ message.Text;
            }
        }
        public showServiceMessages(messages: Tops.IServiceMessage[]): void {
            var me = this;
            if (messages != null && messages.length > 0) {
                if (me.messageHandler) {
                    me.messageHandler.showServiceMessages(messages);
                }
                else {
                    var message = '';

                    for (var i = 0; i < messages.length; i++) {
                        if (i>0) {
                            message += '; ';
                        }
                        message =  message + me.serviceMessageToString(messages[i]);
                    }
                    alert(message);
                }
            }
        }

        public hideServiceMessages(): void {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.hideServiceMessages();
            }
        }


        public showError(errorMessage: string): void {
            // peanut uses this to display exceptions
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.showError(errorMessage);
            }
            else if (errorMessage) {
                alert('Error: ' + errorMessage);
            }
        }


        //**************
        // More message handling called by view models
        //**************

        public showMessage(messageText: string): void {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.showMessage(messageText);
            }
            else {
                alert(messageText);
            }
        }

        // Application level message display functions
        public showErrorMessage(messageText: string): void {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.setErrorMessage(messageText);
            }
            else {
                alert('Error: ' + messageText);
            }
        }

        public showInfoMessage(messageText: string): void {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.setInfoMessage(messageText);
            }
            else {
                alert(messageText);
            }
        }

        public showWaiter(message: string = "Please wait . . .") {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.showWaiter(message);
            }
        }

        public hideWaiter(timeout: number = 0) {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.hideWaiter();
            }
        }
        showWarning(messageText:string):void {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.showWarning(messageText);
            }
            else {
                alert(messageText);
            }
        }

        showProgress(message:string):void {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.showProgress(message);
            }
        }

        setProgress(count:number):void {
            var me = this;
            if (me.messageHandler) {
                me.messageHandler.setProgress(count);
            }
        }

    }


    //todo: refactor to seperate file
    export class EventInfo {
        public eventId: any = 0;
        public eventType: string = '';
        public title: string = '';
        public description: string = '';
        public when: string = '';
        public repeatInfo: string = '';
        public startDate: string = null;
    }

    export class EventObservable {
        public title = ko.observable('');
        public description = ko.observable('');
        public when = ko.observable('');
        public email = ko.observable('');
        public type = ko.observable('');
        public repeatInfo = ko.observable('');
        public isCurrent = ko.observable(false);

        public assign(event : EventInfo) {
            var me = this;
            me.title(event.title);
            me.description(event.description);
            me.when(event.when);
            me.type(event.eventType == 'task' ? 'Task' : 'Calendar Event');
            me.repeatInfo(event.repeatInfo);
            me.isCurrent(event.startDate ? true : false);
        }
    }

}
