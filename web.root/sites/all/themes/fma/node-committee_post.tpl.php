<?php
// $Id: node-committee_post.tpl.php,v 1.5 2007/10/11 09:51:29 goba Exp $
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
<div class="committee-page" >


        <?php if ($committee) : ?>
           <?php if ($committee->updateLink) :    ?>
            <div id="committee-link">
                <a href="<?php print $committee->updateLink; ?>">[Update committee]</a></div>
            <?php endif; ?>

            <?php if ($committee->description) :   ?>
            <div>
            <fieldset>
                <legend>Committee Description</legend>
                <?php print $committee->description; ?>
            </fieldset>
            </div>
            </br>
            <?php endif; // description ?>

        <?php endif; // committee ?>

        <div class="content clear-block">
            <?php print $content ?>
        </div>

        <?php if ($committee && $committee->notes) : ?>
        <div>
        <fieldset class="collapsible collapsed">
            <legend>Notes</legend>
            <div>
            <?php print $committee->notes; ?>
            </div>
        </fieldset>
        </div>
        <?php endif; //notes ?>



        <?php if ($links): ?>
            <div class="links"><?php print $links; ?></div>
        <?php endif; ?>



    </div> <!-- end of wrapper -->
</div> <!-- end node div -->