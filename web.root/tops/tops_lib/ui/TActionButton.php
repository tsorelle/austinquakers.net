<?php
/** Class: TActionButton ***************************************/
/// A renderable component for a submit button
/**
* TAction button support application processing by paring the
* submit button with a hidded field indicating the command code
* to drive the application script.
*
* This functionality is supported in the TRequest class: GetCommandId(),
* getCommand(), and findButtonCommand() methods.
*****************************************************************/
class TActionButton extends TUIComponent
{
    private $name;
    private $action;
    private $text;

    /// Constructor

    public function __construct(
        /// Name of submit button
        $name,
        /// Command code
        $action,
        /// Label on the button
        $text ) {

        $this->name =    $name;
        $this->action =  $action;
        $this->text =    $text;
    }

    /// Render submit button and corresponding hidden field.
    /**
    * Example: <br/>
    * Where name = 'cancel', text='Cancel', action='returnToMain'<br/>
    * Renders<br/><pre>
    *     &lt;input type ="submit" name="cancelButton" value="Cancel" /&gt;
    *     &lt;input type ="hidden" name="onCancel" value="returnToMain" /&gt;
    * </pre>
    * If the button is clicked a call to TRequest::GetCommandID() returns 'returnToMain'
    */
    public function render() {
         return sprintf(
             '<input type="submit" name="%sButton" value="%s" />'.
             '<input type="hidden" name="on%s" value="%s" />'."\n\n",
             $this->name, $this->text, ucfirst($this->name), $this->action);
    }
}
// end TActionButton



