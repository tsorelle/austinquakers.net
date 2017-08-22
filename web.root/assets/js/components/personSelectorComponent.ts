/**
 * Created by Terry on 5/24/2016.
 */
/// <reference path="../Tops.App/App.ts" />
///<reference path="searchListObservable.ts"/>
///<reference path="../Tops.Peanut/Peanut.ts"/>
/// <reference path='../typings/knockout/knockout.d.ts' />
///<reference path="committeeObservable.ts"/>
/// <reference path='../typings/knockout/knockout.d.ts' />
module Tops {
    export class personSelectorComponent {
        private application:IPeanutClient;
        private peanut:Peanut;
        private owner:IEventSubscriber;
        private selectorId : string;

        personsList:ISearchListObservable;

        public headerText:KnockoutObservable<string>;
        public bodyText:KnockoutObservable<string>;
        public modalId:KnockoutObservable<string>;

        constructor(application:IPeanutClient, owner:IEventSubscriber, modalId: string = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;
            me.owner = owner;
            if (!modalId) {
                modalId = 'persons-search-modal';
            }
            me.modalId = ko.observable(modalId);
            me.selectorId = '#' + modalId;
            me.headerText = ko.observable('Find and select a person');
            // me.headerText = (typeof params.headerText == 'string') ?  ko.observable(params.headerText) : params.headerText;
        }
        public initialize(finalFunction? : () => void) {
            var me = this;
            me.personsList = new searchListObservable(2, 6, 'person');
            if (finalFunction) {
                finalFunction();
            }
        }
        
        reset = () => {
            var me = this;
            me.personsList.reset();
        };

        findPersons = () => {
            var me = this;
            var request = me.personsList.searchValue();
            me.application.hideServiceMessages();
            me.peanut.executeService('FindPersons', request,
                function (serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        var list = <INameValuePair[]>serviceResponse.Value;
                        me.personsList.setList(list);
                    }
                    else {
                        me.hide();
                    }
                }
            );
        };
        
        selectPerson = (personItem:INameValuePair)=> {
            var me = this;
            me.hide();
            if (me.owner) {
                me.owner.handleEvent('person-selected',
                    {person: personItem, modalId: me.modalId()}
                )
            }
        };

        show = () => {
            var me = this;
            me.reset();
            jQuery(me.selectorId).modal('show');
        };

        hide = () => {
            var me = this;
            jQuery(me.selectorId).modal('hide');
        };
        
        
        cancelSearch = () => {
            var me = this;
            me.hide();
            if (me.owner) {
                me.owner.handleEvent('person-search-cancelled', me.modalId);
            }
        };
        
    }
}
// Tops.TkoComponentLoader.addVM('person-selector',Tops.personSelectorComponent);