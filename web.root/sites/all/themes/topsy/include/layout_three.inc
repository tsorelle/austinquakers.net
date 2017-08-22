<div id="leftColumn" class="yui-b doNotPrint"> <!-- YUI b -->
    <?php print $left ?>
</div> <!-- end YUI b -->

<div id="yui-main" class="expandForPrint">   <!-- YUI main -->
    <div class="yui-b expandForPrint"> <!-- YUI b -->
        <div class="yui-ge"> <!-- YUI ge -->
            <div class="yui-u first"> <!-- YUI u first -->
        	    <?php include("$includePath/content_main.inc"); ?>
    	    </div> <!-- end YUI u first  -->

            <div id="rightColumn" class="yui-u"> <!-- YUI u -->
        	    <?php print $right ?>
        	</div> <!-- end YUI u -->
        </div> <!-- end YUI ge  -->
    </div>    <!-- end YUI b -->
</div> <!-- end YUI main -->

