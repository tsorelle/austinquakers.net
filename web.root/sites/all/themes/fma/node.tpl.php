<?php
// $Id: node.tpl.php,v 1.5 2007/10/11 09:51:29 goba Exp $
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
<div class="<?php print $wrapperClass; ?>" >

        <?php if ($picture) print $picture; ?>

        <?php if ($showTitle): ?>
          <h2><?php print $title ?></h2>
        <?php endif; ?>

        <?php if ($showSubmitted): ?>
            <div class="submitted">
                <?php print $submitted?>
            </div>
        <?php endif; ?>

        <div class="content clear-block">
            <?php TViewModel::RenderMessageElements(); ?>
            <?php print $content ?>
        </div>


        <?php if ($showSubmittedBelow): ?>
            <div class="submitted">
                <?php print $submitted?>
            </div>
        <?php endif; ?>


        <?php if ($showLinks): ?>

            <div class="clear-block">
                <div class="meta">
                    <?php if ($taxonomy): ?>
                        <div class="terms"><?php print $terms ?></div>
                    <?php endif;?>
                </div>

                <?php if ($links): ?>
                    <div class="links"><?php print $links; ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div> <!-- end of wrapper -->
</div> <!-- end node div -->