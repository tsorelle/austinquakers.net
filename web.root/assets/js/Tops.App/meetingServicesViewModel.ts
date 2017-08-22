/**
 * Created by Terry on 9/10/2015.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />


// replace all occurances of 'MeetingServices' with the name of your model
//  e.g.  MeetingServices -> billingConfiguration  produces billingConfigurationViewModel


// Module
module Tops {

    // data classes
    export interface ITaskListItem {
        eventId : number;
        title : string;
        when : string;
        description: string;
        signupLink: string;
    }


    export class meetingServicesViewModel {
        static instance: Tops.meetingServicesViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // variables
        allTasks : ITaskListItem[] = [];
        itemCount = 0;
        pageSize = 8;
        pageNumber = 1;

        // observables
        taskList : KnockoutObservableArray<ITaskListItem> = ko.observableArray([]);
        showPager = ko.observable(false);
        showNext = ko.observable(false);
        showPrevious = ko.observable(false);

        // Constructor
        constructor() {
            var me = this;

            Tops.meetingServicesViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }



        // Methods

        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            jQuery('#meeting-services-view').hide();
            me.application.initialize(applicationPath,
                function() {
                    me.getInitializations(successFunction);
                });
        }


        // services
        getInitializations(finalFunction?: () => void) {
            var me = this;
            var request = null;

            me.application.hideServiceMessages();

            /*
            var data = me.getFakeResponse();
            var response = new fakeServiceResponse(data);
            me.handleInitializationResponse(response);
            */
            me.application.showWaiter('Initializing...');
            me.peanut.executeService('GetUpcomingTasks',request, me.handleInitializationResponse)
                .always(function() {
                    me.application.hideWaiter();
                    finalFunction();
                });

        }

        private handleInitializationResponse = (serviceResponse: IServiceResponse) => {
            var me = this;
            if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                me.allTasks = <ITaskListItem[]>serviceResponse.Value;
                _.each(me.allTasks, function (task : ITaskListItem) {
                    task.signupLink = 'signup?eid=' + task.eventId;
                },me);
                me.itemCount = me.allTasks.length;
                me.showPager(me.itemCount > me.pageSize);
                me.setPage(0);
                jQuery('#meeting-services-view').show();
            }
        };

        // methods
        previousPage() {
            var me = this;
            me.setPage(-1);
        }

        nextPage() {
            var me = this;
            me.setPage(1);
        }

        setPage(increment: number = 0) {
            var me = this;
            var totalItems = me.allTasks.length;
            me.pageNumber = me.pageNumber + increment;
            var offset = (me.pageNumber - 1) * me.pageSize;


            var pageItemCount = me.pageSize;
            if (offset + pageItemCount > me.itemCount) {
                pageItemCount = me.itemCount - offset;
            }



            var last = offset + pageItemCount;
            var pageItems = [];
            var itemIndex = 0;
            for (var i = offset; i < last; i++ ) {
                pageItems[itemIndex] = me.allTasks[i];
                itemIndex += 1;
            }
            me.taskList(pageItems);
            me.showNext(last < me.allTasks.length);
            me.showPrevious(offset > 0);
        }


        private getFakeResponse() {
            var response = [];
            var max = 25;
            for (var i = 0; i < max; i++) {
                response[i] =
                {
                    eventId : i,
                    title : 'Event ' + i,
                    when: 'September 12, 2015, 8:00 am to 10:00',
                    description: 'This is a description blah bal'
                };
            }
            return response;
        }

    }
}

Tops.meetingServicesViewModel.instance = new Tops.meetingServicesViewModel();
(<any>window).ViewModel = Tops.meetingServicesViewModel.instance;