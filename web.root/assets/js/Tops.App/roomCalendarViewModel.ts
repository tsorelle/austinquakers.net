/**
 * Created by Terry on 9/12/2015.
 */
/**
 * Created by Terry on 9/4/2015.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />



// Module
module Tops {

    // data classes
    export interface ICalendarItem {
        eventId : any;
        title: string;
        when: string;
        eventLink: string;
        end: string;
        start: string;
        conflict: boolean;
    }

    export class roomCalendarViewModel {
        static instance: Tops.roomCalendarViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // variables

        // observables
        roomLookup : KnockoutObservableArray<ILookupListItem> = ko.observableArray([]);
        selectedRoom : KnockoutObservable<ILookupListItem> = ko.observable(null);
        eventList : KnockoutObservableArray<ICalendarItem> = ko.observableArray([]);


        // Constructor
        constructor() {
            var me = this;

            Tops.roomCalendarViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }



        // Methods
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            jQuery('#view-container').hide();
            me.application.initialize(applicationPath,
                function() {
                    me.selectedRoom.subscribe(me.getCalendar);
                    me.getInitializations(successFunction);
                });
        }

        // services
        getInitializations(finalFunction: () => void) {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();
            me.application.showWaiter('Initializing...');
            me.peanut.executeService('GetRoomList',request, me.handleInitializationResponse)
                .always(function() {
                    me.application.hideWaiter();
                    jQuery('#room-calendar-view').show();
                    finalFunction();
                });
        }

        private handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var lookupList = <ILookupListItem[]>serviceResponse.Value;
                me.roomLookup(lookupList);
            }
        };


        public getCalendar = (item : ILookupListItem) => {
            var me = this;
            if (!item) {
                return;
            }
            // alert('getcalendar ' + item.text);       return;

            var request = item.value;
            me.application.hideServiceMessages();
            me.application.showWaiter('Getting calendar...');
            me.peanut.executeService('GetRoomCalendar',request, me.handleGetCalendarResponse)
                .always(function() {
                    me.application.hideWaiter();
                });
        };

        private handleGetCalendarResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                var calendar = <ICalendarItem[]>serviceResponse.Value;
                var previous = null;
                _.each(calendar, function (item : ICalendarItem) {
                    item.eventLink = 'node/' + item.eventId;
                    item.conflict = (previous !== null && previous.end > item.start);
                    previous = item;
                },me);
                me.eventList(calendar);
            }
        };


    }
}

Tops.roomCalendarViewModel.instance = new Tops.roomCalendarViewModel();
(<any>window).ViewModel = Tops.roomCalendarViewModel.instance;