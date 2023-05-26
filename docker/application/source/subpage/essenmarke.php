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


class subpage_essenmarke extends subpage {

    function getContent($page){
        $this->title = "Kategorie";

       $kstellen = array();
       $res_typ = $page->sql->query("SELECT * FROM kostenstellen");
       while($row = mysqli_fetch_array($res_typ))
       {
           $kstellen[] = $row;
       }


        $sql = "SELECT * FROM essenmarken as e, typ as t WHERE t.typ_id = e.typ_id GROUP BY e.typ_id ";
        $ret = "<table border='1' width='100%' class='table compact table-compact table-striped table-bordered table-hover no-footer'>";

        $ret .= "<thead style='background-color: #c0c0c0;'><tr>";
        $ret .= "<th>&nbsp;</th>";

        foreach($kstellen as $kstelle)
        {
            $ret .= "<th colspan=\"3\">".$kstelle["bezeichnung"]."</th>";
        }
        $ret .= "<th colspan=\"3\">Summe</th>";
        $ret .= "</tr></thead>";

        $ret .= "<tr>";
        $ret .= "<td>&nbsp;</td>";

        foreach($kstellen as $kstelle)
        {
            $ret .= "<td>Erstellt</td><td>Benutzt</td><td>Unbenutzt</td>";
        }
        $ret .= "<td>Erstellt</td><td>Benutzt</td><td>Unbenutzt</td>";
        $ret .= "</tr>";



        $res = $page->sql->query($sql);

        echo $page->sql->getError();

        while ($row = mysqli_fetch_array($res)) {

            $ret .= "<tr>";
            $ret .= "<td>".$row["bezeichnung"]."</td>";
            foreach($kstellen as $kstelle)
            {
                $data = $page->sql->fetch_array("SELECT COUNT(*) as anz, SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as guelt, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as gueltn FROM essenmarken WHERE kostenstelle_id = '".$kstelle["kostenstelle_id"]."' and typ_id = '".$row["typ_id"]."' ");
                $ret .= "<td>".$data["anz"]."</td><td>".$data["gueltn"]."</td><td>".$data["guelt"]."</td>";
            }

            $data = $page->sql->fetch_array("SELECT COUNT(*) as anz, SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as guelt, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as gueltn FROM essenmarken WHERE typ_id = '".$row["typ_id"]."' ");
            $ret .= "<td>".$data["anz"]."</td><td>".$data["gueltn"]."</td><td>".$data["guelt"]."</td>";


            $ret .= "</tr>";

            }
        $ret .= "</table>";

        ############## ESSEN ÜBERSICHT #######################
        $ret .= "<table border='1' width='100%' class='table compact table-compact table-striped table-bordered table-hover no-footer'>";

        $ret .= "<thead style='background-color: #c0c0c0;'><tr>";
        $ret .= "<th>&nbsp;</th>";

        foreach($kstellen as $kstelle)
        {
            $ret .= "<th colspan=\"1\">".$kstelle["bezeichnung"]."</th>";
        }
        $ret .= "<th colspan=\"1\">Summe</th>";
        $ret .= "</tr></thead>";

        $ret .= "<tr>";
        $ret .= "<td>&nbsp;</td>";

        foreach($kstellen as $kstelle)
        {
            $ret .= "<td>Benutzt</td>";
        }
        $ret .= "<td>Benutzt</td>";
        $ret .= "</tr>";


        $sql = "SELECT * FROM mahlzeiten as m, typ as t WHERE m.typ_id = t.typ_id  ORDER BY m.time_from ASC, m.typ_id ASC";

        $res = $page->sql->query($sql);

        echo $page->sql->getError();

        while ($row = mysqli_fetch_array($res)) {


            $ret .= "<tr>";
            $ret .= "<td>".date("d.m.Y",$row["time_from"])." ".$row["bezeichnung"]."</td>";
            foreach($kstellen as $kstelle)
            {
                $data = $page->sql->fetch_array("SELECT COUNT(*) as anz  FROM essenmarken WHERE kostenstelle_id = '".$kstelle["kostenstelle_id"]."' and mahlzeit_id = '".$row["mahlzeit_id"]."' AND status = 1");
                $ret .= "<td>".$data["anz"]."</td>";
            }

            $data = $page->sql->fetch_array("SELECT COUNT(*) as anz FROM essenmarken WHERE mahlzeit_id = '".$row["mahlzeit_id"]."' AND status = 1 ");
            $ret .= "<td>".$data["anz"]."</td>";
            $ret .= "</tr>";

        }
        $ret .= "</table>";


        $btn = "<a class='btn btn-primary btn-sm' href='#' onclick=\"ajax_modal('subpage_essenmarke','modal_add_essenmarke','','')\">Neue Essenmarke ausdrucken</a><br>";
        $btn2 = "ID der Essenmarke: <input type='number' id='essemid'> <a class='btn btn-primary btn-sm' href='#' onclick=\"ajax_modal('subpage_essenmarke','essenmarke_getinfos',$('#essemid').val())\">Informationen anzeigen</a><br>";

            return $this->card($btn2 , "Essenmarke Historie").$this->card($btn.$ret , $this->title);
    }

    function reset_essenmarke($data)
    {
        print_r($data);
        $mc = new mysql();
        $id = $data["data"];
        $mc->query("UPDATE essenmarken SET status = 0, mahlzeit_id = 0 WHERE essenmarke_id = ".$id);
        set_history("essenmarke",$id,4,$_SESSION["user"]->email);

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
            $content .= "Typ: <b>".$typ["bezeichnung"]."</b><br><br>";

            if($emarke["status"] == 0)
                $content .= "Status: <b>Unbenutzt</b><br><br>";
            else
            {
                $mzeit = $ms->fetch_array("SELECT * FROM mahlzeiten as m, typ as t WHERE t.typ_id = m.typ_id AND m.mahlzeit_id = '".$emarke["mahlzeit_id"]."'");
                $content .= "Status: <b style='color:red;'>benutzt (".date("d.m.Y",$mzeit["time_from"])." ".$mzeit["bezeichnung"].")</b><br><br>";

            }

            $historie = get_history("essenmarke",$id);

            $content .= "<b>Historie:</b><br>";
            foreach($historie as $hist)
            {
                $content .= date("[d.m.y H:i:s]",$hist["time"])." - ".$hist["msg"]."<br>";
            }
            $content .= "<br><a class='btn btn-primary btn-sm' href='#' onclick=\"ajax_modal('subpage_essenmarke','reset_essenmarke','".$id."','')\">Essenmarke wieder gültig machen</a><br>";
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

        $form->add_checkbox("Essenmarke schneiden","", true,"schneiden",true);

        $form->add_textbox("Wertigkeit für Mehrfachmarke:","1","","","number","multimark_anz",true);


        $form->setTargetClassFunction("subpage_essenmarke","essenmarke_drucken");
        return json_encode(array("status" => "1","content" => $form->getContent(),"header"=> "Neue Essenmarke ausdrucken"));
    }

    function essenmarke_drucken($data)
    {

        $mc = new mysql();
        $anz = 0;
        try {
        $connector = new NetworkPrintConnector(get_settings("ip_escpos_drucker"), 9100,5);
        } catch (Exception $e) {
            return json_encode(array("status" => "0", "msg" => "Der Drucker konnte nicht erreicht werden ..."));
        }

        $printer = new Printer($connector);

        while($anz < $data["anzahl"])
        {
                if($mc->query("INSERT into essenmarken (typ_id,kostenstelle_id,time_created,wertigkeit) VALUES ('".$data["typ"]."','".$data["kostenstelle"]."','".time()."','".$data["multimark_anz"]."')"))
                {
                $id = $mc->getID();

                $printer-> setTextSize(1,2);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text(get_settings("organisation")."\n\n");

                $printer -> text("Essenmarke\n\n");
                $printer -> text(get_settings("veranstaltung")." ".date("Y")." in ".get_settings("ort")."\n");
                $printer -> feed();
                $printer-> setTextSize(2,1);

                $typ = $mc->fetch_array("SELECT * FROM typ WHERE typ_id = '".$data["typ"]."'");
                $kostenstelle = $mc->fetch_array("SELECT * FROM kostenstellen WHERE kostenstelle_id = '".$data["kostenstelle"]."'");


                $printer->text($typ["bezeichnung"]."\n");

                if($data["multimark_anz"] > 1) {
                    $printer -> feed();
                    $printer -> text("Essenmarke zählt für ".$data["multimark_anz"]." Personen\n\n");
                }


                $printer -> feed();
                $printer-> setTextSize(1,1);

                $printer->setBarcodeHeight(80);
                $printer->barcode("E".$id);
                $printer->text("\nID: ".$id." // ".$kostenstelle["bezeichnung"]."\n\n");

                $printer->text(((int)$typ["preis"]*(int)$data["multimark_anz"])." Euro");
                $printer->feed(2);

                $printer->text(get_settings("essenmarke_eula_text"));
                $printer->feed();

                $printer-> setTextSize(1,1);
                $printer->text(date(get_settings("essenmarke_date_string"),time()));
                $printer->feed();

                if(isset($data["schneiden"])) {
                    $printer->cut();
                }
                else
                {
                    $printer->text("------------------");
                    $printer->feed();
                }
                }
                else
                {
                    echo $mc->getError();
                }
            $anz++;
        }
        if(!isset($data["schneiden"])) {
            $printer->cut();
        }
        $printer -> close();
        return json_encode(array("status" => "1", "msg" => "!", "callback" => "    location.href = \"?s=essenmarke\";", "formcontrol" => ""));

    }
}
