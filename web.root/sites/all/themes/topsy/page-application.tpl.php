<?php
    if (!isset($content)) {
        $content = 'No content';
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Title</title>

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!-- Bootstrap -->
    <script src="sites/all/themes/fma/assets/js/bootstrap.min.js"></script>


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


    <script src="topsJS/Tops.Peanut/Peanut.js?v=1.0"></script>
    <script src="topsJS/Tops.App/App.js?v=1.0"></script>

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
                <ul class="menu nav navbar-nav">
                    <li class="first leaf"><a href="/" title="Community site home page">Home</a></li>
                    <li class="leaf"><a href="/directory" title="">Directory</a></li>
                    <li class="leaf"><a href="/calendar" title="Calendar of events">Calendar</a></li>
                    <li class="leaf"><a href="/documents/fmanotes" title="Monthly Meeting Newsletter">Friendly Notes</a></li>
                    <li class="leaf"><a href="/help" title="Get help for features of the FMA Community Web Site">Help</a></li>
                </ul>

            </nav>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown" style="">
                    <a href="#" title="Logout or My Account" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/user/logout">Log out</a></li>
                    </ul>
                </li>
            </ul>


            <!-- end nav region -->

        </div>
    </div>

</header>

<div class="main-container container">

    <header role="banner" id="page-header">

        <!-- ?php if (!empty($site_slogan)): ? -->
        <!-- p class="lead"></p -->
        <!-- ?php endif; ? -->
    </header> <!-- /#page-header -->

    <div class="row">
        <section class="col-sm-12">
            <a id="main-content"></a>
            <h1 class="page-header">{title}</h1>
            <div class="region region-content">

                                        <h1>Hello Test One</h1>

                <div>
                    <?php print "Content: $content" ?>
                </div>
                                        <!-- ******************  END FORM ****************************************************************************  -->
            </div> <!-- content div -->
        </section>
    </div>
</div>

<footer class="footer container panel-footer">
</footer>

<!-- script src="assets/js/Tops.App/{viewmodelname}ViewModel.js"></script -->
<!-- script>ViewModel.init('design/', function() {  ko.applyBindings(ViewModel); });</script -->
<?php print $closure; ?>
</body>
</html>