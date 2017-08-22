///<reference path="CKEditorControl.ts"/>
/**
 * Created by Terry on 5/19/2016.
 */
///<reference path="../typings/fma/committees.d.ts"/>
///<reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../components/editPanel.ts' />
///<reference path="selectListObservable.ts"/>
    
// Module
module Tops {
    export class termOfServiceObservable extends editPanel {

        committeeId : any;
        committeeMemberId: any;
        personId = ko.observable(0);
        committeeName = ko.observable('');
        name = ko.observable('');
        email = ko.observable('');
        phone = ko.observable('');
        startOfService= ko.observable('');
        endOfService= ko.observable('');
        dateRelieved = ko.observable('');
        notes= ko.observable('');
        dateAdded = ko.observable('');
        dateUpdated = ko.observable('');

        dateError = ko.observable('');


        role: selectListObservable;
        status: selectListObservable;
        backup: ITermOfServiceListItem = null;

        public constructor() {
            super();
            var me = this;
            me.role = new selectListObservable(null,[
                {Name:'member', Value: '1' },
                {Name:'clerk', Value: '2'},
                {Name:'co-clerk', Value: '3' },
                {Name:'convener', Value: '4' },
                {Name:'correspondent', Value: '5' },
                {Name:'ex officio', Value: '7' },
                {Name:'recorder', Value: '6' }],'1');

            me.status = new selectListObservable(null,[
                {Name:'nominated', Value: '1' },
                {Name:'first reading', Value: '2' },
                {Name:'approved', Value: '3' },
                {Name:'withdrawn', Value: '4' }],'1');
        }

        public clear = () => {
            var me = this;
            me.personId(0);
            me.committeeMemberId = 0;
            me.name('');
            me.email('');
            me.phone('');
            me.role.restoreDefault();
            me.status.restoreDefault();
            me.startOfService('');
            me.endOfService('');
            me.dateRelieved('');
            me.notes('');
            me.dateAdded('');
            me.dateUpdated('');
            me.hasErrors(false);
            me.dateError('');
        };
        
        public assign = (term : ITermOfServiceListItem, committeeName: string) => {
            var me = this;
            me.backup = term;
            me.committeeId = term.committeeId;
            me.committeeMemberId = term.committeeMemberId;
            me.personId(term.personId);
            me.committeeName(committeeName);
            me.name(term.name);
            me.email(term.email);
            me.phone(term.phone);
            me.startOfService(Dates.formatDateString(term.startOfService,'usdate'));
            me.endOfService(Dates.formatDateString(term.endOfService,'usdate'));
            me.dateRelieved(Dates.formatDateString(term.dateRelieved,'usdate'));
            me.notes(term.notes);
            me.dateAdded(term.dateAdded);
            me.dateUpdated(term.dateUpdated);
            me.hasErrors(false);
            me.dateError('');

            me.role.setValue(term.roleId);
            me.status.setValue(term.statusId);


            me.isAssigned = true;
        };

        public rollback() {
            var me = this;
            if (me.backup) {
                me.assign( me.backup, me.committeeName());
            }
            else {
                me.clear();
            }
        }
        
        public validate = () => {
            var me = this;
            me.hasErrors(false);
            me.dateError('');
            if (!me.startOfService()) {
                me.dateError('A start date is required.');
                me.hasErrors(true);
            }
            return !me.hasErrors();
        };
        
        getValues = () :ITermOfService => {
            var me = this;
            var statusId = me.status.getValue();
            var roleId = me.role.getValue();
            var result : ITermOfService = {
                committeeId: me.committeeId,
                committeeMemberId: me.committeeMemberId,
                personId :      me.personId(),
                statusId:       statusId,
                startOfService: Dates.formatDateString(me.startOfService(),'isodate'),
                endOfService:   Dates.formatDateString(me.endOfService(),'isodate'),
                dateRelieved:   Dates.formatDateString(me.dateRelieved(),'isodate'),
                roleId:         roleId,
                notes:          me.notes()
            };
            return result;
        };
    }
    export class committeeObservable extends editPanel {
        committeeId= ko.observable(0);

        description = ko.observable('');
        name = ko.observable('');
        mailbox = ko.observable('');
        active:KnockoutObservable<boolean> = ko.observable(true);
        isStanding:KnockoutObservable<boolean> = ko.observable(true);
        isLiaison:KnockoutObservable<boolean> = ko.observable(true);
        membershipRequired:KnockoutObservable<boolean> = ko.observable(true);
        notes = ko.observable('');
        dateAdded = ko.observable('');
        dateUpdated = ko.observable('');

        nameError = ko.observable('');
        descriptionError = ko.observable('');

        descriptionEditor : CKEditorControl;


        private backup : IFmaCommittee = null;

        public initialize(finalFunction: ()=>void) {
            var me = this;
            jQuery.getScript("//cdn.ckeditor.com/4.5.9/standard/ckeditor.js")
                .done(function () {
                    me.descriptionEditor = new CKEditorControl(me.description,'committee-description');
                    me.view();
                    if (finalFunction) {
                        finalFunction();
                    }
                })
                .fail(function () {
                    alert("ckeditor load failed.");
                });

        }

        public clear() {
            var me = this;
            me.committeeId(0);
            me.description('');
            me.active(true);
            me.name('');
            me.mailbox('');
            me.isLiaison(false);
            me.isStanding(false);
            me.membershipRequired(false);
            me.notes('');
            me.dateUpdated('');
            me.dateAdded('');
            me.hasErrors(false);
            me.nameError('');
            me.descriptionError('');
        }

        public assign(committee:IFmaCommittee) {
            var me = this;
            me.backup = committee;
            me.committeeId(committee.committeeId);
            me.name(committee.name);
            me.mailbox(committee.mailbox);
            me.active(committee.active);
            me.isStanding(committee.isStanding);
            me.isLiaison(committee.isLiaison);
            me.membershipRequired(committee.membershipRequired);
            me.description(committee.description);
            me.notes(committee.notes);
            me.dateAdded(committee.dateAdded);
            me.dateUpdated(committee.dateUpdated);
            me.hasErrors(false);
            me.nameError('');
            me.descriptionError('');
        }

        public rollback() {
            var me = this;
            if (me.backup) {
                me.assign(me.backup);
            }
            else {
                me.clear();
            }
        }

        public getValues = ():IFmaCommitteeUpdate => {
            var me = this;
            var description = me.descriptionEditor.getValue();
            me.description(description);
            var result:IFmaCommitteeUpdate = {
                committeeId: me.committeeId(),
                active: me.active(),
                mailbox: me.mailbox(),
                isStanding: me.isStanding(),
                isLiaison: me.isLiaison(),
                membershipRequired: me.membershipRequired(),
                name: me.name(),
                notes: me.notes(),
                description: description
            };

            return result;
        };

        public validate = () => {
            var me = this;

            me.hasErrors(false);
            if (me.committeeId()==0 && me.name().trim() === '') {
                me.nameError('Committee name is required.');
                me.hasErrors(true);
            }
            else {
                me.nameError('');
            }

            var description = me.descriptionEditor.getValue();
            if (description.trim() === '') {
                me.descriptionError('Committee description is required.');
                me.hasErrors(true);
            }
            else {
                me.descriptionError('');
            }

            return !me.hasErrors();
        };

        public  editMode() {
            var me = this;
            me.descriptionEditor.show();
            me.edit();
        }
    }
}