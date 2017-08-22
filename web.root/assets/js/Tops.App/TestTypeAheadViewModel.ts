/**
 * Created by Terry on 5/15/2015.
 */
/**
 * Created by Terry on 5/26/14.
 */
///<reference path='../typings/knockout/knockout.d.ts' />
///<reference path='../typings/jquery/jquery.d.ts' />
///<reference path='../typings/underscore/underscore.d.ts' />
///<reference path='../typings/typeahead/typeahead.d.ts' />
///<reference path="./App.ts" />
///<reference path="../Tops.Peanut/Peanut.ts" />
// Module
module Tops {
    export class TestTypeAheadViewModel {
        static instance:Tops.TestTypeAheadViewModel;
        // private application: Tops.Application;
        // private peanut: Tops.Peanut;

       private states = [
            {name: 'Alabama', code: 'AL'},{name: 'Alaska', code: 'AK'},
            {name: 'Texas', code: 'TX'},{name: 'Tennessee', code: 'TN'}
        ];

        selectedState : KnockoutObservable<any>;
        dropdown: any;

        // Constructor
        constructor() {
            var me = this;
            me.selectedState = ko.observable();
            Tops.TestTypeAheadViewModel.instance = me;
        }

        init(applicationPath: string) {
            var me = this;
            // me.application.setApplicationPath(applicationPath);
            // me.clearPerson();
            me.dropdown = jQuery('#state');
            me.dropdown.typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 1
                },
                {
                    name: 'states',
                    source: me.statesFilter(me.states)
                });
            me.dropdown.on('blur',me.onStateChange);

            me.setState('TXX');
        }

        setState(code: string) {
            var me = this;
            var state = _.find(me.states, function (s : any) {
                    return s.code == code;
                }
            );
            if (state) {
                me.selectedState(state);
                me.dropdown.val(state.name);
            }
            else {
                me.selectedState(null);
                me.dropdown.val('');

            }

        }



        public onStateChange = ():void =>  {
            var me = this;

            //var name =  $('#state').val();
            var name =  me.dropdown.val();
            var state = _.find(me.states, function (s : any) {
                    return s.name == name;
                }
            );
            me.selectedState(state);

        }

        public onButtonClick() : void  {
            var me = this;
            var state = me.selectedState();
            var msg = state ? 'State: ' + state.name + " (" + state.code + ")" : "State not found";
            alert(msg);
        }

        public statesFilter = function(states: any) {
            return function findMatches(q, cb) {
                // regex used to determine if a string contains the substring `q`
                var substrRegex = new RegExp(q, 'i');
                var selected = [];
                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each( states,
                    function (i, s) {
                        if (substrRegex.test(s.name)) {
                            selected.push((s.name));
                        }
                    }
                );
                cb(selected);
            };
        };


    }
}

Tops.TestTypeAheadViewModel.instance = new Tops.TestTypeAheadViewModel();
(<any>window).ViewModel = Tops.TestTypeAheadViewModel.instance;