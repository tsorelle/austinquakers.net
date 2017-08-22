/**
 * Created by Terry on 5/26/14.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
/// <reference path="./App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
// Module
module Tops {
    // replace all occurances of 'yourVmName' with the name of your model
    //  e.g.  yourVmName -> billingConfiguration  produces billingConfigurationViewModel
    export class testapp1ViewModel {
        static instance: Tops.testapp1ViewModel;
        private application: Tops.Application;
        private peanut: Tops.Peanut;

        // Constructor
        constructor() {
            var me = this;

            Tops.testapp1ViewModel.instance = me;
            me.application = new Tops.Application(me);
            me.peanut = me.application.peanut;
        }

        person: KnockoutObservable<any> = ko.observable();
        // Declarations
        // Examples:
        //  templateList: KnockoutObservableArray = ko.observableArray([]);
        //  currentPage: KnockoutObservableString = ko.observable("");


        // Methods

        test() { alert("hello"); }

        init(applicationPath: string) {
            var me = this;
            alert('Initializing');
            me.application.setApplicationPath(applicationPath);
            me.clearPerson();
        }

        clearPerson() {
            var me = this;
            var initPerson =
            {
                id : 0,
                Name: "",
                Gender: "",
                Status: "New",
                Age: 0
            };
            me.person(initPerson);
        }

        onUpdatePerson() {
            var me = this;

            me.application.hideServiceMessages();
            // me.application.showErrorMessage("Testing error messages: updatePerson");

            me.peanut.executeService('UpdatePerson', me.person(),
                function (serviceResponse: Tops.IServiceResponse) {
                    if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {

                        me.person(serviceResponse.Value);

                    }
                    else {
                        alert("Service failed");
                        me.clearPerson();
                    }
                }
            ).always(
                function() {
                    alert("Service complete.");
                }
            );
        }

        onGetPerson() {
            var me = this;
            me.application.hideServiceMessages();
            // me.application.showInfoMessage("Testing info messages: getPerson");


            me.peanut.getFromService('GetPerson', 1,
                function (serviceResponse: Tops.IServiceResponse) {
                    if (serviceResponse.Result == Tops.Peanut.serviceResultSuccess) {
                        me.person(serviceResponse.Value);
                    }
                    else {
                        alert("Service failed");
                        me.clearPerson();
                    }
                });


        }
    }
}

Tops.testapp1ViewModel.instance = new Tops.testapp1ViewModel();
(<any>window).ViewModel = Tops.testapp1ViewModel.instance;