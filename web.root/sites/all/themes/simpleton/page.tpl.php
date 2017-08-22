<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php print $head_title; ?></title>

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <!-- Bootstrap -->
    <script src="/sites/all/themes/fma/assets/js/bootstrap.min.js"></script>


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- needed? -->
    <!-- script src="sites/all/themes/fma/assets/js/jquery.once.js?v=1.2"></script -->

    <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>


    <script src="http://cdnjs.cloudflare.com/ajax/libs/headjs/1.0.3/head.load.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/knockout/3.3.0/knockout-min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>


    <script src="/assets/js/Tops.Peanut/Peanut.js?v=1.0"></script>
    <script src="/assets/js/Tops.App/App.js?v=1.0"></script>

    <link rel="stylesheet" href="/sites/all/themes/simpleton/assets/css/simpleton.css">

</head>

<body>

<header id="navbar" role="banner" class="navbar container navbar-default">
    <div class="container">

        <div class="navbar-header">

            <a class="name navbar-brand" href="/" title="Home">Friends Meeting of Austin</a>

            <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-collapse collapse">
            <nav role="navigation">
                <?php if ($logged_in): ?>
                    <ul class="menu nav navbar-nav">
                        <li class="first leaf"><a href="/" title="Community site home page">Home</a></li>
                        <li class="leaf"><a href="/directory" title="">Directory</a></li>
                        <li class="leaf"><a href="/calendar" title="Calendar of events">Calendar</a></li>
                        <li class="leaf"><a href="/documents/fmanotes" title="Monthly Meeting Newsletter">Friendly Notes</a></li>
                        <li class="leaf"><a href="/help" title="Get help for features of the FMA Community Web Site">Help</a></li>
                    </ul>
                <?php endif; ?>

                <?php if (!$logged_in): ?>
                    <ul class="menu nav navbar-nav">
                        <li class="first leaf"><a href="/" title="Community site home page">Home</a></li>
                        <li class="leaf"><a href="/help" title="Get help for features of the FMA Community Web Site">Help</a></li>
                    </ul>
                <?php endif; ?>
            </nav>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown" style="">
                    <a href="#" title="Logout or My Account" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">

                        <?php if ($logged_in): ?>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="/logout">Sign out</a></li>
                        <?php endif; ?>

                        <?php if (!$logged_in): ?>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="/">Sign in</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>


            <!-- end nav region -->

        </div>
    </div>

</header>

<div class="main-container container">
    <header role="banner" id="page-header">
    </header> <!-- /#page-header -->

    <h1 class="page-header"><?php print $title; ?></h1>
    <messages-component></messages-component>
    <div class="row" id="view-container" style="display:none">
        <section class="col-sm-12">
            <?php
            print $content;
            ?>
        </section>
    </div>
</div>

<footer class="footer container">

    <?php print $vieweditlink; ?>

</footer>
<?php print $closure; ?>
</body>
<?php
    TViewModel::RenderStartScript();
?>
</html>