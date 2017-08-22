///<reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />


// replace all occurances of 'yourName' with the name of your model
//  e.g.  yourName -> billingConfiguration  produces billingConfigurationViewModel


// Module
module Tops {

    // data classes
    /* Example:
    export class EventInfo {
        public eventId: any = 0;
        public eventType: string = '';
        public title: string = '';
        public description: string = '';
        public fromDate: string = '';
        public toDate: string = '';
    }

    */


    export class yourNameViewModel {
        static instance: Tops.yourNameViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // variables

        // observables
        // canEdit = ko.observable(false);

        // Constructor
        constructor() {
            var me = this;

            Tops.yourNameViewModel.instance = me;
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
            me.peanut.executeService('ServiceName',request, me.handleInitializationResponse)
                .always(function() {
                    me.application.hideWaiter();
                    finalFunction();
                });
        }

        private handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            // todo: delete handleServiceResponseTemplate when not needed
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {


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


    }
}

Tops.yourNameViewModel.instance = new Tops.yourNameViewModel();
(<any>window).ViewModel = Tops.yourNameViewModel.instance;