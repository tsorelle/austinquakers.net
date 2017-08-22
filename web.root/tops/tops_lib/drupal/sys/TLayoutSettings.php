<?php
/*****************************************************************
Class:  TLayoutSettings
Description:
*****************************************************************/
class TLayoutSettings
{
    private static $layouts = array();
    private static $config;

    private static function getConfig() {
        if (!isset(TLayoutSettings::$config)) {
            $path = TClassLib::GetSiteFile('config/layout.ini');
            TLayoutSettings::$config = new TConfiguration($path);
        }
        return TLayoutSettings::$config;
    }


    private static function getNodeSettings($section, $isTeaser) {
        $config = TLayoutSettings::getConfig();
        $result = new stdClass();

        if ($config->sectionExists($section)) {
            $result->showTitle =   $config->getFlagValue($section,'showTitle');
            $result->showSubmitted = $config->getFlagValue($section,'showSubmitted');
            $result->showSubmittedBelow = $config->getFlagValue($section,'showSubmittedBelow');
            $result->showLinks = $config->getFlagValue($section,'showLinks');
            $result->linkTitle = $config->getFlagValue($section,'linkTitle',1);
        }
        else {
            $result->showTitle = true;
            $result->showSubmitted = true;
            $result->showLinks = true;
            $result->showSubmittedBelow = false;
            $result->linkTitle = true;
        }
        if ($isTeaser && $result->showLinks && $result->showTitle)
            $result->wrapperClass = $config->getValue($section,'wrapperClass','storyNode');
        else
            $result->wrapperClass = $config->getValue($section,'wrapperClass','pageNode');
        /*
        TTracer::Assert($result->showTitle,'showTitle');
        TTracer::Assert($result->showSubmitted,'showSubmitted');
        TTracer::Assert($result->showLinks,'showLinks');
        TTracer::Assert($result->showSubmittedBelow,'showSubmittedBelow');
        */

        return $result;
    }

    private static function getPageSettings($section) {
        $config = TLayoutSettings::getConfig();
        $result = new stdClass();
        if ($config->sectionExists($section)) {
            $result->showTitle =   $config->getFlagValue($section,'showTitle',1);
            $result->showTabs = $config->getFlagValue($section,'showTabs',1);
        }
        else {
            $result->showTitle = true;
            $result->showTabs = true;
        }
        return $result;
    }

    private static function getCommentSettings($section) {
        $config = TLayoutSettings::getConfig();
        $result = new stdClass();
        if ($config->sectionExists($section)) {
            $result->showPicture =   $config->getFlagValue($section,'showPicture',0);
            $result->wrapperClass =   $config->getValue($section,'wrapperClass','');
        }
        else {
            $result->showPicture = false;
            $result->wrapperClass = '';
        }
        return $result;
    }


    public static function GetNodeLayout($node, $isTeaser) {
//        TTracer::On();
        if ($node->sticky && $node->promote == 1) {
            $section = "node.page.lead";
        }
        else
            $section = $isTeaser ?
                'node.'.$node->type.'.teaser' :
                'node.'.$node->type.'.body';

// TTracer::Trace("section = $section");
        if (isset(TLayoutSettings::$layouts[$section]))
            return TLayoutSettings::$layouts[$section];
        $result = TLayoutSettings::getNodeSettings($section, $isTeaser);
        TLayoutSettings::$layouts[$section] = $result;
        return $result;
    }

    public static function GetPageLayout($type) {
        $section = "node.$type.page";
        if (isset(TLayoutSettings::$layouts[$section]))
            return TLayoutSettings::$layouts[$section];
        $result = TLayoutSettings::getPageSettings($section);
        TLayoutSettings::$layouts[$section] = $result;
        return $result;
    }

    public static function GetCommentLayout($type) {
        $section = "node.$type.comment";
        if (isset(TLayoutSettings::$layouts[$section]))
            return TLayoutSettings::$layouts[$section];
        $result = TLayoutSettings::getCommentSettings($section);
        TLayoutSettings::$layouts[$section] = $result;
        return $result;
    }
}
// end TLayoutSettings



