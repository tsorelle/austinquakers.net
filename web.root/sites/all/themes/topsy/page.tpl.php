<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="<?php print $language->language ?>"
    lang="<?php print $language->language ?>"
    dir="<?php print $language->language ?>" >

<head>
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
</head>

<body class="<?php print $body_classes; ?>">
    <div id="<?php print $docId?>" class="<?php print $pageWidthClass?> pageFrame">
        <div id="hd"> <!-- YUI header container -->
          <div id="logo-title">

            <?php if (!empty($logo)): ?>
              <div id="logo-image">
                  <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
                    <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
                  </a>
              </div>
            <?php endif; ?>

            <div id="name-and-slogan">
              <?php if (!empty($site_name)): ?>
                <div id="site-name">
                    <h1>
                      <a href="<?php print $front_page ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
                    </h1>
                </div> <!-- site-name -->
              <?php endif; ?>


              <?php if (!empty($site_slogan)): ?>
                <!-- Slogans don't work in current version. Add manually if desired -->
                <div id="site-slogan"><?php print $site_slogan; ?></div>
              <?php endif; ?>
            </div> <!-- /name-and-slogan -->
          </div> <!-- /logo-title -->

          <?php if (!empty($search_box)): ?>
            <!-- position search as block or uncomment this section -->
            <!-- div id="search-box"><?php print $search_box; ?></div -->
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

          <?php if (!empty($top_navigation)): ?>
            <div id="top-navigation-region" class="hmenu">
              <?php print $top_navigation; ?>
            </div>
          <?php endif; ?>


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
    <?php TTracer::PrintMessages(); ?>

        <?php print $closure; ?>
    </div> <!-- end page frame "docId" -->
</body>
</html>