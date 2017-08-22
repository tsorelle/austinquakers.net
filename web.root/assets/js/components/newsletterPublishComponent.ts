/**
 * Created by Terry on 6/6/2016.
 */
///<reference path="searchListObservable.ts"/>
///<reference path="personSelectorComponent.ts"/>
///<reference path="selectListObservable.ts"/>
///<reference path="CKEditorControl.ts"/>
/**
 * Created by Terry on 5/20/2016.
 */
/// <reference path='../typings/knockout/knockout.d.ts' />
/// <reference path='../typings/underscore/underscore.d.ts' />
/// <reference path='../typings/bootstrap/bootstrap.d.ts' />
/// <reference path="../Tops.App/App.ts" />
/// <reference path="../Tops.Peanut/Peanut.ts" />
/// <reference path='../Tops.Peanut/Peanut.d.ts' />
/// <reference path='../typings/jqueryui/jqueryui.d.ts' />

module Tops {
    interface INewsletterResponse {
        issueDate: string;
        messageText: string;
    }

    export class newsletterPublishComponent {
        private application:IPeanutClient;
        private peanut:Peanut;

        private formData: FormData = null;
        
        publicationDate = ko.observable('');
        messageText = ko.observable('');
        testMode = ko.observable(true);
        dateInitialized = false;
        fileStatus = ko.observable('upload');
        fileSelected = ko.observable(false);
        acceptDate : KnockoutComputed<boolean>;
        issueDate : string = '';

        public constructor(application:IPeanutClient)  { // , owner:IEventSubscriber = null) {
            var me = this;
            me.application = application;
            me.peanut = application.peanut;

            me.acceptDate = ko.computed(function () {
               return me.fileSelected() || me.fileStatus() == 'uploaded';
            });
            // me.owner = owner;
        }

        public initialize(finalFunction?:() => void) {
            var me = this;
            me.application.setPageTitle('Friendly Notes');
            if (finalFunction) {
                finalFunction();
            }
        }

        sendFile = () => {
            var me = this;
        };

        sendMessage = () => {
            var me = this;
            var request = {
                subject: 'Friendly Notes ' + me.issueDate,
                listCode: 'fmanotes',
                messageText: me.messageText(),
                test: me.testMode()
            };

            me.application.showWaiter('Sending Friendly Notes messages . . .');
            me.peanut.executeService('SendToEmailList', request,
                function (serviceResponse: IServiceResponse) {
                    if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                        if (!me.testMode()) {
                            me.clearForm();
                        }
                    }
                }).always(
                    function () {
                        me.application.hideWaiter();
                    }
                );
        };

        getMessageText = () => {
            var me = this;
            me.messageText('');
            me.issueDate = '';
            me.application.hideServiceMessages();
            if (me.fileStatus() == 'upload') {
                me.application.showWaiter('Uploading . . .');
                me.formData.append('publicationDate', me.publicationDate());
                me.peanut.postServiceForm(
                    'PublishFriendlyNotes', me.formData,
                    function (serviceResponse:IServiceResponse) {
                        if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                            var response = <INewsletterResponse>serviceResponse.Value;
                            me.messageText(response.messageText);
                            me.issueDate = response.issueDate;
                        }
                    }
                ).always(
                    function () {
                        me.application.hideWaiter();
                        me.clearUpload();
                    }
                );
            }
            else {
                 var request =  me.publicationDate();
                 me.application.showWaiter('Getting message text . . .');
                 me.peanut.executeService('GetFriendlyNotesMessage', request,
                    function (serviceResponse: IServiceResponse) {
                        if (serviceResponse.Result == Peanut.serviceResultSuccess) {
                            var response = <INewsletterResponse>serviceResponse.Value;
                            me.messageText(response.messageText);
                            me.issueDate = response.issueDate;
                        }
                 }).always(
                     function () {
                         me.application.hideWaiter();
                     }
                 );
            }
        };

        clearForm = () => {
            var me = this;
            me.formData = null;
            me.messageText('');
            me.publicationDate('');
            me.issueDate = '';
            me.initDate();
            me.fileSelected(false);
            return true;
        };

        clearUpload = () => {
            var me = this;
            me.formData = null;
            $("#fileselect").val("");
            me.fileSelected(false);
            return true;
        };



        initDate = () => {
            var me = this;
            if (!me.dateInitialized) {
                // var dateValue = Dates.getCurrentDateString('usdate')
                // me.publicationDate(dateValue);
                jQuery(function() {
                    jQuery( ".datepicker" ).datepicker({
                        changeYear: true
                    });
                });

                me.dateInitialized = true;

            }
            return true;
        };

        onFileSelected = () => {
            var me = this;
            me.formData = null;
            me.fileSelected(false);
            me.messageText('');
            me.publicationDate('');
            var e = <any>document.getElementById('fileselect');
            var file = e.files[0];
            if (file) {
                if (file.type != 'application/pdf') {
                    me.application.showError('The file must be a pdf');
                    $("#fileselect").val("");
                    return;
                }
                var type = file.type;
                // if (!file.type.match())
                me.formData = new FormData();
                me.formData.append('file',file);
                me.initDate();
                me.fileSelected(true);
            }
        };
    }
      
}