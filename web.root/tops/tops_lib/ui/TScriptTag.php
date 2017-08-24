<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/23/2017
 * Time: 6:38 AM
 */

class TScriptTag extends TUIComponent
{
    private $src;

    public static function Create($src) {
        return new TScriptTag($src);
    }

    public function __construct($src)
    {
        $this->src = $src;
    }

    public function render()
    {
        return "<script src='$this->src'></script>";
    }
}