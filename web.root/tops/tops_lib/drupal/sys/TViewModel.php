<?php
/**
 * Created by PhpStorm.
 * User: tsorelle
 * Date: 3/3/14
 * Time: 4:25 AM
 */

class TViewModel {
    private static $vmPaths = array();

    private static function getPageName() {
        $name = drupal_get_path_alias($_GET['q']);
        if (!strstr('/',$name)) {
            return $name;
        }
        return null;
    }

    public static function getVmPath() {
        $name = self::getPageName();
        if ($name && array_key_exists($name,self::$vmPaths)) {
            return self::$vmPaths[$name];
        }
        return null;
    }

    public static function Initialize() {
        // self::$vmPath = '';
        $name = self::getPageName();
        if ($name)
        {
            // $vmPath = "topsJS/Tops.App/$name".'ViewModel.js';
            $vmPath = "assets/js/Tops.App/$name".'ViewModel.js';
            $filepath = $_SERVER['DOCUMENT_ROOT'].'/'.$vmPath;
            $exists = @file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$vmPath);
            if ($exists) {
            // if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$vmPath)) {
                self::$vmPaths[$name] = $vmPath;
                self::addScripts();
                return true;
            }
            else {
                if (array_key_exists($name,self::$vmPaths)) {
                    unset(self::$vmPaths[$name]);
                }
            }
        }
        return false;
    }

    private static function addScripts() {
        // assume jquery already added.
        // drupal_add_js("assets/js/jquery-1.8.3.js");
        // drupal_add_js("misc/jquery.js"); // must be  jquery-1.8.3.js or up
        drupal_add_js("assets/js/json2.js");
        drupal_add_js("assets/js/knockout-2.2.1.js");
        drupal_add_js('assets/js/underscore-min.js');
        drupal_add_js("assets/js/Tops.Peanut/Peanut.js?tv=1.26");
        drupal_add_js("assets/js/Tops.App/App.js?tv=1.27");
        drupal_add_js("assets/js/head.load.min.js");
    }

    public static function RenderMessageElements() {
        if (self::getVmPath()) {
            print
                '<div id="errorMessages"><div id="errorText"></div></div>'."\n".
                '<div id="warningMessages"><div id="warningText"></div></div>'."\n".
                '<div id="infoMessages"><div id="infoText"></div></div>'."\n";
        }
    }

    public static function RenderStartScript($basepath='/') {
        $vmPath = self::getVmPath();
        if ($vmPath)
        {
                print '<script src="'.$basepath.$vmPath.'"'."></script>\n".
                       "<script>\n".
                        "ViewModel.init('/', function() { ko.applyBindings(ViewModel);  jQuery('#view-container').show();}); \n".
                        "</script>\n";
        }
    }
} 