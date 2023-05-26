<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */

class subpage_kostenstellen extends subpage {

    function getContent($page){
        $this->title = "Kostenstellen";


        $table = new autotable();
        $table->init("kostenstellen",array("kostenstelle_id","bezeichnung"));
        $table->id_row = "kostenstelle_id";
        $table->table_name = "kostenstellen";

        $table->set_headername("bezeichnung","Bezeichnung");
        $table->set_disabled("kostenstelle_id");

        $table->set_headername("kostenstelle_id","# ID");

        $table->enable_delete("Wollen Sie die Kostenstelle %s wirklich löschen (Es wird nur die Kostenstelle gelöscht, alle Essenmarken bleiben erhalten)?!","bezeichnung");

        $table->edit = true;

        $btn = "<a class='btn btn-primary btn-sm' href='#' onclick=\"ajax_modal('subpage_kostenstellen','modal_add_kostenstelle','','')\">Neue Kostenstelle anlegen</a><br>";

        return $this->card($btn.$table->getContent(),$this->title);
    }

    function modal_add_kostenstelle ($data)
    {
        $mq = new mysql();
        $form = new form("add_zeltdorf");
        $form->add_textbox("Bezeichnung","","","","text","bezeichnung",true);

        $form->setTargetClassFunction("subpage_kostenstellen","save_kostenstelle");
        return json_encode(array("status" => "1","content" => $form->getContent(),"header"=> "Neue Kostenstelle anlegen"));
    }

    function save_kostenstelle($data)
    {
        $mc = new mysql();
        $sql = "INSERT into kostenstellen (bezeichnung) VALUES ('".$data["bezeichnung"]."')";
        if ($mc->query($sql)) {
            return json_encode(array("status" => "1", "msg" => "!", "callback" => "location.href=\"?s=kostenstellen\";", "formcontrol" => ""));
        }
        return json_encode(array("status" => "0", "msg" => "DB ERROR: " . $mc->getError()));
    }
}
