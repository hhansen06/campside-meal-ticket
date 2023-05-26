<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */


require 'includes/escpos/autoload.php';

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;


class subpage_teilnehmer extends subpage {

    function getContent($page){
        $this->title = "Teilnehmer Ausweise";

       $kstellen = array();
       $res_typ = $page->sql->query("SELECT * FROM jugendfeuerwehr");
       while($row = mysqli_fetch_array($res_typ))
       {
            $jfs[$row["jf_id"]] = $row["name"];
       }

        $atable = new autotable();
        $atable->add_customrow("info");

        $atable->init("teilnehmer",array("teilnehmer_id","vorname","nachname","jf_id"));
        $atable->id_row = "teilnehmer_id";
        $atable->set_fieldfunction("info","gen_btn");

        $atable->set_headername("teilnehmer_id","# ID");
        $atable->set_headername("vorname","Vorname");
        $atable->set_headername("nachname","Nachname");
        $atable->set_headername("jf_id","Jugendfeuerwehr");

        function gen_btn($data)
        {
            return "<a class='' href='#' onclick=\"ajax_modal('subpage_teilnehmer','modal_info','".$data["teilnehmer_id"]."','')\">Details</a><br>";
        }


       $atable->set_fieldsource("jf_id",$jfs);

       return $this->card($atable->getContent() , "Teilnehmer");
    }

    function reset_essen($data)
    {
        $id = $data["data"];
        $teilnehmer_id = $data["formcontrolname"];

        $mc = new mysql();
        if($mc->query("DELETE FROM teilnehmer_mahlzeit WHERE teilnehmer_mahlzeit_id = '".$id."'"))
            return $this->modal_info(array("data" => $teilnehmer_id));
    }

    function modal_info($data)
    {
        $mc = new mysql();
        $id = $data["data"];
        $teilnehmer = $mc->fetch_array("SELECT * FROM teilnehmer WHERE teilnehmer_id = '".$id."'");

        $header = "Details über ".$teilnehmer["nachname"].", ".$teilnehmer["vorname"];

        $content = "Teilnehmer ID: ".$data["data"]."<br><br>";
        $content .= "<b>Gegessen:</b><br>";

        $res = $mc->query("SELECT * FROM mahlzeiten as m, typ as t WHERE m.typ_id = t.typ_id ORDER BY m.time_from ASC,m.typ_id ASC");
        while($row = mysqli_fetch_array($res))
        {
            $mahlzeit = $mc->fetch_array("SELECT * FROM teilnehmer_mahlzeit WHERE teilnehmer_id = ".$id." AND mahlzeit_id = ".$row["mahlzeit_id"]);

            $reset_link = "<a class='' href='#' onclick=\"ajax_modal('subpage_teilnehmer','reset_essen','".$mahlzeit["teilnehmer_mahlzeit_id"]."','".$mahlzeit["teilnehmer_id"]."')\">[zurücksetzen]</a>";

            if(isset($mahlzeit["teilnehmer_mahlzeit_id"]))
                $content .= date("d.m.Y",$row["time_from"])." - ".$row["bezeichnung"]." - <span style='color:green;'>gegessen ".$reset_link."</span><br>";
            else
                $content .= date("d.m.Y",$row["time_from"])." - ".$row["bezeichnung"]." - <span style='color:red;'>nicht gegessen</span><br>";

        }

        $historie = get_history("ausweis",$id);

        $content .= "<br><b>Historie:</b><br>";
        foreach($historie as $hist)
        {
            $content .= date("[d.m.y H:i:s]",$hist["time"])." - ".$hist["msg"]."<br>";

        }

        return json_encode(array("status" => "1","content" => $content,"header"=> $header));

    }

    function essenmarke_getinfos($data)
    {
        $id = $data["data"];
        $ms = new mysql();

        $emarke = $ms->fetch_array("SELECT * FROM essenmarken WHERE essenmarke_id = '".$id."'");
        if(isset($emarke["essenmarke_id"]))
        {
            $emarke = $ms->fetch_array("SELECT * FROM essenmarken WHERE essenmarke_id = '".$id."'");
            $kostenstellen = $ms->fetch_array("SELECT * FROM kostenstellen WHERE kostenstelle_id = '".$emarke["kostenstelle_id"]."'");
            $typ = $ms->fetch_array("SELECT * FROM typ WHERE typ_id = '".$emarke["typ_id"]."'");

            $content = "Gedruckt am <b>".date("d.m.Y",$emarke["time_created"])."</b> um <b>".date("H:i",$emarke["time_created"])."</b> Uhr<br>";
            $content .= "Kostenstelle: <b>".$kostenstellen["bezeichnung"]."</b><br>";
            $content .= "Typ: <b>".$typ["bezeichnung"]."</b><br>";

        }
        else
        {
            $content = "<b>Es wurde keine Essenmarke mit der ID: ".$id." gedruckt!";
        }



        return json_encode(array("status" => "1","content" => $content,"header"=> "Historie der Essenmarke mit ID: ".$data["data"]));
    }

    function modal_add_essenmarke ($data)
    {
        $ms = new mysql();
        $form = new form("add_kategorie");
        $form->buttontext = "Essenmarke drucken";

        $res_k = $ms->query("SELECT * FROM kostenstellen");
        $kostenstellen = array();
        while($row = mysqli_fetch_array($res_k))
        {
           $kostenstellen[$row["kostenstelle_id"]] = $row["bezeichnung"];
        }

        $res_k = $ms->query("SELECT * FROM typ");
        $typ = array();
        while($row = mysqli_fetch_array($res_k))
        {
            $typ[$row["typ_id"]] = $row["bezeichnung"]." (".$row["preis"]." Euro)";
        }

        $form->add_textbox("Anzahl:","1","","","number","anzahl",true);

        $form->add_select("Kostenstelle:","",$kostenstellen,"","kostenstelle",true);
        $form->add_select("Typ:","",$typ,"","typ",true);


        $form->setTargetClassFunction("subpage_essenmarke","essenmarke_drucken");
        return json_encode(array("status" => "1","content" => $form->getContent(),"header"=> "Neue Essenmarke ausdrucken"));
    }

}
