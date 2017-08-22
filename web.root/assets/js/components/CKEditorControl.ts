///<reference path="../typings/knockout/knockout.d.ts"/>
///<reference path="../typings/ckeditor/ckeditor.d.ts"/>
/**
 * Created by Terry on 5/27/2016.
 */
module Tops {
    /**
     * Wrapper class for CkEditor
     */
    export class CKEditorControl {
        private editor : any = null;
        private observable : KnockoutObservable<string>;
        private elementId: string;

        constructor(observable: KnockoutObservable<string>,elementId:string) {
            var me = this;
            me.observable = observable;
            me.elementId = elementId;
        }

        public show() {
            var me = this;
            if (me.editor == null) {
                me.editor = CKEDITOR.replace(me.elementId);
            }
            me.editor.setData(me.observable());
        }

        public getValue() {
            var me = this;
            var value = me.editor.getData();
            me.observable(value);
            return value;
        }
    }
}
