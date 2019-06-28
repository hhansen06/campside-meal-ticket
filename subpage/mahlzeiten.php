<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */

class subpage_mahlzeiten extends subpage {

    function getContent($page)
    {
        return "<div id='subcontentarea'></div><script>ajax_getasync_Content(\"subpage_mahlzeiten\",\"getContentAsync\");</script>";
    }


    function getContentAsync($page){
            $this->title = "Mahlzeiten";

            $mc = new mysql();

            $sql = "SELECT  * from mahlzeiten";

            $table = new autotable();
            $table->init("mahlzeiten",array("mahlzeit_id","time_from","time_till","typ_id"));
            $table->id_row = "mahlzeit_id";
            $table->table_name = $this->title;


            function get_timef($data)
            {
                return date("H:i (d.m.Y)", $data["time_from"]);
            }

            function get_timet($data)
            {
                return date("H:i (d.m.Y)", $data["time_till"]);
            }

        $table->set_headername("time_from","Uhrzeit Beginn");
        $table->set_rowtype("time_from","datetime-local");
        $table->set_fieldfunction("time_from","get_timef");

        $table->set_headername("time_till","Uhrzeit Ende");
        $table->set_rowtype("time_till","datetime-local");
        $table->set_fieldfunction("time_till","get_timet");

        $table->set_disabled("mahlzeit_id");

        $table->set_headername("mahlzeit_id","# ID");

        $table->enable_delete("Wollen Sie die Mahlzeit mit ID %s wirklich löschen?!","mahlzeit_id");

            $table->edit = true;

            $table->set_headername("typ_id","Typ der Mahlzeit");
            $mahlzeittypen = array();
            $res = $mc->query("SELECT * FROM typ");
            while($row = mysqli_fetch_array($res))
            {
                $mahlzeittypen[$row["typ_id"]] = utf8_encode($row["bezeichnung"]);
            }
            
            $table->set_fieldsource("typ_id",$mahlzeittypen);

        $btn = "<a class='btn btn-primary btn-sm' href='#' onclick=\"ajax_modal('subpage_mahlzeiten','modal_add_mahlzeit','','')\">Neue Mahlzeit anlegen</a><br>";


        $mz = $this->get_aktuelle_mahlzeit();
        if($mz)
            $aktmz = $this->card("Aktuell läuft ein <b>".utf8_encode($mz["bezeichnung"])."</b> bis <b>".date("d.m.Y H:i ",$mz["time_till"])."</b> Uhr","Aktuelle Mahlzeit");
        else
            $aktmz = $this->card("Aktuell läuft <b>keine</b> Mahlzeit","Aktuelle Mahlzeit");
        return $aktmz.$this->card($btn.$table->getContent(),$this->title);
    }

    function modal_add_mahlzeit ($data)
    {
        $mq = new mysql();
        $form = new form("add_mahlzeit");
        $form->add_textbox("Mahlzeit Beginn","","","","datetime-local","time_beginn",true);
        $form->add_textbox("Mahlzeit Ende","","","","datetime-local","time_ende",true);

        $typen = array();
        $rs = $mq->query("SELECT * FROM typ");
        while($row = mysqli_fetch_array($rs))
        {
            $typen[$row["typ_id"]] = utf8_encode($row["bezeichnung"]);
        }

        $form->add_select("Typ","",$typen,"","typ_id",true);

        $form->setTargetClassFunction("subpage_mahlzeiten","save_mahlzeit");
        return json_encode(array("status" => "1","content" => $form->getContent(),"header"=> "Neue Mahlzeit anlegen"));
    }

    function save_mahlzeit($data)
    {
        $time_ende = strtotime($data["time_ende"]);
        $time_beginn = strtotime($data["time_beginn"]);

        if(($time_ende - $time_beginn) < 3600)
            return json_encode(array("status" => "0", "msg" => "Mahlzeit kürzer 1h ?"));

        if(($time_ende - $time_beginn) > 86400)
            return json_encode(array("status" => "0", "msg" => "Mahlzeit länger als einen Tag?"));

        if(($time_ende - $time_beginn) < 0)
            return json_encode(array("status" => "0", "msg" => "Ende VOR Beginn ?"));


        $mc = new mysql();
        $sql = "INSERT into mahlzeiten (time_from,time_till,typ_id) VALUES (".$time_beginn.",".$time_ende.",".$data["typ_id"].")";
            if ($mc->query($sql)) {
                return json_encode(array("status" => "1", "msg" => "!","callback" => "ajax_getasync_Content(\"subpage_mahlzeiten\",\"getContentAsync\");"));
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
