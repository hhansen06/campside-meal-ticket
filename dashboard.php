
<?php
include("config.php");
?>
<!-- Bootstrap core CSS -->
<link href="<?php echo BASEURL?>/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo BASEURL?>/css/open-iconic-bootstrap.css" rel="stylesheet">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

<link href="<?php echo BASEURL?>/css/smart_wizard.css" rel="stylesheet" type="text/css" />
<link href="<?php echo BASEURL?>/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />

<link href="<?php echo BASEURL?>/css/jquery.dm-uploader.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo BASEURL?>/css/jquery.dataTables.min.css" rel="stylesheet">


<!-- Custom styles for this template -->
<link href="<?php echo BASEURL?>/css/dashboard.css" rel="stylesheet">

<script src="<?php echo BASEURL?>/js/popper.min.js"></script>
<script src="<?php echo BASEURL?>/js/functionsV2.js"></script>

<script src="<?php echo BASEURL?>/js/jquery-3.3.1.min.js"></script>
<script src="<?php echo BASEURL?>/js/jquery.serialzize.js"></script>
<script src="<?php echo BASEURL?>/js/bootstrap.min.js"></script>
<script src="<?php echo BASEURL?>/js/jquery.dm-uploader.min.js"></script>
<script src="<?php echo BASEURL?>/js/jquery.dataTables.min.js"></script>
<script>
    update_dashboard();
    document.addEventListener('DOMContentLoaded', uhrzeit);
</script>
<p id="uhr" class="uhr">12:12:12</p>
<div id="contentarea"></div>

