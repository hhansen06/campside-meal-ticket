<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 19.09.2018
 * Time: 16:29
 */


class user {
    function addaccount($data)
    {
         $email = strtolower(filter_var($data["data1"], FILTER_SANITIZE_EMAIL));
        $password = strtolower(filter_var($data["data"], FILTER_SANITIZE_STRING));

        $mc = new mysql();
        $checkemail = $mc->fetch_array("SELECT * FROM user WHERE email = '".$email."'");

        if(isset($checkemail["user_id"]))
        {
            return json_encode(array("status" => 0,"msgheader" => "Konto existiert bereits!","msg" => "E-Mail Addresse wird bereits verwendet!"));
        }
        else {

            if($mc->query("INSERT into user (email,password,enable_hash,time_created) VALUES ('".$email."',PASSWORD('".$password."'),'".md5(time()."-".rand())."','".time()."')")) {

                if($this->send_welcome_mail($mc->getID()))
                    return json_encode(array("status" => 1,"header" => "Erfolgreich angelegt!", "msg" => "Ihr Benutzerkonto wurde erfolgreich angelegt. Es wurde gerade eine E-Mail an: <b>".$email."</b> gesendet. Bitte klicken Sie in der E-Mail auf den enthaltenen Link um Ihr Konto zu aktivieren."));
                else
                    return json_encode(array("status" => 0,"msgheader" => "Systemfehler!!","msg" => "Es ist ein interner Fehler aufgetreten! Fehler:user->send_welcome_mail->e2"));

            }
            else
                return json_encode(array("status" => 0,"msgheader" => "Datenbankfehler!","msg" => "Es ist ein interner Fehler aufgetreten! Fehler:user->addaccount->e1"));
        }
    }

    function activate($key,$user_id)
    {
        $mc = new mysql();
        $user = $mc->fetch_array("SELECT * FROM user WHERE user_id = '".$user_id."'");
        if($key == $user["enable_hash"])
        {
           if($mc->query("update user set enabled = 1 WHERE user_id = '".$user_id."'"))
           {
               echo "<script>addAlert('Konto aktiviert', 'Ihr Benutzerkonto wurde erfolgreich aktiviert!', 'success');</script>";
           }
        }

    }

    function send_welcome_mail($user_id)
    {
        $mc = new mysql();
        $user = $mc->fetch_array("SELECT * FROM user WHERE user_id = '".$user_id."'");

        $url = "https://katalog.diepholz.de/?key=".$user["enable_hash"]."&user_id=".$user["user_id"];

        $texthtml = "Sehr geehrter Nutzer,
       <br>
       Es wurde f&uuml;r Sie ein Benutzerkonto auf <a href='https://katalog.diepholz.de'>katalog.diepholz.de</a> erstellt.<br>
       Bitte klicken Sie auf diesen Link um ihr Benutzerkonto zu aktivieren: <a href='".$url."'>".$url."</a><br>
       <br>
       Ihr Team des Bildungsb&uuml;ros<br>
       Telefon:  05441-9761914
       E-Mail: bildungsbuero@diepholz.de
       ";


     return send_mail($user["email"],"[BILDUNGSPORTAL] Neues Benutzerkonto wurde angelegt!",$texthtml);
    }
}

function is_admin()
{
        if($_SESSION["user"]->is_admin == "1")
            return true;
        else
            return false;

}