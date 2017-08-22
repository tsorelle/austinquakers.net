/**
 * Created by Terry on 10/30/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='TkoComponentLoader.ts' />
module Tops {
    export class UserComponent {
        firstName : KnockoutObservable<string>;
        lastName : KnockoutObservable<string>;
        fullName: KnockoutComputed<string>;
        constructor(params : any) {
            var me = this;
            me.firstName  = ko.observable(params.first);
            me.lastName = ko.observable(params.last);
            me.fullName = ko.pureComputed(function() {
                return me.firstName() + ' ' + me.lastName();
            },me);
        }
    }
}
Tops.TkoComponentLoader.addVM('user',Tops.UserComponent);

