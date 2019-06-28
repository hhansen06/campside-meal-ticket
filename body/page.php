
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?php echo $this->pagetitle?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo BASEURL?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASEURL?>/css/open-iconic-bootstrap.css" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

    <link href="<?php echo BASEURL?>/css/smart_wizard.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASEURL?>/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo BASEURL?>/css/jquery.dm-uploader.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASEURL?>/css/jquery.dataTables.min.css" rel="stylesheet">


    <!-- Custom styles for this template -->
    <link href="<?php echo BASEURL?>/css/page.css" rel="stylesheet">

    <script src="<?php echo BASEURL?>/js/popper.min.js"></script>
    <script src="<?php echo BASEURL?>/js/functionsV2.js"></script>

    <script src="<?php echo BASEURL?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo BASEURL?>/js/jquery.serialzize.js"></script>
    <script src="<?php echo BASEURL?>/js/bootstrap.min.js"></script>
    <script src="<?php echo BASEURL?>/js/jquery.dm-uploader.min.js"></script>
    <script src="<?php echo BASEURL?>/js/jquery.dataTables.min.js"></script>


</head>

<body>

<div class="modal" id='modal_page' tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title"></h5>
                <a class="close" data-dismiss="modal">×</a>
            </div>
            <div class="contentarea"></div>
            <div class="modal-body" id="modal_body">
            </div>
            <div class="modal-footer">
                <span class="btn btn-danger" data-dismiss="modal">Abbrechen</span>
            </div>
        </div>
    </div>
</div>


<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="index.php"><?php echo get_settings("veranstaltung")." ".get_settings("zeltlager_jahr")?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse navbar-expand-sm" id="navbarsExampleDefault">

        <ul class="navbar-nav mr-auto">

            <?php
            foreach(basemenu as $key => $settings)
            {
                if(isset($settings["target"]))
                    $target = $settings["target"];
                else
                    $target = "";


                $show = true;
                if(isset($settings["admin"])) {
                    if(!is_admin()) {
                        $show = false;
                    }
                }

                        if($show) {
                            if(isset($settings["url"]))
                                $url = $settings["url"];
                            else
                                $url = "?s=" . $settings["class"];

                            echo "
                    <li class=\"nav-item\">
                        <a class=\"nav-link\" target='".$target."' href=\"".$url."\">" . $key . "</a>
                    </li>
                    ";
                        }

            }
            ?>
        </ul>
        <span class="navbar-nav nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="dropdown01" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Einstellungen</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
            <?php
            foreach(configmenu as $key => $settings)
            {
                if(isset($settings["target"]))
                    $target = $settings["target"];
                else
                    $target = "";


                $show = true;
                if(isset($settings["admin"])) {
                    if(!is_admin()) {
                        $show = false;
                    }
                }

                if($show) {
                    if(isset($settings["url"]))
                        $url = $settings["url"];
                    else
                        $url = "?s=" . $settings["class"];

                    echo "
                        <a class=\"dropdown-item\" target='".$target."' href=\"".$url."\">" . $key . "</a>
                    
                    ";
                }

            }
            ?>
                </div>
        </span>
        <span class="navbar-nav nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="dropdown02" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $this->user->email?></a>
            <div class="dropdown-menu" aria-labelledby="dropdown02">
                <a class="dropdown-item" onclick="ajax_action('ajax_logoff',location_index)" href="#">Abmelden</a>
            </div>
        </span>
    </div>
</nav>

<main role="main" id="contentarea" class="container-fluid prespacing">

    <?php
    echo $this->header1;

    if($_SERVER["SERVER_NAME"] == "dev-datenschutz.lkdh.intern")
    {
        echo alert("Entwicklungsumgebung!!","Derzeit wird die Datenbank: ".MYSQL_DATENBANK." verwendet!","danger");

    }

    echo $this->content;
    ?>

</main><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script>
$(function () {

$('[data-toggle="tooltip"]').tooltip();

})
</script>
</body>
</html>
