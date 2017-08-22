/**
 * Created by Terry on 9/3/2015.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
///<reference path='../typings/underscore/underscore.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
// Module
module Tops {



    export class PersonListItem {
        public personId: any = 0;
        public name : string = '';

        public directoryLink: string = '';
        public selected: boolean = false;
    }


    export interface IGetEventResponse {
        event: Tops.EventInfo;
        persons: PersonListItem[];
        canEdit: any;
        canSendMail: any;
        userPersonId: any;
    }

    export class EventServiceRequest {
        public eventId : any;
        public personId : any;
        public action: string;
    }

    export class signupViewModel {
        static instance: Tops.signupViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;
        private eventId: any;
        private personId : any = 0;
        enlisted : boolean = false;

        // observables
        public event : EventObservable;
        public persons = ko.observableArray([]);
        canEdit = ko.observable(false);
        canSendMail = ko.observable(false);
        editLink = ko.observable('');
        viewLink = ko.observable('');
        inviteLink = ko.observable('');
        buttonLabel = ko.observable('');

        // Constructor
        constructor() {
            var me = this;

            Tops.signupViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
            me.event = new EventObservable();
           // me.messageModal = PeanutModal.initialize();
        }



        // Methods
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            jQuery("tops-task-view").hide();
            me.application.initialize(applicationPath,
                function() {
                    me.personId = HttpRequestVars.Get('pid',0);
                    me.eventId = HttpRequestVars.Get('eid',0);
                    me.getEvent(successFunction);
                });
        }

        getEvent(finalFunction?: () => void) {
            var me = this;
            me.application.hideServiceMessages();
            me.application.showWaiter('Initializing, please wait...');
            var request = new EventServiceRequest();
            request.eventId = me.eventId;
            request.personId = me.personId;
            request.action = 'get';

            /*
            // test
            me.personId = 0;
            var response = me.makeFakeEventResponse();
            var serviceResponse = new fakeServiceResponse(response);
            me.handleGetEventResponse(serviceResponse);
            */

            me.peanut.executeService('GetEventInfo', request, me.handleGetEventResponse)
                .always(function() {
                    me.application.hideWaiter();
                    if (finalFunction) {
                        finalFunction();
                    }
                });

        }

        handleGetEventResponse = (serviceResponse: Tops.IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result != Tops.Peanut.serviceResultErrors) {
                var response = <IGetEventResponse>serviceResponse.Value;
                me.event.assign(response.event);
                me.canEdit((response.canEdit == 1));
                me.canSendMail((response.canSendMail == 1));
                if (!me.personId) {
                    me.personId = response.userPersonId;
                }
                me.eventId = response.event.eventId;
                me.editLink('/node/'+me.eventId+'/edit');
                me.viewLink('/node/'+me.eventId);
                me.inviteLink('/invitations?eid='+me.eventId);
                me.setPersons(response.event.eventType,response.persons);
            }
            jQuery("tops-task-view").show();
        };

        private setPersons(eventType: string, persons: PersonListItem[]) {
            var me = this;
            _.each(persons, function (person : PersonListItem) {
                person.directoryLink = 'directory?cmd=showPerson&pid=' + person.personId;
            },me);
            me.enlisted = me.isEnlisted(persons);
            me.persons(persons);
            var pageTitle = 'Event';
            if (me.enlisted) {
                pageTitle = 'Your reminder';
                me.buttonLabel('Cancel reminder');
            }
            else {
                pageTitle = eventType == 'task' ? 'Sign me up' : 'Remind me';
                me.buttonLabel(pageTitle);
            }
            jQuery(".pageTitle").text(pageTitle);
        }

        private isEnlisted(persons: PersonListItem[]) {
            var me = this;
            var result = _.find(persons,function(person: PersonListItem) {
                return person.personId == me.personId;
            },me);
            return (result != null);
        }

        public signup = () => {
            var me = this;
            var request = new EventServiceRequest();
            request.eventId = me.eventId;
            request.personId = me.personId;
            request.action = me.enlisted ? 'cancel' : 'set';

            me.application.hideServiceMessages();
            me.application.showWaiter('Updating reminder...');
            me.peanut.executeService('UpdateReminder',request, me.handleUpdateReminderResponse)
                .always(function() {
                    me.application.hideWaiter();
                });
        };

        private handleUpdateReminderResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var persons = <PersonListItem[]>serviceResponse.Value;
                me.setPersons(me.event.type(),persons);
            }
        };





    }
}

Tops.signupViewModel.instance = new Tops.signupViewModel();
(<any>window).ViewModel = Tops.signupViewModel.instance;