///<reference path="../components/newsletterPublishComponent.ts"/>
/**
 * Created by Terry on 6/6/2016.
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
    export class publishnewsletterViewModel {
        static instance: Tops.publishnewsletterViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;
        private newsletterComponent: newsletterPublishComponent;

        // Constructor
        constructor() {
            var me = this;
            Tops.publishnewsletterViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        // Methods
        init(applicationPath: string, successFunction?: () => void) {
            var me = this;
            me.application.startup(applicationPath,'Ko',
                function() {
                    me.application.showWaiter('Initializing...');
                    me.application.loadResources("newsletterPublishComponent.js", function () {
                        me.newsletterComponent = new newsletterPublishComponent(me.application);
                        me.application.registerComponent('newsletter-publish',me.newsletterComponent,
                            function () {
                                me.newsletterComponent.initialize(function () {
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

Tops.publishnewsletterViewModel.instance = new Tops.publishnewsletterViewModel();
(<any>window).ViewModel = Tops.publishnewsletterViewModel.instance;
