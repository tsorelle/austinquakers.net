<?php

/**
* Converts a list of css link tags to import statements
* within a style block.  Seperate blocks are created for each
* Media type.  The purpose is to force old browsers such as
* Netscape 4 to ignore the style sheets and display the page
* as plain text.
*/
function stylesAsImports($styles) {
    $links = split("\n",$styles);
    $result = "\n";
    $prevMedia = '';
    foreach($links as $link) {
        if (!empty($link)) {
            $parts = split(' ',$link);
            $media = $parts[3];
            $href = $parts[4];
            if ($media != $prevMedia) {
                if (!empty($prevMedia)) {
                    $result .= "</style>\n";
                }
                $result .= sprintf("<style type=\"text/css\" %s>\n",$media);
                $prevMedia = $media;
            }

            $result .= sprintf("    @import %s;\n", str_replace('"',')',str_replace('href="','url(',$href)));
        }
    }
    return $result."</style>\n";;
}

function topsy_blogger() {

    $parts = split('/',$_GET['q']);

    if (sizeof($parts) == 2 && $parts[0] == 'blog')
        return $parts[1];
    return 0;
}

/***
*  topsy_preprocess_page
* -----------------------------
*  Layout is based on the Yahoo UI Grids style sheets
*  Overall page width and template is sent here as:
*      $pageWidthClass and $docId
*  Column templates are set in include files:
*          layout_right.inc
*          layout_left.inc
*          content_main.inc (for a single column)
*  The layout files include content include files:
*          content_main.inc (main content)
*          content_navigation.inc (vertical menu)
*          content_extra.inc (secondary content)
*  For help with YUI grids see:
*          Documentation: http://developer.yahoo.com/yui/grids/
*          Design tool:   http://developer.yahoo.com/yui/grids/builder/
*          Cheat sheet:   http://yuiblog.com/assets/pdf/cheatsheets/css.pdf
*
*
**/
//function topsy_preprocess_page(&$variables) {
function topsy_preprocess_page(&$variables) {
    // inputs
    //krumo($variables);
    $bloggerId = topsy_blogger();
    TTracer::Trace("BloggerId = $bloggerId");
    if ($bloggerId) {
        $userView = TDrupalUserView::Create($bloggerId);
        if ($bloggerId == 1)
            $blogTitle = "The Web Clerk's Blog";
        else
             $blogTitle = $userView->GetFullName()."'s Blog" ;

        if (isset($blogTitle))
            $variables['title'] = $blogTitle;
       $variables['page_picture'] = $userView->getPicture('left');
    }
   $directory = $variables['directory'];
   $base_path = $variables['base_path'];
   $layout    = $variables['layout'];
    $styles = stylesAsImports($variables['styles']);
//    TTracer::ShowArray($styles);
    $variables['styles'] = $styles;

  TTracer::Trace('preprocess_page');
//   TTracer::Trace("directory = $directory");
//   TTracer::Trace("base_path = $base_path");
//   TTracer::Trace("layout = $layout");
//    TTracer::Assert(isset($variables['directory']),'directory found.');
//    TTracer::Assert((isset($variables['base_path'])),' base_path found.');
//    TTracer::Assert((isset($variables['layout'])),' layout found.');
//    TTracer::Assert( $variables['teaser'], 'is teaser');
//    TTracer::Assert((isset($variables['page'])),'page flag found.');
  // TTracer::Off();

    $includePath = $_SERVER['DOCUMENT_ROOT'].'/sites/all/themes/topsy/include';
    /*
    if ($base_path == '/')
        $includePath = $_SERVER['DOCUMENT_ROOT'].'/'.$directory;
    else
        $themePath = $base_path.$directory;
    */
    $orientation = $layout;
    // override left/right orientation for test only
    // for production change block locations
    // $orientation = 'right';

//    $docId = 'custom-doc'; // 845px -- see style sheet
//    $docId = 'doc'; // 750px - good for 800/600
//    $docId = 'doc3'; // 100%
//    $docId = doc2; //950px   -- for 1024
    $docId = 'doc4';// 974px -- for 1024
    if ($layout == 'both') {
        // 3 column
        $pageWidthClass = 'yui-t2';
//        $pageWidthClass = 'yui-t3';
        $themeInc = "$includePath/layout_three.inc";
    }
//    else if ($left) {
    else if ($orientation == 'left') {
        // 2 column left
        $pageWidthClass = 'yui-t2';
        $themeInc = "$includePath/layout_left.inc";
    }
//    else if ($right) {
    else if ($orientation == 'right') {
        // 2 column right
        $pageWidthClass = 'yui-t5';
        $themeInc = "$includePath/layout_right.inc";
    }
    else {
        // 1 column
        $pageWidthClass = 'yui-t7';
        $themeInc = "$includePath/layout_single.inc";
     }

    // exit("pageWidth $pageWidthClass; themeInc $themeInc");

//    $variables['tabs2'] = menu_secondary_local_tasks(); // from garland
    $variables['docId'] = $docId;
    $variables['themeInc'] = $themeInc;
    $variables['includePath'] = $includePath;
    $variables['pageWidthClass'] = $pageWidthClass;


    if (isset($variables['node'])) {
        $node = $variables['node'];
        $pageLayout = TLayoutSettings::GetPageLayout($node->type);
        $variables['showTitle'] =  ($pageLayout->showTitle && !empty($variables['title']));
        $variables['showTabs']  =  ($pageLayout->showTabs && !empty($variables['tabs']));;
    }
    else {
        $variables['showTitle']	    =  !empty($variables['title']);
        $variables['showTabs']	    =  !empty($variables['tabs']);
    }


   if (!(TUser::Authenticated() || TDrupalPageController::IsPagePublic() )) {
        $variables['content'] = 'You must log in to view this content.';
    }


}

function topsy_preprocess_node(&$variables) {
      // krumo($variables);
    $node = $variables['node'];
    if (!empty($variables['submitted'])) {
        $userView = TDrupalUserView::Create($variables['uid']);
        $variables['submitted'] = $userView->getSubmitMessage(
            $variables['created'],
            'By %s on %s');
    }


    TTracer::Trace('Topsy preprocess node type: '. $node->type);
//    TTracer::ShowArray($variables);
    // TTracer::Trace('Page = '.substr($_SERVER['QUERY_STRING'], 2));
    $isTeaser =  $variables['teaser'];
    $layout =  TLayoutSettings::GetNodeLayout($node,$isTeaser);
    if (!$isTeaser)
        TNavigator::SetNodeBreadCrumb($node->type);
    $variables['showTitle']	    =  $layout->showTitle && $variables['page'] == 0;
    $variables['showSubmitted'] =
        ($layout->showSubmitted && !empty($variables['submitted']));
    $variables['showSubmittedBelow'] =
        ($layout->showSubmittedBelow && !empty($variables['submitted']));
    $variables['showLinks']	    =  $layout->showLinks;
    $variables['wrapperClass']  = $layout->wrapperClass;
    if ($layout->showTitle && $layout->linkTitle) { // && $variables['page'] == 0) {
        $title = $variables['title'];
/*        $variables['title'] =
            sprintf('<a href="/?q=node/%d" title="%s">%s</a>',
                $node->nid,$title, $title);
*/
    }
}

/**
 * Format a username.
 *
 * @param $object
 *   The user object to format, usually returned from user_load().
 * @return
 *   A string containing an HTML link to the user's page if the passed object
 *   suggests that this is a site user. Otherwise, only the username is returned.
 */
function topsy_username($object) {

  if ($object->uid) {

    $name = TDrupalUser::GetFullUserName($object->uid, 20);

    if (user_access('access user profiles')) {
      $output = l($name, 'user/'. $object->uid, array('attributes' => array('title' => t('View user profile.'))));
    }
    else {
      $output = check_plain($name);
    }
  }
  else if ($object->name) {
    // Sometimes modules display content composed by people who are
    // not registered members of the site (e.g. mailing list or news
    // aggregator modules). This clause enables modules to display
    // the true author of the content.
    if (!empty($object->homepage)) {
      $output = l($object->name, $object->homepage, array('attributes' => array('rel' => 'nofollow')));
    }
    else {
      $output = check_plain($object->name);
    }

    $output .= ' ('. t('not verified') .')';
  }
  else {
    $output = variable_get('anonymous', t('Anonymous'));
  }

  return $output;
}

function topsy_node_submitted($node) {
    $submittedDate = format_date($node->created, 'custom', 'l F j, Y');
    $authorName = theme('username', $node);

  return t("By !username on !submitdate",
    array(
      '!username' => theme('username', $node),
      '!submitdate' => $submittedDate //format_date($node->created),
    ));
}

/**
 * Process variables for comment.tpl.php.
 *
 * @see comment.tpl.php
 * @see theme_comment()
 */
function topsy_preprocess_comment(&$variables) {
//    TTracer::On();
//    TTracer::Trace('quip comment uid='.$variables['comment']->uid);
    $nodeType = $variables['node']->type;
    $layout = TLayoutSettings::GetCommentLayout($nodeType);
    $userView = TDrupalUserView::Create($variables['comment']->uid);
    $showPicture = $layout->showPicture; // && !empty($variables['picture']);
    if ($showPicture) {
      $variables['picture'] = $userView->getPicture();
    }
    $variables['commentWrapperClass'] = $layout->wrapperClass;
    $variables['showPicture'] = $showPicture;
    $variables['submitted'] = $userView->getSubmitMessage(
        $variables['comment']->timestamp,
        'Comment by %s on %s');
}

function topsy_breadcrumb($breadcrumb) {
    if (!empty($breadcrumb)) {
        $crumbs = array();
        // eliminate null elements
        foreach ($breadcrumb as $crumb) {
            if (!(empty($crumb) || ($crumb == '<a href=""></a>'))) {
                array_push($crumbs,$crumb);
            }
        }
        if (!empty($crumbs))  {
            return sprintf('<div class="breadcrumb">%s</div>',implode(' &raquo; ',$crumbs));
        }
            //  Return a themed breadcrumb trail.
//	Alow you to customize the breadcrumb markup
//


    }
}


?>
