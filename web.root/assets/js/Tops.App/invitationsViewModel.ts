/**
 * Created by Terry on 9/9/2015.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />

// Module
module Tops {

    // data classes
    export interface IInitInvitationsResponse {
        event: Tops.EventInfo;
        canEdit: any;
        mailingLists : ILookupListItem[];
    }

    export class sendInvitationsRequest {
        event: Tops.EventInfo;
        listId: number;
        testMessageOnly : number;
        comment: string;
    }

    export class invitationsViewModel {
        static instance: Tops.invitationsViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        private eventId: any;
        private eventInfo : EventInfo = null;

        // observables
        public event : EventObservable = new EventObservable();
        public comments = ko.observable('');
        public mailingLists : KnockoutObservableArray<ILookupListItem> = ko.observableArray([]);
        public selectedList : KnockoutObservable<ILookupListItem> = ko.observable(null);
        public canEdit = ko.observable(false);
        public sendTestMessage = ko.observable(false);
        public formVisible = ko.observable(false);

        // Constructor
        constructor() {
            var me = this;
            Tops.invitationsViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }



        // Methods
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            jQuery("tops-invitation-view").hide();
            me.application.initialize(applicationPath,
                function() {
                    me.eventId = HttpRequestVars.Get('eid',0);
                    if (me.eventId == 0) {
                        me.application.showErrorMessage("Error: No event id received.")
                    }
                    else {
                        me.getInitializations(successFunction);
                    }
                });
        }


        // services
        getInitializations(successFunction?: () => void) {
            var me = this;
            me.application.hideServiceMessages();
            me.application.showWaiter('Initializing, please wait...');
            var request = me.eventId;
/*
             // test
             var response = me.makeFakeEventResponse();
             var serviceResponse = new fakeServiceResponse(response);
             me.handleInitializationResponse(serviceResponse);

*/
            me.peanut.executeService('InitializeEventInvitation', request, me.handleInitializationResponse)
                .always(function() {
                    me.application.hideWaiter();
                    successFunction();
                });

        }

        handleInitializationResponse = (serviceResponse: Tops.IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result != Tops.Peanut.serviceResultErrors) {
                var response = <IInitInvitationsResponse>serviceResponse.Value;
                me.eventInfo = response.event;
                me.event.assign(response.event);
                me.formVisible(me.event.isCurrent());
                me.canEdit((response.canEdit == 1));
                me.mailingLists(response.mailingLists);
                if (me.mailingLists.length > 0) {
                    me.selectedList(me.mailingLists[0]);
                }
                jQuery("tops-invitation-view").show();
            }
        };

        sendMessage = () => {
            var me = this;
            var list = me.selectedList();
            var request = new sendInvitationsRequest();
            request.event = me.eventInfo;
            request.listId = me.selectedList().value;
            request.testMessageOnly = me.sendTestMessage() ? 1 : 0;
            request.comment = me.comments();

            me.application.hideServiceMessages();
            me.application.showWaiter('Sending invitations...');
            me.peanut.executeService('SendEventInvitations',request, me.handleSendMessageResponse)
                .always(function() {
                    var visible = me.event != null && me.event.isCurrent ? true : false;
                    me.application.hideWaiter();
                });
        };

        private handleSendMessageResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.formVisible(me.sendTestMessage());
            }
        };
    }
}

Tops.invitationsViewModel.instance = new Tops.invitationsViewModel();
(<any>window).ViewModel = Tops.invitationsViewModel.instance;