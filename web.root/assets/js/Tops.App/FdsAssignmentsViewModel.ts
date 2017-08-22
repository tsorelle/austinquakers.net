/**
 * Created by Terry on 5/25/2015.
 */
/**
 * Created by Terry on 9/17/2014.
 */
/**
 * Created by Terry on 5/26/14.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
///<reference path='../typings/underscore/underscore.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
// Module
module Tops {

    export class FdsAssignmentDto {
        public id: number;
        public assignmentDate: string;
        public personID: number;
        public ageGroupId: number;
        public note: string;
        public role: number;
        public state: number;
    }

    export class FdsAssignmentView extends FdsAssignmentDto {
        public teacherName: string;
        public ageGroupName: string;
        public roleName: string;
    }

    export class LookupListItem {
        public value : any;
        public text : string;
        public title : string;
    }

    export class WeekListItem {
        public order : number;
        public value : string;
        public longText : string;
        public shortText : string;
    }

    export class UpdateFdsAssignmentsRequest {
        public month: number;
        public year: number;
        public updates : FdsAssignmentDto[];
    }

    export interface IAssignmentsResponse {
        month : number;
        year: number;
        displayMonth: string;
        assignments: FdsAssignmentDto[];
        calendar: WeekListItem[];
    }

    export class InitFdsClassManagerResponse implements IAssignmentsResponse{
        public month : number;
        public year: number;
        public displayMonth: string;
        public assignments: FdsAssignmentDto[];
        public calendar: WeekListItem[];
        public ageGroups : LookupListItem[];
        public teachers: LookupListItem[];
    }


    export class UpdateFdsAssignmentsResponse implements IAssignmentsResponse {
        public month : number;
        public year: number;
        public displayMonth: string;
        public assignments: FdsAssignmentDto[];
        public calendar: WeekListItem[];
    }

    export class FdsAssignmentsViewModel {
        static instance: Tops.FdsAssignmentsViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // Constructor
        constructor() {
            var me = this;

            Tops.FdsAssignmentsViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        // private variables
        private currentMonth: Number  = 0;
        private currentYear:  Number  = 0;
        private insertId: number = 0;

        // observables
        displayMonth: KnockoutObservable<string> = ko.observable('Month');

        assignments1 : KnockoutObservableArray<FdsAssignmentDto> = ko.observableArray([]);
        assignments2 : KnockoutObservableArray<FdsAssignmentDto> = ko.observableArray([]);
        assignments3 : KnockoutObservableArray<FdsAssignmentDto> = ko.observableArray([]);
        assignments4 : KnockoutObservableArray<FdsAssignmentDto> = ko.observableArray([]);
        assignments5 : KnockoutObservableArray<FdsAssignmentDto> = ko.observableArray([]);

        assignmentsHeader1 : KnockoutObservable<string> = ko.observable('');
        assignmentsHeader2 : KnockoutObservable<string> = ko.observable('');
        assignmentsHeader3 : KnockoutObservable<string> = ko.observable('');
        assignmentsHeader4 : KnockoutObservable<string> = ko.observable('');
        assignmentsHeader5 : KnockoutObservable<string> = ko.observable('');

        editing : KnockoutObservable<boolean> = ko.observable(false);
        waiting : KnockoutObservable<boolean> = ko.observable(false);
        ready : KnockoutObservable<boolean> = ko.observable(false);
        readyToPostNewItem : KnockoutComputed<boolean>; // assigned to KnockoutComputed - me.newAssignmentReady

        weeks: KnockoutObservable<number> = ko.observable(4);
        calendar: KnockoutObservableArray<WeekListItem> = ko.observableArray([]);
        teachers: KnockoutObservableArray<LookupListItem> = ko.observableArray([]);
        ageGroups: KnockoutObservableArray<LookupListItem> = ko.observableArray([]);
        teacherRoles: KnockoutObservableArray<LookupListItem> = ko.observableArray([]);

        newItemDate : KnockoutObservable<WeekListItem> = ko.observable(null);
        newItemAgeGroup : KnockoutObservable<LookupListItem> = ko.observable(null);
        newItemTeacher : KnockoutObservable<LookupListItem> = ko.observable(null);
        newItemRole : KnockoutObservable<LookupListItem> = ko.observable(null);

        assignmentUpdates : KnockoutObservableArray<FdsAssignmentDto> = ko.observableArray([]);


        // Methods


        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            me.wait();
            me.application.initialize(applicationPath,
                function() {
                    me.readyToPostNewItem = ko.computed(me.newAssignmentReady);
                    me.createRoleList();
                    me.getInitialData(successFunction);
                });
        }

        createRoleList() {
            var me = this;
            var list : LookupListItem[] = [];
            var item = new LookupListItem();
            item.text = 'Teacher';
            item.value = 1;
            list.push(item);

            item = new LookupListItem();
            item.text = 'Assistant';
            item.value = 2;
            list.push(item);

            me.teacherRoles(list);
        }

        getInitialData(finalFunction: () => void) {
            var me = this;
            me.application.hideServiceMessages();
            (me.peanut.executeService('InitFdsClassManager', null,
                function(serviceResponse: Tops.IServiceResponse) {
                    if (serviceResponse.Result != Tops.Peanut.serviceResultErrors) {
                        var response = <InitFdsClassManagerResponse>serviceResponse.Value;
                        me.ageGroups(response.ageGroups);
                        me.teachers(_.sortBy(response.teachers,'text'));
                        me.bindAssignments(response);
                    }
                    else {
                        alert("Service failed");
                    }
                    finalFunction();
                }
            )).done(me.stopWaiting);
        }

        convertAssignments(assignments: FdsAssignmentDto[]) {
            var context = {
                me: this,
                result : []
            };
            var me = this;
            _.each(assignments,function(assignment: FdsAssignmentDto){
                this.result.push(this.me.createAssignmentView(assignment));
            },context);
            return context.result;
        }

        bindAssignments(response: IAssignmentsResponse) {
            var me = this;
            me.assignmentUpdates([]);
            me.displayMonth(response.displayMonth);
            me.calendar(response.calendar);
            me.weeks(response.calendar.length);
            me.currentMonth =  response.month;
            me.currentYear = response.year;
            for(var w=0; w<5; w++) {
                var assignments = [];
                var header = '';
                if (response.calendar[w]) {
                    var wdate = response.calendar[w].value;
                    assignments  = _.filter(response.assignments,
                        function(a : FdsAssignmentDto) {
                            return a.assignmentDate == this;
                        }
                    ,wdate);
                    header = response.calendar[w].longText;
                    assignments = me.convertAssignments(assignments);
                }

                switch(w) {
                    case 0 :
                        me.assignments1(assignments);
                        me.assignmentsHeader1(header);
                        break;

                    case 1 :
                        me.assignments2(assignments);
                        me.assignmentsHeader2(header);
                        break;
                    case 2 :
                        me.assignments3(assignments);
                        me.assignmentsHeader3(header);
                        break;
                    case 3 :
                        me.assignments4(assignments);
                        me.assignmentsHeader4(header);
                        break;
                    case 4 :
                        me.assignments5(assignments);
                        me.assignmentsHeader5(header);
                        break;
                }
            }
        }

        getTeacher(assignment: FdsAssignmentDto) : string {
            var me = this;
            var teachers = me.teachers();
            var teacher : LookupListItem = _.find(teachers,function(teacher: LookupListItem) {
                return teacher.value == this.personID;
            }, assignment );
            if (!teacher) {
                return "Unknown";
            }
            return teacher.text;
        }

        getAgeGroup(assignment: FdsAssignmentDto) : string {
            var me = this;
            var ageGroups = me.ageGroups();
            var ageGroup : LookupListItem = _.find(ageGroups,function(group: LookupListItem) {
                return group.value == this.ageGroupId;
            }, assignment );
            if (!ageGroup) {
                return 'Unknown';
            }
            return ageGroup.text;
        }

        createAssignmentView(assignment: FdsAssignmentDto)  : FdsAssignmentView {
            var me = this;
            var view = <any>assignment;
            view.ageGroupName = me.getAgeGroup(assignment);
            view.teacherName = me.getTeacher(assignment);
            view.roleName = assignment.role == 1 ? 'Teacher' : 'Assistant';
            view.state = 0;
            return <FdsAssignmentView>view;
        }

        wait = ()=>   {
            var me = this;
            me.ready(false);
            me.waiting(true);
        };

        stopWaiting = ()=> {
            var me = this;
            me.ready(true);
            me.waiting(false);
        };

        newAssignmentReady = ():boolean => {
            var me = this;
            return (me.newItemAgeGroup() && me.newItemDate() && me.newItemRole() && me.newItemTeacher()) ? true : false;
        };

        getPreviousMonth() {
            var me = this;
            me.saveAssignmentsAndGetNext(-1);
        }

        getNextMonth() {
            // alert("next");
            var me = this;
            me.saveAssignmentsAndGetNext(1);
        }

        saveAssignments() {
            var me = this;
            me.saveAssignmentsAndGetNext(0);
        }

        createUpdateRequest(increment: number) {
            var me = this;
            var updates = me.assignmentUpdates();
            var request = new UpdateFdsAssignmentsRequest();
            request.month = Number(me.currentMonth);
            request.month += increment;
            request.year = Number(me.currentYear);
            if (request.month < 1) {
                request.month = 12;
                request.year--;
            }
            else if (request.month > 12) {
                request.month = 1;
                request.year++;
            }
            request.updates = me.assignmentUpdates();
            return request;
        }

        saveAssignmentsAndGetNext(increment: number) {
            var me = this;
            me.application.hideServiceMessages();
            me.wait();

            var request = me.createUpdateRequest(increment);
            (me.peanut.executeService('UpdateFdsClasses', request,
                function(serviceResponse: Tops.IServiceResponse) {
                    if (serviceResponse.Result != Tops.Peanut.serviceResultErrors) {
                        var response = <UpdateFdsAssignmentsResponse>serviceResponse.Value;
                        me.bindAssignments(response);
                    }
                    else {
                        alert("Service failed");
                    }

                }
            )).done(
                me.stopWaiting
            );
        }

        clearEditForm() {
            var me = this;
            me.newItemDate(null);
            me.newItemRole(null);
            me.newItemTeacher(null);
            me.newItemAgeGroup(null);
        }

        showEditForm() {
            var me = this;
            me.clearEditForm();
            me.editing(true);
        }

        hideEditForm() {
            var me = this;
            me.editing(false);
            me.ready(true);
        }

        addNewAssignment() {
            var me = this;
            var newAssignment = new FdsAssignmentDto();
            newAssignment.ageGroupId = me.newItemAgeGroup().value;
            newAssignment.assignmentDate = me.newItemDate().value;
            newAssignment.personID = me.newItemTeacher().value;
            newAssignment.role = me.newItemRole().value;
            newAssignment.id = --me.insertId;
            newAssignment.state = 1;

            me.assignmentUpdates.push(newAssignment);

            var newView =new FdsAssignmentView();
            newView.ageGroupName = me.newItemAgeGroup().text;
            newView.roleName = me.newItemRole().text;
            newView.teacherName = me.newItemTeacher().text;
            newView.ageGroupId = newAssignment.ageGroupId;
            newView.assignmentDate = newAssignment.assignmentDate;
            newView.personID = newAssignment.personID;
            newView.role = newAssignment.role;
            newView.id =  newAssignment.id;
            newView.state = 1;

            switch (me.newItemDate().order) {
                case 1 :
                    me.assignments1.push(newView);
                    break;
                case 2 :
                    me.assignments2.push(newView);
                    break;
                case 3 :
                    me.assignments3.push(newView);
                    break;
                case 4 :
                    me.assignments4.push(newView);
                    break;
                case 5 :
                    me.assignments5.push(newView);
                    break;
            }

            me.editing(false);
            me.ready(true);
        }

        removeAssignment = (view : FdsAssignmentView)=> {
            var me = this;
            if (view.state == 0) {
                // add to update list
                var deleted = new FdsAssignmentDto();
                deleted.id = view.id;
                deleted.state = -1;
                me.assignmentUpdates.push(deleted);
            }
            else {
                // remove insert from update list
                var updates = me.assignmentUpdates();
                var insertedItem = _.find(updates,
                    function(item: FdsAssignmentDto) {
                        return item.id == this.id;
                    },
                    view);
                if (insertedItem) {
                    me.assignmentUpdates.remove(insertedItem);
                }
            }

            var calendarItem = _.find(
                me.calendar(),
                function(item : WeekListItem) {
                        return item.value == (<FdsAssignmentView>this).assignmentDate;
                    },view);

            if (calendarItem) {
                switch(calendarItem.order) {
                    case 1 :
                        me.assignments1.remove(view);
                        break;
                    case 2 :
                        me.assignments2.remove(view);
                        break;
                    case 3 :
                        me.assignments3.remove(view);
                        break;
                    case 4 :
                        me.assignments4.remove(view);
                        break;
                    case 5 :
                        me.assignments5.remove(view);
                        break;
                }
            }
        };
    }
}

Tops.FdsAssignmentsViewModel.instance = new Tops.FdsAssignmentsViewModel();
(<any>window).ViewModel = Tops.FdsAssignmentsViewModel.instance;