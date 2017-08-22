/**
 * Created by Terry on 5/19/2016.
 */
///<reference path="../typings/fma/committees.d.ts"/>
///<reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
///<reference path="../typings/ckeditor/ckeditor.d.ts"/>
/// <reference path='../components/editPanel.ts' />
/// <reference path='../components/committeeObservable.ts' />
/// <reference path='../components/committeesPageComponent.ts' />

// Module
module Tops {


    export class committeesViewModel {
        static instance: Tops.committeesViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;
        private pageComponent: committeesPageComponent;

        // Constructor
        constructor() {
            var me = this;
            Tops.committeesViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        // Methods
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            me.application.startup(applicationPath,'Ko',
                // me.application.initialize(applicationPath,
                function() {
                    me.application.setPageTitle('FMA Committees');
                    me.application.showWaiter('Initializing...');
                    me.application.loadResources(["editPanel.js","committeeObservable.js","committeesPageComponent.js",
                        "searchListObservable.js","personSelectorComponent.js",'selectListObservable.js','CKEditorControl.js'],function () {
                        me.pageComponent = new committeesPageComponent(me.application);
                        me.application.registerComponent('committees-page',me.pageComponent,
                            function () {
                                me.pageComponent.initialize(function () {
                                    successFunction();
                                    me.application.hideWaiter();
                                });
                            }
                            );
                    });
                });
        }


    }
}

Tops.committeesViewModel.instance = new Tops.committeesViewModel();
(<any>window).ViewModel = Tops.committeesViewModel.instance;