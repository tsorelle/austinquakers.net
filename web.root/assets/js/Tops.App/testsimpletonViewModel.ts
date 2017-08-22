/**
 * Created by Terry on 12/18/2015.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />


// Module
module Tops {
    export class testsimpletonViewModel {
        static instance: Tops.testsimpletonViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // variables

        // observables
        // canEdit = ko.observable(false);
        testMessage = ko.observable('Hello world');
        // Constructor
        constructor() {
            var me = this;

            Tops.testsimpletonViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }



        // Methods
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            me.application.startup(applicationPath,'Ko',
                // me.application.initialize(applicationPath,
                function() {
                    me.getInitializations(successFunction);
                });
        }

        // services
        getInitializations(finalFunction?: () => void) {
            var me = this;
            var request = null;
            me.application.hideServiceMessages();
            me.application.showWaiter('Initializing...');

            var fake = new fakeServiceResponse(request);
            me.handleInitializationResponse(fake);
            me.application.hideWaiter();
            finalFunction();
            /*
            me.peanut.executeService('ServiceName',request, me.handleInitializationResponse)
                .always(function() {
                    me.application.hideWaiter();
                    finalFunction();
                });
           */
        }

        private handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            // todo: delete handleServiceResponseTemplate when not needed
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.application.showMessage('Initialized')
            }
            else {
                me.application.showMessage('Failed')
            }
        };


        public serviceCallTemplate() {
            // todo: delete serviceCallTemplate when not needed
            var me = this;
            var request = null; //

            me.application.hideServiceMessages();
            me.application.showWaiter('Message here...');
            me.peanut.executeService('directory.ServiceName',request, me.handleServiceResponseTemplate)
                .always(function() {
                    me.application.hideWaiter();
                });
        }

        private handleServiceResponseTemplate = (serviceResponse: IServiceResponse) => {
            // todo: delete handleServiceResponseTemplate when not needed
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {


            }
        };

        public clickme = () => {
            var me = this;
            var planet = me.testMessage();
            me.testMessage(planet == 'Hello world' ? 'Hello mars' : 'Hello world');
        }

    }
}

Tops.testsimpletonViewModel.instance = new Tops.testsimpletonViewModel();
(<any>window).ViewModel = Tops.testsimpletonViewModel.instance;