<?php
/** Class:TScriptBlock ***************************************/
///  Render a script block for javaScript
/*******************************************************************/
class TScriptBlock extends TUIComponent
{
    private $content = array();

    public function render()
    {
        if (count($this->content) == 0)
            return '';

        $result = '<script LANGUAGE="JavaScript">'."\n".'<!-- // Activate cloak'."\n";

        foreach($content as $text)
            $result .= $text."\n";

        return $result .'// Deactivate cloak -->'."\n".'</script>'."\n";
    }  //  render

    public function add($text)
    {
        array_push($this->content,$text);
    }  //  add

}   // finish class TScriptBlock


