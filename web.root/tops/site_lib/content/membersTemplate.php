<!DOCTYPE HTML PUBLIC "-//W3C//Dtd HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
      <title><?=$pageController->getPageTitle()?></title>
      <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
      <link rel="stylesheet" type="text/css" href="/quip/style/print.css" media="print" />
      <style type="text/css" media="all">
        @import "/quip/style/yui/reset-fonts-grids.css";
        @import "/quip/style/yui/base-min.css";
        @import "/quip/style/yui/base-min.css";
        @import "/sites/all/themes/quip/style.css";
        @import "/quip/style/quip_members.css";
      </style>
      <style type="text/css" media="screen">
        <?php print $pageController->renderCssImports('screen'); ?>
      </style>
	</head>

	<body>

      <div id="top"><a href="#mainContent" class="doNotDisplay doNotPrint">Skip to main content.</a></div>
		<div  id="doc" class="yui-t1 pageFrame">
        <div id="frame">
		   <div id="hd"> <!-- header -->

        <div id="headerTop">
           QUIP Members
         </div>
        <div id="headerMain">
            <img src="/quip/images/quiplogo.jpg" />
        </div>
        <div id="headerContent">
        </div>
        <div id="headerBottom">
            <h2><?php print $pageController->getHeaderSubtitle() ?></h2>

        </div>




			</div>

			<div id="bd"> <!-- body -->
                <div id="trace"><?=TTracer::Render()?> </div>
                <?=$pageController->renderBeginForm()?>
                <div class="breadcrumb">
                <?=$pageController->renderSubNavigation()?>
                </div>
                <?=$pageController->renderErrorMessages()?>
                <?=$pageController->renderInfoMessages()?>
                <!-- render content -->
                <?
                    foreach($pageController->getMainContent() as $item) {
                        if ($item instanceof TFilePath) {
                            include($item);
                        }
                        else
                            echo $item;
                    }
                ?>
                <?=$pageController->renderEndForm()?>
			</div> <!-- end bd -->

            <div id="ft"> <!-- footer -->
            </div>
            </div> <!-- end frame -->
	  	</div> <!-- end doc -->
	</body>
</html>
