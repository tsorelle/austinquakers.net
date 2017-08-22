<?php
function _simpleton_ispage() {
    $q = $_GET['q'];
    $parts = explode('/',$q);
    $length = count($parts);
    return ($length == 2 && $parts[0] == 'node');
}

function _simpleton_canEditView($variables) {
    if (array_key_exists('is_admin',$variables) && $variables['is_admin']) {
        return true;
    }
    if (array_key_exists('user',$variables)) {
        $usr = $variables['user'];
        if (isset($usr->roles)) {
            return in_array('moderator',$usr->roles);
        }
    }
    return false;
}

function simpleton_preprocess_page(&$variables) {
    $variables['vieweditlink'] = '';
    if (_simpleton_ispage()) {
        $variables['closure'] = '';
        $variables['viewmodel'] = TViewModel::getVmPath();

        if (_simpleton_canEditView($variables)) {
            $variables['vieweditlink'] = '<p><a style="font-size: smaller" href="/'.$_GET['q'] . '/edit">[Edit source]</a></p>';
        }
    }
}

function simpleton_preprocess_node(&$variables) {
    if ($variables['page'] && _simpleton_ispage()) {
            $variables['links'] = '';
            // $variables['closure'] = '';
            $variables['submitted'] = '';
            $url = $variables['node_url'];
    }

}

