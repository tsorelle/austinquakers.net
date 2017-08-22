<?php
// $Id$
/**
* Template to display TOPS application main output
**/

/*
if (empty($variables))
    print '<p>No variables.</p>';
else {
    print '<pre>';
    print_r($variables);
    print '</pre>';
}
*/

if (isset($variables['pageController']))
    $pageController = $variables['pageController'];
else
    $pageController = 'controller not found.';


if (is_string($pageController)) {
    print '<p>'.$pageController.'</p>';
}
else {
    print $pageController->renderBeginForm();
    foreach($pageController->getMainContent() as $item) {
        if ($item instanceof TFilePath)
            include($item);
        else
            echo $item;
    }
    print $pageController->renderEndForm();
}

?>