/**
 * Created by Terry on 9/17/2014.
 */
/**
 * Created by Terry on 5/26/14.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
// Module
module Tops {

    export class WmTask {
        public wmTaskId: number;
        public taskDate: string;
        public greeter: string;
        public closer: string;
    }

    export class GetMonthlyTasksRequest {
        public month: number;
        public year: number;
    }

    export class UpdateMonthlyTasksRequest  {
        public  month :number;
        public  year: number;
        public  tasks: WmTask[];
        public  increment : number;

    }


    export class WmTaskView extends  WmTask {
        public dayOfMonth : string;
    }

    export class GetMonthlyTasksResponse {
        public month : number;
        public year : number;
        public displayMonth : string;
        public tasks : WmTaskView[];
    }


    // replace all occurances of 'yourVmName' with the name of your model
    //  e.g.  yourVmName -> billingConfiguration  produces billingConfigurationViewModel
    export class wmtasksViewModel {
        static instance: Tops.wmtasksViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // Constructor
        constructor() {
            var me = this;

            Tops.wmtasksViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        // observables
        displayMonth: KnockoutObservable<string> = ko.observable('Month');
        month: KnockoutObservable<number> = ko.observable(9);
        year:  KnockoutObservable<number> = ko.observable(2014);
        tasks: KnockoutObservableArray<WmTaskView> = ko.observableArray([]);
        waiting : KnockoutObservable<boolean> = ko.observable(false);
        ready : KnockoutObservable<boolean> = ko.observable(false);
        previousTasks: WmTask[];

        person: KnockoutObservable<any> = ko.observable();
        // Methods

        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            me.application.initialize(applicationPath, function() {
                me.waiting();
                me.getInitialMonth(successFunction);
            });
        }

        wait() {
            var me = this;
            me.ready(false);
            me.waiting(true);
        }

        stopWaiting() {
            var me = this;
            me.ready(true);
            me.waiting(false);
        }


        getPreviousMonth() {
            var me = this;
            me.saveTasksAndGetNext(-1);
        }

        getNextMonth() {
            // alert("next");
            var me = this;
            me.saveTasksAndGetNext(1);
        }

        saveTasks() {
            var me = this;
            me.saveTasksAndGetNext(0);
        }

        tasksChanged() : boolean {
            var me = Tops.wmtasksViewModel.instance;
            var newValues : WmTask[] = me.tasks();
            for (var i = 0; i < newValues.length; i++) {
                if (newValues[i].closer != me.previousTasks[i].closer ||
                        newValues[i].greeter != me.previousTasks[i].greeter)
                    return true;
            }
            return false;
        }

        saveTasksAndGetNext(increment: number) {
            var me = this;

            me.application.hideServiceMessages();
            me.wait();

            if (me.tasksChanged()) {

                var request = new UpdateMonthlyTasksRequest();
                request.month = me.month();
                request.tasks = me.tasks();
                request.year = me.year();
                request.increment = increment;
                me.peanut.executeService('UpdateWmTasks', request, me.handleGetMonthResponse);
            }
            else {
                var month : number = me.month();
                month = Number(month);
                month += increment;
                var year : number = me.year();
                if (month < 1) {
                    month = 12;
                    year--;
                }
                else if (month > 12) {
                    month = 1;
                    year++;
                }
                me.getMonth(month,year);
            }
        }

        getInitialMonth(successFunction: () => void) {
            var me = this;
            me.application.hideServiceMessages();
            var request = new GetMonthlyTasksRequest();
            request.month = 0;
            request.year = 0;
            me.peanut.executeService('GetWmTasks', request,
                function(serviceResponse: Tops.IServiceResponse) {
                    if (me.handleGetMonthResponse(serviceResponse)) {
                        successFunction();
                    }
                }
            );
        }

        getMonth(month : number = 0, year: number = 0) { // zero values return current
            var me = this;
            me.application.hideServiceMessages();
            var request = new GetMonthlyTasksRequest();
            request.month = month;
            request.year = year;
            me.peanut.executeService('GetWmTasks', request, me.handleGetMonthResponse);
        }

        cloneTasks(tasks: WmTaskView[]) {
            var me = Tops.wmtasksViewModel.instance;
            me.previousTasks = [];
            for (var i = 0; i < tasks.length; i++) {
                var clone = new WmTask();
                clone.closer = tasks[i].closer;
                clone.greeter = tasks[i].greeter;
                me.previousTasks[i] = clone;
            }
        }

        handleGetMonthResponse(serviceResponse: Tops.IServiceResponse) {
            var me = Tops.wmtasksViewModel.instance;
            var ok = (serviceResponse.Result != Tops.Peanut.serviceResultErrors);
            if (ok) {
                var response = <GetMonthlyTasksResponse>serviceResponse.Value;
                me.displayMonth(response.displayMonth);
                me.month(response.month);
                me.year(response.year);
                me.cloneTasks(response.tasks);
                me.tasks(response.tasks);
            }
            else {
                alert("Service failed");
            }
            me.stopWaiting();
            return ok;
        }

        handleServiceException(errorMessage: string) {
            var me = Tops.wmtasksViewModel.instance;
            me.waiting(false);
        }
    }
}

Tops.wmtasksViewModel.instance = new Tops.wmtasksViewModel();
(<any>window).ViewModel = Tops.wmtasksViewModel.instance;