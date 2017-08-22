<div id="content-header">
    <?php
        print $breadcrumb;
        if ($mission)
            print '<div id="mission">'.$mission.'</div>';

        if ($showTabs) {
            if ($showTitle)
                print '<h1 class="pageTitle with-tabs">'. $title .'</h1>';
            if ($tabs) {
                print '<div id="tabs-wrapper" class="clear-block">';
                print $tabs;
                print '</div>'; // end of tabs-wrapper
            }
            if ($tabs2)
                print '<ul class="tabs secondary">'. $tabs2 .'</ul>';
        }
        else if ($showTitle)
            print '<h1 class="pageTitle">'. $title .'</h1>';

        if ($show_messages && $messages)
            print $messages;

        print $help;
    ?>
</div>

<div id="content-region">
  <?php
        if ($page_picture)
            print '<div id=page-picture>'.$page_picture.'</div>';

        print $content
    ?>
</div>

<?php if ($content_footer): ?>
  <div id="content-footer">
    <?php print $content_footer; ?>
  </div> <!-- /#content-footer -->
<?php endif; ?>

<?php if ($feed_icons): ?>
  <div class="feed-icons"><?php print $feed_icons; ?></div>
<?php endif; ?>

