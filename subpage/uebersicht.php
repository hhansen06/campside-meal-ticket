<?php
/**
 * Created by PhpStorm.
 * User: svhh
 * Date: 09.07.2019
 * Time: 15:23
 */


class subpage_uebersicht extends subpage
{
    function getContent($page)
    {
        $mc = new mysql();

        $ret = "<table width='100%' border='1'>";
        $ret .= "<tr>";
        $ret .= "<td>Zeltdorf</td>";
        $ret .= "<td>Essenmarken</td>";
        $res1 = $mc->query("SELECT * FROM zeltdorf");
        while($row1 = mysqli_fetch_array($res1))
        {
            $ret .= "<td>".$row1["name"]."</td>";
        }
        $ret .= "<td>Alle Teilnehmer</td>";
        $ret .= "<td>Summe</td>";
        $ret .= "</tr>";


        $res = $mc->query("SELECT * FROM mahlzeiten as m, typ as t WHERE m.typ_id = t.typ_id ORDER BY m.time_from ASC,m.typ_id ASC");
        while($row = mysqli_fetch_array($res))
        {
            $ret .= "<tr>";
            $ret .= "<td>".$row["bezeichnung"]." ".date("d.m.Y",$row["time_from"])."</td>";

            $anz_essenmarken = $mc->fetch_array("SELECT sum(wertigkeit) AS anz, count(*) AS anz2 FROM essenmarken where mahlzeit_id = '".$row["mahlzeit_id"]."'");
            $ret .= "<td>".$anz_essenmarken["anz"]."</td>";


            $res1 = $mc->query("SELECT * FROM zeltdorf");
            while($row1 = mysqli_fetch_array($res1))
            {
                $anz_teilnehmer = $mc->fetch_array("SELECT count(*) as anz FROM teilnehmer_mahlzeit as tm, teilnehmer as t, jugendfeuerwehr as j where t.jf_id = j.jf_id AND t.teilnehmer_id = tm.teilnehmer_id AND tm.mahlzeit_id = '".$row["mahlzeit_id"]."' AND j.zeltdorf_id = '".$row1["zeltdorf_id"]."'");

                $ret .= "<td>".$anz_teilnehmer["anz"]."</td>";
            }


            $anz_teilnehmer = $mc->fetch_array("SELECT count(*) as anz FROM teilnehmer_mahlzeit where mahlzeit_id = '".$row["mahlzeit_id"]."'");
            $ret .= "<td>".$anz_teilnehmer["anz"]."</td>";
            $ret .= "<td>".((int)$anz_essenmarken["anz"]+(int)$anz_teilnehmer["anz"])."</td>";
            $ret .= "</tr>";

        }
        $ret .= "</table>";

       return $ret;
    }
}