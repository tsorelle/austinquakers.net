/**
 * Created by Terry on 5/26/2015.
 */

///<reference path='../typings/underscore/underscore.d.ts' />
module Tops {
    export class finder {
        private p = 3;
        findme(a : number[]) {
            var me = this;
            var r = _.filter(
                a,
                function(i) {
                    return i % this.p == 0;
                }, me
            );

            return r;

        }
    }
}