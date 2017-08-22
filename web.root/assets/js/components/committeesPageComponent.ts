///<reference path="searchListObservable.ts"/>
///<reference path="personSelectorComponent.ts"/>
///<reference path="selectListObservable.ts"/>
///<reference path="CKEditorControl.ts"/>
/**
 * Created by Terry on 5/20/2016.
 */
///<reference path="committeeObservable.ts"/>
///<reference path="../typings/ckeditor/ckeditor.d.ts"/>
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    interface ICommitteeListItem extends INameValuePair {
        active: any;
    }
    interface IGetCommitteeListResponse {
        list: ICommitteeListItem[],
        canEdit: boolean;
    }

    
    interface IMemberReportItem {
        statusId : any;
        memberName : string;
        email : string;
        phone : string;
        role : string;
        nominationStatus : any;
    }
    interface ICommitteeReportItem extends IMemberReportItem{
        committeeId : any;
        committeeName : string;
    }

    interface ICommitteeReportDisplayItem {
        committeeName: string;
        members: IMemberReportItem[];
    }


    export class committeesPageComponent implements IEventSubscriber{

        private application:IPeanutClient;
        private peanut:Peanut;
        private owner : IEventSubscriber;
        private editorLoaded = false;
        canEdit = ko.observable(false);
        pageView = ko.observable('forms');
        reportDate = ko.observable('');
        committeeForm: committeeObservable;
        termOfServiceForm : termOfServiceObservable;
        personSelector: personSelectorComponent;
        committeeList: ICommitteeListItem[];
        committeeSelector: selectListObservable;
        activeFilter = true;
        memberList: ITermOfServiceListItem[];
        members: KnockoutObservableArray<ITermOfServiceListItem> = ko.observableArray([]);
        currentMemberFilter = 'current';
        committeeMemberFilter : selectListObservable;

        // descriptionEditor : CKEditorControl;
        datePickerInitialized = false;

        reportResponse: ICommitteeReportItem[] = [];


        reportOptionsColumnClass = ko.observable('col-md-12');

        reportOptions = {
            currentMembers: ko.observable(true),
            nominations: ko.observable(true),
            emails: ko.observable(true),
            phones: ko.observable(true),
            committeeFilter: ko.observable('all')
        };

        report = {
            current: ko.observableArray<ICommitteeReportDisplayItem>([]),
            nominated: ko.observableArray<ICommitteeReportDisplayItem>([])
        };

        public constructor(application:IPeanutClient, owner: IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            me.committeeMemberFilter = new selectListObservable(me.onMemberFilterChange,[
                {Name:'Current members', Value: 'current' },
                {Name:'Nominations', Value: 'nominated' },
                {Name:'Current and Former members', Value: 'former' }],'current');
        }

        public initialize(finalFunction? : () => void) {
            var me = this;
            me.committeeForm = new committeeObservable();
            me.memberList = [];
            me.members([]);
            me.termOfServiceForm = new termOfServiceObservable();
            // me.application.hidePageHeading();
            // me.application.setPageHeading('Testing');
            me.personSelector = new personSelectorComponent(me.application,me);
            me.application.registerComponent('person-selector',me.personSelector,
                function () {
                    me.committeeForm.initialize(function () {
                        me.personSelector.initialize(function () {
                            me.getCommitteeList(finalFunction);
                        });
                    });
                }
            );
        }
        
        private getCommitteeList(finalFunction : () => void) {
            var me = this;
            var request = null;
            me.application.hideServiceMessages();
            me.committeeSelector = new selectListObservable(me.selectCommittee, []);
            me.peanut.executeService('GetCommitteeList', request,
                function (serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var response = <IGetCommitteeListResponse>serviceResponse.Value;
                        me.committeeList = response.list;
                        me.canEdit(response.canEdit);
                        var filtered = me.filterCommitteeList(true);
                        me.committeeSelector.setOptions(filtered);
                        // me.committeeSelector = new selectListObservable(me.selectCommittee, filtered);
                        me.committeeSelector.subscribe();
                        var cid = HttpRequestVars.Get('cid');
                        if (cid) {
                            me.committeeSelector.setValue(cid);
                        }
                    }
                    else {
                        me.pageView('none');
                    }
                    if (finalFunction) {
                        finalFunction();
                    }
                }
            );
        }

        selectMember = (selected: ITermOfServiceListItem) => {
            var me = this;
            if (selected) {
                // alert("Selected: " + selected.name + ' ' + selected.termOfService);
                me.termOfServiceForm.assign(selected,me.committeeForm.name());
                me.termOfServiceForm.view();
                // var state = me.termOfServiceForm.viewState();
                me.showTermDetail();
            }
        };

        private showTermDetail() {
            var me = this;
            if (!me.datePickerInitialized) {
                jQuery(function() {
                    jQuery( ".datepicker" ).datepicker({
                        changeYear: true
                    });
                });
                me.datePickerInitialized = true;
            }
            jQuery("#term-detail-modal").modal('show');
        }

        private selectCommittee = (selected: INameValuePair) => {
            var me = this;
            if (selected) {
                var request = selected.Value;
                me.committeeForm.view();
                me.application.hideServiceMessages();
                me.peanut.executeService('GetCommitteeAndMembers', request,
                    function (serviceResponse: IServiceResponse) {
                        if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                            var response = <IGetCommitteeResponse>serviceResponse.Value;
                            me.committeeForm.assign(response.committee);
                            me.reportOptionsColumnClass('col-md-6');
                            me.committeeMemberFilter.unsubscribe();
                            me.memberList = response.members;
                            jQuery("#all-members-checkbox").attr("checked",false);
                            me.committeeMemberFilter.unsubscribe();
                            me.committeeMemberFilter.setValue('current');
                            me.filterMemberList('current');
                            me.committeeMemberFilter.subscribe();
                        }
                    }
                );
            }
            else {
                me.reportOptionsColumnClass('col-md-12');
                me.committeeForm.clear();
            }
        };

        filterMemberList = (filter: string) => {
            var me = this;
            var filtered =  _.filter(me.memberList,function (item: ITermOfServiceListItem) {
                if (filter == 'nominated') {
                    return item.statusId < 3;
                }
                if (item.statusId != 3) {
                    return false;
                }
                var today = Dates.getCurrentDateString('isodate');
                if (filter == 'current' && item.dateRelieved) {
                    return item.dateRelieved >= today;
                }
                return true;
            });
            
            me.currentMemberFilter = filter;
            me.members(filtered);
        };

        onMemberFilterChange = (selected: INameValuePair) => {
            var me = this;
            me.filterMemberList(selected.Value);
        }

        resetCommitteeList = () => {
            var me = this;
            me.activeFilter = !me.activeFilter;
            me.committeeSelector.unsubscribe();
            me.committeeSelector.setOptions(me.filterCommitteeList(me.activeFilter));
            me.committeeSelector.subscribe();
            return true;
        };

        private filterCommitteeList(active: boolean) {
            var me = this;
            if (!active) {
                return me.committeeList;
            }
            else {
                return _.filter(me.committeeList,function (item: ICommitteeListItem) {
                   return item.active;
                });
            }
        }
        
        private closeCommittee() {
            var me = this;
            me.committeeSelector.setValue(null);
            me.committeeForm.clear();
            me.memberList = [];
            me.members([]);
        }

        newCommittee = () => {
            var me=this;
            me.closeCommittee();
            me.editCommittee();
        };

        editCommittee = () => {
            var me = this;
            // me.descriptionEditor.show();
            me.committeeForm.editMode();
        };

        saveCommittee = () => {
            var me = this;
            if (!me.committeeForm.validate()) {
                return;
            }
            var request = me.committeeForm.getValues();
            var isNew = request.committeeId == 0;
            me.application.hideServiceMessages();
            me.peanut.executeService('UpdateCommittee', request,
                function (serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var response = <IFmaCommittee>serviceResponse.Value;
                        if (isNew) {
                            var lookupItem : ICommitteeListItem = {
                                Name: request.name,
                                Value: response.committeeId,
                                active : request.active
                            };

                            me.committeeList.push(lookupItem);
                            me.committeeList = _.sortBy(me.committeeList,"Name");
                            me.committeeSelector.unsubscribe();
                            if ((!request.active) && me.activeFilter == true ) {
                                me.activeFilter = false;
                                jQuery("filter-committees-checkbox").attr("checked",false);
                            }
                            me.committeeSelector.setOptions(me.filterCommitteeList(me.activeFilter));
                            me.committeeSelector.setValue(response.committeeId);
                            me.committeeSelector.subscribe();
                        }
                        me.committeeForm.assign(response);
                        me.committeeForm.view();
                    }
                }
            );


        };

        cancelCommitteeChanges = () => {
            var me = this;
            me.committeeForm.view();
        };
        
        showPersonSearch = () => {
            var me = this;
            me.personSelector.show();
        };
        

        updateTerm = () => {
            var me = this;
            if (!me.termOfServiceForm.validate()) {
                return;
            }
            var request = me.termOfServiceForm.getValues();

            var me = this;
            if (!me.termOfServiceForm.validate()) {
                return;
            }
            var isNew = request.committeeMemberId == 0;
            me.application.hideServiceMessages();
            me.peanut.executeService('UpdateCommitteeTerm', request,
                function (serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        me.memberList = <ITermOfServiceListItem[]>serviceResponse.Value;
                        me.filterMemberList(me.currentMemberFilter);
                    }
                }
            );
            jQuery("#term-detail-modal").modal('hide');
        };

        editTerm = () => {
            var me = this;
            me.termOfServiceForm.edit();
        };

        cancelTermEdit = () => {
            var me = this;
            me.termOfServiceForm.rollback();
            me.termOfServiceForm.view();
            if (me.termOfServiceForm.personId() == 0) {
                jQuery("#term-detail-modal").modal('hide');
            }
        };


        newTerm = (person : INameValuePair) => {
            var me = this;
            var committee = me.committeeSelector.selected();
            me.termOfServiceForm.clear();
            me.termOfServiceForm.personId(Number(person.Value));
            me.termOfServiceForm.name(person.Name);
            me.termOfServiceForm.committeeId = committee.Value;
            me.termOfServiceForm.committeeName(committee.Name);

            me.termOfServiceForm.edit();
            me.showTermDetail();
        };

        private setPageView(view : string) {
            var me = this;
            me.pageView(view);
            if (view == 'forms') {
                me.application.setPageHeading('Committees');
                
            }
            else {
                me.application.setPageHeading('Committee Members and Nominations');
                me.reportDate(Dates.getCurrentDateString());
            }
        }
        
        runReport = () => {
            var me = this;
            me.application.showWaiter('Running committee report...');
            me.peanut.executeService('GetCommitteeReport', null,
                function (serviceResponse:IServiceResponse) {
                    me.application.hideWaiter();
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        me.reportResponse = <ICommitteeReportItem[]>serviceResponse.Value;
                        me.showReportOptions();
                    }
                }
            ).fail(function () {
                me.application.hideWaiter();
            });
        };

        closeReport = () => {
            var me = this;
            me.setPageView('forms');
        };
        
        showReportOptions = () => {
            var me = this;
            jQuery('#report-options-modal').modal('show');            
        };
        
        applyReportOptions = () => {
            var me = this;
            me.report.current([]);
            me.report.nominated([]);
            var list = me.reportResponse;

            if (me.reportOptions.committeeFilter() != 'all') {
                var id = me.committeeForm.committeeId();
                list = _.filter(list,function (item: ICommitteeReportItem) {
                    return item.committeeId == id;
                });
            }

            if (me.reportOptions.currentMembers()) {
                let filtered : ICommitteeReportItem[] = _.filter(list,function (item: ICommitteeReportItem) {
                    return item.statusId == 3;
                });
                let current = me.buildReportObservable(filtered);
                me.report.current(current);
            }

            if (me.reportOptions.nominations()) {
                let filtered : ICommitteeReportItem[] =  _.filter(list,function (item: ICommitteeReportItem) {
                    return item.statusId != 3;
                });
                let nominations = me.buildReportObservable(filtered);
                me.report.nominated(nominations);
            }

            me.setPageView('reports');
            jQuery('#report-options-modal').modal('hide');
        };

        private buildReportObservable(selected: ICommitteeReportItem[]) : ICommitteeReportDisplayItem[] {
            var result : ICommitteeReportDisplayItem[] = [];
            var committeeId = 0;
            var len = selected.length;
            var observableItem : ICommitteeReportDisplayItem = null;
            for (var i = 0;i< len; i++) {
                let item:ICommitteeReportItem = selected[i];
                if (item.committeeId != committeeId) {
                    if (observableItem !== null) {
                        result.push(observableItem);
                    }
                    observableItem = {
                        committeeName: item.committeeName,
                        members: []
                    };
                    committeeId = item.committeeId;
                }
                observableItem.members.push(item);
            }
            if (observableItem !== null) {
                result.push(observableItem);
            }

            return result;
        }

        handleEvent = (eventName:string, data?:any)=> {
            var me = this;
            switch (eventName) {
                case 'person-selected' :
                    me.newTerm(<INameValuePair>data.person);
                    break;
                case 'person-search-cancelled' :
                    // alert('Search cancelled.');
                    break;
            }
        };

    }
}

// Tops.TkoComponentLoader.addVM('component-name',Tops.committeesPageComponent);

