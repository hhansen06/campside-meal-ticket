<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */

class subpage_statistik extends subpage {

    function getContent($page){

        $jf = $page->sql->fetch_array("SELECT * FROM jugendfeuerwehr WHERE jf_id = '".$_GET["jf_id"]."'");
        $this->title = "Statistik für JF ".$jf["name"];


        $ret = "<table width='100%' border='1'>";

        $ret .= "<thead><tr>";
        $ret .= "<th>&nbsp;</th>";
        $res = $page->sql->query("SELECT * FROM mahlzeiten as m, typ as t WHERE m.typ_id = t.typ_id ORDER BY m.time_from ASC, m.typ_id ASC");
        $mahlzei_id = array();
        while($mahlzeit = mysqli_fetch_array($res))
        {
            $mahlzei_id[ $mahlzeit["mahlzeit_id"]] =$mahlzeit;
            $mahlzei_id[$mahlzeit["mahlzeit_id"]]["anz"] = 0;
            $ret.= "<th>".$mahlzeit["mahlzeit_id"]."</th>";
        }
        $ret.= "</tr></thead>";


        $res = $page->sql->query("SELECT * FROM teilnehmer where jf_id = '".$jf["jf_id"]."'");
        while($teilnehmer = mysqli_fetch_array($res))
        {
            $ret .= "<tr>";
                $ret .= "<td>".$teilnehmer["nachname"].", ".$teilnehmer["vorname"]."</td>";

                foreach($mahlzei_id as $key => $value)
                {
                    $hat_gegessen = $page->sql->fetch_array("SELECT count(*) as anz FROM teilnehmer_mahlzeit WHERE teilnehmer_id = '".$teilnehmer["teilnehmer_id"]."' AND mahlzeit_id = '".$key."'");
                    if($hat_gegessen["anz"] == 0)
                    {
                        $ret .= "<td>&nbsp;</td>";
                    }
                    else {
                        $ret .= "<td>X</td>";
                        $mahlzei_id[$key]["anz"] = $mahlzei_id[$key]["anz"] + 1;
                    }
                }
            $ret .= "</tr>";
        }
            $ret .= "<tr>";
            $ret .= "<td>Summe:</td>";

            foreach($mahlzei_id as $key => $value)
            {

                    $ret .= "<td>".$value["anz"] ."</td>";
            }
            $ret .= "</tr>";


        
        $ret.="</table>";

        foreach($mahlzei_id as $key => $value)
        {
            $ret.= $key.": ".date("d.m.Y",$value["time_from"])." ".utf8_encode($value["bezeichnung"])."<br>";
        }


        return $this->card($ret,$this->title);
    }
}
