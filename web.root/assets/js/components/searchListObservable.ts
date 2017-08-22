/**
 * Created by Terry on 10/26/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
module Tops {
    export interface ISearchListObservable {
        searchValue : KnockoutObservable<string>;
        searchResults : KnockoutObservable<string>;
        selectionCount : KnockoutObservable<number>;
        hasMore : KnockoutObservable<boolean>;
        hasPrevious : KnockoutObservable<boolean>;
        selectionList : any[];
        columnCount : number;
        maxInColumn : number;
        itemsPerPage: number;
        currentPage : number;
        lastPage : number;
        itemList: INameValuePair[];
        foundCount : KnockoutObservable<number>;
        reset : () => void;
        setList : (list : Tops.INameValuePair[]) => void;
        nextPage : ()=> void;
        previousPage : ()=> void;
        
    }

    // * Base class for observable container that handles search results.
    export class searchListObservable implements ISearchListObservable {
        searchValue = ko.observable('');
        selectionCount = ko.observable(0);
        hasMore = ko.observable(false);
        hasPrevious = ko.observable(false);
        selectionList = [];
        columnCount : number;
        maxInColumn : number;
        itemsPerPage: number;
        currentPage : number = 1;
        lastPage : number;
        itemList: INameValuePair[];
        foundCount = ko.observable(0);
        searchResults = ko.observable('');
        itemName : string = 'item';
        itemPlural : string = 'items';

        public constructor(columnCount : number, maxInColumn: number, itemName: string = 'item', itemPlural: string = null ) {
            var me = this;
            me.itemName = itemName;
            me.itemPlural = itemPlural ? itemName + itemPlural : itemName + 's';
            me.columnCount = columnCount;
            me.maxInColumn = maxInColumn;
            me.itemsPerPage = columnCount * maxInColumn;
            for (var i=1;i<=columnCount;i++) {
                me.selectionList[i] = ko.observableArray<INameValuePair>([]);
            }
        }


        /**
         * reset search observables
         */
        public reset() {
            var me = this;
            me.searchValue('');
            me.selectionCount(0);
            me.foundCount(0);
            me.searchResults('');
        }

        /**
         *  Assign result list
         */
        public setList(list : Tops.INameValuePair[]) {
            var me = this;
            me.itemList = list;
            var itemCount = list.length;
            if (itemCount == 1 && list[0].Value === null) {
                me.foundCount(itemCount - 1);
            }
            else {
                me.foundCount(itemCount);
            }
            me.selectionCount(itemCount);
            if (itemCount == 1) {
                me.searchResults('One ' + me.itemName + ' found.')
            }
            else if (itemCount == 0) {
                me.searchResults('No ' + me.itemPlural + ' found.');
            }
            else {
                me.searchResults(itemCount + ' ' + me.itemPlural+ ' found.');
            }
                        
            me.currentPage = 1;
            me.lastPage = Math.ceil(itemCount / me.itemsPerPage);
            me.hasMore(me.lastPage > 1);
            me.hasPrevious(false);
            me.parseColumns();
        }

        /**
         * handle next-page click
         */
        public nextPage = ()=> {
            var me = this;
            if (me.currentPage < me.lastPage) {
                me.currentPage = me.currentPage + 1;
            }

            me.hasMore(me.lastPage > me.currentPage);
            me.hasPrevious(me.currentPage > 1);
            me.parseColumns();
        };

        /**
         * handle previous-page click
         */
        public previousPage = ()=> {
            var me = this;
            if (me.currentPage > 1) {
                me.currentPage = me.currentPage - 1;
            }
            me.hasMore(me.lastPage > me.currentPage);
            me.hasPrevious(me.currentPage > 1);
            me.parseColumns();
        };

        /**
         * arrange list in observable columns
         */
        private parseColumns() {
            var me = this;
            var columns = [];
            var currentColumn = 0;
            var startIndex = me.itemsPerPage * (me.currentPage - 1);
            var lastIndex = startIndex + me.itemsPerPage;
            if (lastIndex >= me.itemList.length) {
                lastIndex = me.itemList.length - 1;
            }
            columns[0] = [];
            var j = 0;
            for(var i = startIndex; i <= lastIndex; i++) {
                columns[currentColumn][j] = me.itemList[i];
                if ((i+1) % me.maxInColumn == 0) {
                    ++currentColumn;
                    columns[currentColumn] = [];
                    j=0;
                }
                else {
                    j=j+1;
                }
            }
            for (var i=0; i< me.columnCount; i++) {
                me.selectionList[i+1](columns[i]);
            }
        }
    }
}