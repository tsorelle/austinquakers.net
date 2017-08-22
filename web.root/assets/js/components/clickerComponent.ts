/**
 * Created by Terry on 10/30/2015.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='TkoComponentLoader.ts' />
module Tops {
    export class clickerComponent {
        public onClick : ()=> void;
        message = ko.observable('');
        constructor(params : any) {
            var me = this;
            me.message(params.message);
            me.onClick = params.onClick;
        }
    }
}
Tops.TkoComponentLoader.addVM('clicker',Tops.clickerComponent);
