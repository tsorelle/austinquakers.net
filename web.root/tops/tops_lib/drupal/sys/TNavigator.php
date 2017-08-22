<?php
/** Class: TNavigator ***************************************/
/// renders page navagation links
/**
*****************************************************************/
class TNavigator
{
    public function __construct() {
    }

    public function __toString() {
        return 'TNavigator';
    }

    public static function AppendBreadCrumb($text, $href, $hint='') {
        if (!empty($hint))
            $hint = sprintf(' title="%s"',$hint);
        $link = sprintf('<a href="%s"%s>%s</a>',$href,$hint,$text);
        $crumbs = drupal_get_breadcrumb();
        array_push($crumbs, $link);
        drupal_set_breadcrumb($crumbs);
    }

    private static function setCrumbs($setting) {
        $crumbs = explode('::',$setting);
        if (empty($crumbs))
            return;
        TTracer::ShowArray($crumbs);
        foreach($crumbs as $crumb) {
            $parts = explode(',',$crumb);
            TTracer::ShowArray($parts);
            $count = sizeof($parts);
            if ($count > 0)
                self::AppendBreadCrumb(
                    $parts[0],
                    $count < 2 ? '' : $parts[1],
                    $count < 3 ? '' : $parts[2]);
        }

    }

    public static function SetNodeBreadCrumb($contentType) {
        // TTracer::Trace('SetBreadCrumb');
        $config = TConfiguration::GetSettings('navigation');
        $setting = $config->getValue('node',$contentType,'');
        if (!empty($setting))
            self::setCrumbs($setting);
        //TTracer::Trace("Type = $contentType; node setting = $setting");

        $setting = $config->getValue('page',substr($_SERVER['QUERY_STRING'], 2),'');
        if (!empty($setting))
            self::setCrumbs($setting);

        //TTracer::Trace("page setting = $setting");

    }
}



// end TNavigator