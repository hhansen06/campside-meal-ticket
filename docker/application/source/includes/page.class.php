<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 21.03.2018
 * Time: 12:17
 */

class page
{
    var $pagetitle = PAGETITLE;
    var $loginerror = "";
    var $content;
    var $sql;
    var $user;
    var $header1 = "";

    function setHeader($text)
    {
        $this->header1 = "<h2>".$text."</h2>";
    }

    function page($theme = "page")
    {

        $this->sql = new mysql();

    // LOGOFF Intent
        if (isset($_GET["logoff"])) {
        session_destroy();
        }

        // LOGIN Intent
        if (isset($_POST["trylogin"])) {
            $this->try_login($_POST["benutzername"], $_POST["passwort"]);
        }

        // User case
        if (isset($_SESSION["user"])) {
            $this->user = $_SESSION["user"];
            // INHALT ANZEIGEN
            $this->show_content($theme);
        } else {
            // LOGINSEITE ANZEIGEN
            $this->show_login();
        }
    }


    function try_login($username, $password)
    {

        $userdata =  $this->sql->fetch_array("SELECT * FROM user WHERE password = SHA1('".$password."') AND email = '".$username."'");
        if(isset($userdata["user_id"])) {
        if($userdata["enabled"] == 0)
        {
            $this->loginerror = "Ihr Benutzerkonto wurde noch nicht aktiviert. Bitte Prüfen Sie Ihre E-Mail!";
            return false;
        }
        else {
            $this->user = (object)[];
            foreach ($userdata as $key => $value) {
                $this->user->$key = $value;
            }

            $_SESSION["user"] = $this->user;
            return true;
        }
        }
        else
            $this->loginerror = "Benutzername oder Passwort falsch!";
            return false;

    }

    function show_content($theme)
    {
        if (isset($_GET["s"])) {
            $cl = "subpage_" . $_GET["s"];
            if (class_exists($cl)) {
                $subpage = new $cl();


                $this->content = $subpage->getContent($this);
                $this->pagetitle = $subpage->title . " | " . $this->pagetitle;

            } else
                echo "page.class " . $cl . " not found!";
        }
            else
            {
                $cl = HOMECLASS;
                if (class_exists($cl)) {
                $subpage = new $cl();


                $this->content = $subpage->getContent($this);
                $this->pagetitle = $subpage->title . " | " . $this->pagetitle;

            } else
                echo "page.class " . $cl . " not found!";
        }


        include("body/".$theme.".php");
    }



    function show_login()
    {


        include("body/login.php");
    }

    function pr($object)
    {
        echo "<pre>" . print_r($object) . "</pre>";
    }


}