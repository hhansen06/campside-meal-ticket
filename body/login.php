
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo $BASEDIR?>/favicon.ico">

    <title>Anmelden | <?php echo $this->pagetitle?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/login.css" rel="stylesheet">
    <link href="<?php echo BASEURL?>/css/page.css" rel="stylesheet">

    <script src="<?php echo BASEURL?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo BASEURL?>/js/functionsV2.js"></script>


</head>
<body>
<div class="container">
    <div class="row justify-content-start">
        <div class="col-12">
            <div id="contentarea"></div>
        </div>
        <div class="col-4">
        </div>
        <div class="col-4">
            <form class="form-signin" id="login" action="" method="post">

                <h1 class="h3 mb-3 font-weight-normal">Datenkatalog</h1>
                <img class="mb-4" src="images/logo.png" width="300px">
                <?php if(strlen($this->loginerror) > 0)echo "<p class='loginerror'>".$this->loginerror."</p>";?>
                <label for="benutzername" class="sr-only">E-Mail</label>
                <input type="text" id="benutzername" name="benutzername" class="form-control" placeholder="E-Mail" required autofocus>
                <label for="passwort" class="sr-only">Passwort</label>
                <input type="password" id="passwort" name="passwort"  class="form-control" placeholder="Passwort" required>
                <button class="btn btn-lg btn-primary btn-block" name="trylogin" type="submit">Anmelden</button>
                <button class="btn btn-lg btn-success btn-block" name="trylogin" onclick="showcreateaccount();" type="button">Konto anlegen</button>
            </form>

            <form style='display:none;' class="form-signin" id="register" action="" method="post">
                <h1 class="h3 mb-3 font-weight-normal">Datenkatalog</h1>
                <h1 class="h3 mb-3 font-weight-normal">Neues Konto anlegen</h1>

                <label for="passwort" class="sr-only">Passwort</label>
                <input type="password" id="passwortneu" name="passwort"  class="form-control" placeholder="Passwort" required>

                <label for="passwort1" class="sr-only">Passwort wiederholung</label>
                <input type="password" id="passwort1neu" name="passwort1"  class="form-control" placeholder="Passwort wiederholung" required>

                <label for="benutzername" class="sr-only">E-Mail</label>
                <input type="text" id="benutzernameneu" name="benutzername" class="form-control" placeholder="E-Mail" required autofocus>

                <br>
                <button class="btn btn-lg btn-primary btn-block" name="trylogin" onclick="createaccount();" type="button">Neues Konto anlegen</button>
                <button class="btn btn-lg btn-danger btn-block" name="trylogin" onclick="cancelcreateaccount();" type="button">Abbrechen</button>

            </form>
        </div>
</body>
</html>

<?php
if(isset($_GET["key"])) {
    $usr = new user();
    $usr->activate($_GET["key"], (int)$_GET["user_id"]);
}
?>