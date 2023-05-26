<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */

class subpage_zeltdoerfer extends subpage {

    function getContent($page){
        $this->title = "Zeltdörfer";


        $table = new autotable();
        $table->init("zeltdorf",array("zeltdorf_id","name"));
        $table->id_row = "zeltdorf_id";
        $table->table_name = "zeltdorf";

        $table->set_headername("name","Bezeichnung");
        $table->set_disabled("zeltdorf_id");

        $table->set_headername("zeltdorf_id","# ID");

        $table->enable_delete("Wollen Sie das Zeltdorf %s wirklich löschen (Es wird nur das Zeltdorf gelöscht, alle Teilnehmerdaten bleiben erhalten)?!","name");

        $table->edit = true;

        $btn = "<a class='btn btn-primary btn-sm' href='#' onclick=\"ajax_modal('subpage_zeltdoerfer','modal_add_zeltdorf','','')\">Neues Zeltdorf anlegen</a><br>";

        return $this->card($btn.$table->getContent(),$this->title);
    }

    function modal_add_zeltdorf ($data)
    {
        $mq = new mysql();
        $form = new form("add_zeltdorf");
        $form->add_textbox("Bezeichnung","","","","text","bezeichnung",true);

        $form->setTargetClassFunction("subpage_zeltdoerfer","save_zeltdorf");
        return json_encode(array("status" => "1","content" => $form->getContent(),"header"=> "Neues Zeltdorf anlegen"));
    }

    function save_zeltdorf($data)
    {
        $mc = new mysql();
        $sql = "INSERT into zeltdorf (name) VALUES ('".$data["bezeichnung"]."')";
        if ($mc->query($sql)) {
            return json_encode(array("status" => "1", "msg" => "!", "callback" => "location.href=\"?s=zeltdoerfer\";", "formcontrol" => ""));
        }
        return json_encode(array("status" => "0", "msg" => "DB ERROR: " . $mc->getError()));
    }

    function get_aktuelle_mahlzeit()
    {
        $mc = new mysql();
        $ret = $mc->fetch_array("SELECT * FROM mahlzeiten as m, typ as t WHERE m.time_from < UNIX_TIMESTAMP() AND m.time_till > UNIX_TIMESTAMP() AND m.typ_id = t.typ_id");
        if(isset($ret["mahlzeit_id"]))
            return $ret;
        else
            return false;
    }
}
