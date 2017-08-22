<?php
// $Id: node-tops.tpl.php,v 1.5 2007/10/11 09:51:29 goba Exp $
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
    <div class="<?php print $wrapperClass; ?>" >
        <div class="content clear-block  topsform">
            <?php print $content ?>
        </div>
    </div> <!-- end of wrapper -->
</div> <!-- end node div -->