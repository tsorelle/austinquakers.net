<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="<?php print $language->language ?>"
    lang="<?php print $language->language ?>"
    dir="<?php print $language->language ?>" >

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=7" />
  <meta name="googlebot" content="noarchive" />
  <title>
    <?php print $head_title; ?>
  </title>
  <?php print $head; ?>
  <style type="text/css" media="all">
      @import url(/tops/styles/yui/reset-fonts-grids.css);
      @import url(/tops/styles/yui/base-min.css);
  </style>
  <?php print $styles; ?>
  <?php TDrupalPageController::PrintCssImports();  ?>
  <script type="text/javascript"></script> <!-- avoid IE problems -->
  <?php print $scripts; ?>
  <?php TDrupalPageController::PrintScriptImports() ?>
</head>

<body class="<?php print $body_classes; ?>">
    <div id="<?php print $docId?>" class="<?php print $pageWidthClass?> pageFrame">
        <div id="hd"> <!-- YUI header container -->
          <div id="logo-title">

           <div id="logo-image">
                  <a href="/" title="Home" rel="home" id="logo">
                    <img src="/sites/all/themes/fma/images/cameosLeft1.gif" alt="Home" />
                  </a>
           </div>
           <div id="logo-image-right">
                  <a href="/" title="Home" rel="home" id="logo">
                    <img src="/sites/all/themes/fma/images/cameosright.gif" alt="Home" />
                  </a>
           </div>


            <div id="name-and-slogan">
              <?php if (!empty($site_name)): ?>
                <div id="site-name">
                    <h1>
                      <a href="<?php print $front_page ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
                    </h1>
                </div> <!-- site-name -->
              <?php endif; ?>

              <h2 id="site-slogan">
                  A site for members and attenders of Friends Meeting of Austin
              </h2>

            </div> <!-- /name-and-slogan -->
          </div> <!-- /logo-title -->

          <?php if (!empty($search_box)): ?>
            <!-- position search as block or uncomment this section
            <div id="search-box"><?php print $search_box; ?></div>  -->
          <?php endif; ?>

          <?php if (!empty($header)): ?>
            <div id="header-region">
              <?php print $header; ?>
            </div>
          <?php endif; ?>



          <?php if (!empty($banner)): ?>
            <div id="banner-region">
              <?php print $banner; ?>
            </div>
          <?php endif; ?>

        <div id="top-bar">



          <?php if (!empty($top_navigation)): ?>
            <div id="top-navigation-region" class="hmenu">
              <?php print $top_navigation; ?>
            </div>
          <?php endif; ?>

        </div> <!-- end top bar -->
        </div> <!-- end header container "hd" -->

        <!-- YUI Main page area -->
        <div id="bd">
            <?php include($themeInc);  ?>
        </div> <!-- end YUI bd -->

        <!-- YUI page footer area -->
        <div id="ft">
          <?php if (!empty($footer)): ?>
            <div id="footer-region">
              <?php print $footer; ?>
            </div>
          <?php endif; ?>

         <?php if (!empty($footer_links)): ?>
            <div id="footer-links-region" class="hmenu">
              <?php print $footer_links; ?>
            </div>
          <?php endif; ?>

         <?php if (!empty($end_block)): ?>
            <div id="end-block-region">
              <?php print $end_block; ?>
            </div>
          <?php endif; ?>




        </div> <!-- end YUI ft -->
    <?php
        TTracer::PrintMessages();
        /*
        $messages = TTracer::GetMessages();
        $count = count($messages);
        if ($count > 0) {
            firep($count.' trace messages');
            foreach ($messages as $message)
                firep($message);
        }
        */

    ?>

        <?php print $closure; ?>
    </div> <!-- end page frame "docId" -->

</body>
<?php TFooterScripts::Render(); ?>
<?php TViewModel::RenderStartScript(); ?>
</html>