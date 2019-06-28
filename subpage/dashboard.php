<?php
/**
 * Created by PhpStorm.
 * User: henrik
 * Date: 21.06.2019
 * Time: 00:01
 */


class dashboard extends subpage {

    function get_content($data)
    {
        $mc = new mysql();
        $mahlzeit = new subpage_mahlzeiten();
        $mz = $mahlzeit->get_aktuelle_mahlzeit();

        if($mz) {
            $timeout = 10000;
            $msg = "<div id='header'>" . utf8_encode($mz["bezeichnung"]) . " " . date("d.m.Y", $mz["time_till"]) . "</div>";
            $msg .= "<div id='zeltdorf-container'>";

            $res = $mc->query("SELECT * FROM zeltdorf");
            while ($row = mysqli_fetch_array($res)) {

               $anz = $mc->fetch_array("select count(*) as anz from zeltdorf as z, jugendfeuerwehr as j, teilnehmer as t, teilnehmer_mahlzeit as tm WHERE z.zeltdorf_id= ".$row["zeltdorf_id"]." AND z.zeltdorf_id = j.zeltdorf_id AND t.jf_id = j.jf_id AND tm.teilnehmer_id = t.teilnehmer_id AND tm.mahlzeit_id =".$mz["mahlzeit_id"]);
               $summe = $mc->fetch_array("select count(*) as anz from zeltdorf as z, jugendfeuerwehr as j, teilnehmer as t WHERE z.zeltdorf_id = ".$row["zeltdorf_id"]." AND z.zeltdorf_id = j.zeltdorf_id AND t.jf_id = j.jf_id");

               if($summe["anz"] > 0)
                   $proz = round($anz["anz"] / ($summe["anz"] /100),2);
               else
                   $proz = 0;

                $msg .= "<div class='zeltdorf'>
                    <div class='zeltdorf_name'>" . $row["name"] . " ".$anz["anz"]."/".$summe["anz"]."</div>
                    <div class='zeltdorf_wert'>".$proz."%</div>
                </div>";
            }
            $msg .= "</div>";

            $anz = $mc->fetch_array("select count(*) as anz from teilnehmer as t, teilnehmer_mahlzeit as tm WHERE  tm.teilnehmer_id = t.teilnehmer_id AND tm.mahlzeit_id =".$mz["mahlzeit_id"]);
            $summe = $mc->fetch_array("select count(*) as anz from teilnehmer as t");

            if($summe["anz"] > 0)
                $proz = round($anz["anz"] / ($summe["anz"] /100),2);
            else
                $proz = 0;


            $msg .= "
                        <div id='gesamt'>
                           <div id='gesamt-text'>
                            ".$anz["anz"]." von ".$summe["anz"]." Essen dieser Mahlzeit<br>
                            <div style='width:".$proz."%' class='gesamt-progressbar'>".$proz."%</div>
                           </div>
                        </div>
                ";

            $msg .= "<div id='stats'><img src='http://www.zeltlager-2019.de/QNAP/CloudBackup/webcam/webcamzeltlager/current/current.jpg'></div>";
            $teilnehmer = $mc->fetch_array("select count(*) as anz from teilnehmer as t");
            $mahlzeiten = $mc->fetch_array("select count(*) as anz from mahlzeiten as t");
            $anz = $mc->fetch_array("select count(*) as anz FROM teilnehmer_mahlzeit as tm, mahlzeiten as m WHERE tm.mahlzeit_id = m.mahlzeit_id");

            $gesamt = $teilnehmer["anz"] * $mahlzeiten["anz"];

            if($anz["anz"] > 0)
                $proz = round($anz["anz"] / ($gesamt /100),2);
            else
                $proz = 0;

            $msg .= "
                        <div class='pulldown'>
                        <div id='gesamt'>
                           <div id='gesamt-text'>
                            ".$anz["anz"]." von ".$gesamt." Essen aller Mahlzeiten<br>
                            <div style='width:".$proz."%' class='gesamt-progressbar'>".$proz."%</div>
                           </div>
                        </div>
                ";

            $msg .= "Stand: ".date("H:i:s")." Uhr";

            $msg .= "</div>";
        }
        else
        {
            $timeout = 60000;
        $msg = "<p id='uhr2' class='uhr'></p><br><p class='date'>".date("d.m.Y")."</p>";
        }

        return json_encode(array("status" => 1,"msg" => $msg, "timeout" => $timeout));
    }
}
