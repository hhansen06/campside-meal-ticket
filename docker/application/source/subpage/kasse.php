<?php
/**
 * Created by PhpStorm.
 * User: henrik
 * Date: 09.06.2019
 * Time: 01:12
 */


class kasse extends subpage {

    function getstats()
    {
        return $this->lastbarcode;
    }

    var $lastbarcode;

    function check_barcode($data)
    {
        $this->lastbarcode = $data["barcode"];
        if(startsWith($data["barcode"],"*E"))
        {
            return $this->check_essenmarke($data);
        }
        if(startsWith($data["barcode"],"*K"))
        {
            return $this->konfigure_card($data);
        }
        if(startsWith($data["barcode"],"*"))
        {
                return $this->check_teilnehmer($data);
        }

            $data = array();
            $data["stats"] = $this->getstats();
            $data["status"] = 0;
            $data["msg"] = "Barcode unbekannt!";
            return $data;
    }
    function check_teilnehmer($data)
    {
        $id = substr($data["barcode"], 1);

        $mahlzeit = new subpage_mahlzeiten();
        $aktive_mahlzeit = $mahlzeit->get_aktuelle_mahlzeit();
        if(!$aktive_mahlzeit)
        {
            $data["stats"] = $this->getstats();
            $data["status"] = 0;
            $data["msg"] = "Keine Mahlzeit aktiv!";
            set_history("ausweis",$id,12,$this->get_kasse_id_by_mac($data["mac"]));
        }
        else {
            $mc = new mysql();
            $teilnehmer = $mc->fetch_array("select * from jugendfeuerwehr as j, teilnehmer as t WHERE j.jf_id = t.jf_id AND t.teilnehmer_id = ".$id);
            $teilnehme_mahlzeit = $mc->fetch_array("SELECT * FROM teilnehmer_mahlzeit WHERE teilnehmer_id = '" . $id . "' AND mahlzeit_id = '".$aktive_mahlzeit["mahlzeit_id"]."'");
            if(!isset($teilnehme_mahlzeit["teilnehmer_mahlzeit_id"]))
            {
                $error = true;
                $errormsg = "";
                if(get_settings("check_korrekt_zeltdorf") == 1)
                {
                    if($teilnehmer["zeltdorf_id"] == get_settings("aktuelles_zeltdorf_id"))
                    {
                        $error = false;
                    }
                    else
                    {
                        if($teilnehmer["zeltdorf_id"] == 0)
                        {
                            $error = false;
                        }
                        else {
                            if(get_settings("unlock_all_zeltdoerfer") > time())
                            {
                                $error = false;
                            }
                            else {
                                $error = true;
                                $errormsg = "falsches Zeltdorf!";
                            }
                        }
                    }
                }
                else
                {
                    $error = false;
                }

                if(!$error) {
                    $mc->query("INSERT into teilnehmer_mahlzeit (teilnehmer_id,mahlzeit_id,time,kasse) VALUES (" . $id . "," . $aktive_mahlzeit["mahlzeit_id"] . "," . time() . "," . $this->get_kasse_id_by_mac($data["mac"]) . ")");
                    $data["stats"] = $this->getstats();
                    $data["status"] = 1;
                    $data["msg"] = "OK!";
                    set_history("ausweis", $id, 10, $this->get_kasse_id_by_mac($data["mac"]));
                }
                else
                {
                    $data["stats"] = $this->getstats();
                    $data["status"] = 0;
                    $data["msg"] = $errormsg;
                }

            }
            else
            {
                $data["stats"] = $this->getstats();
                $data["status"] = 0;
                $data["msg"] = "bereits gegessen!";
                set_history("ausweis",$id,11,$this->get_kasse_id_by_mac($data["mac"]));
            }


        }

        return json_encode($data);
    }

    function konfigure_card($data)
    {
        if($data["barcode"] == "*K111") {

            if((int)get_settings("check_korrekt_zeltdorf") == 1)
            {
                set_settings("check_korrekt_zeltdorf",0);
                $data = array();
                $data["stats"] = $this->getstats();
                $data["status"] = 1;
                $data["msg"] = "Dorfsperre deaktiviert!";
                return json_encode($data);

            }
            else
            {
                set_settings("check_korrekt_zeltdorf",1);
                $data = array();
                $data["stats"] = $this->getstats();
                $data["status"] = 1;
                $data["msg"] = "Dorfsperre aktiv!";
                return json_encode($data);
            }
        }
        else {
        if($data["barcode"] == "*K110") {
            $time = time() + ((int)get_settings("unlock_time")*60);
            set_settings("unlock_all_zeltdoerfer",$time);

            $data = array();
            $data["stats"] = $this->getstats();
            $data["status"] = 1;
            $data["msg"] = "Entsperrt bis: ".date("H:i",$time);
            return json_encode($data);
         }
        else {
            $mc = new mysql();
            $zeltdorf = $mc->fetch_array("SELECT * FROM zeltdorf WHERE barcode = '" . substr($data["barcode"], 1) . "'");
            set_settings("aktuelles_zeltdorf_id", $zeltdorf["zeltdorf_id"]);

            $data = array();
            $data["stats"] = $this->getstats();
            $data["status"] = 1;
            $data["msg"] = $zeltdorf["name"];
            return json_encode($data);
        }
        }
    }

    function check_essenmarke($data)
    {
        $mahlzeit = new subpage_mahlzeiten();
        $aktive_mahlzeit = $mahlzeit->get_aktuelle_mahlzeit();
        if(!$aktive_mahlzeit)
        {
            $data["stats"] = $this->getstats();
            $data["status"] = 0;
            $data["msg"] = "Keine Mahlzeit aktiv!";
        }
        else {
            $id = substr($data["barcode"], 2);
            $mc = new mysql();

            $essenmarke = $mc->fetch_array("SELECT * FROM essenmarken WHERE essenmarke_id = '" . $id . "'");

            if (isset($essenmarke["essenmarke_id"])) {
                if ($aktive_mahlzeit["typ_id"] == $essenmarke["typ_id"]) {
                    if ($essenmarke["status"] == 0) {
                        $data["stats"] = $this->getstats();
                        $data["status"] = 1;
                        $data["msg"] = "Essenmarke OK!";
                        set_history("essenmarke",$id,1,$this->get_kasse_id_by_mac($data["mac"]));
                        $mc->query("UPDATE essenmarken SET status = 1, mahlzeit_id = '".$aktive_mahlzeit["mahlzeit_id"]."' WHERE essenmarke_id = ".$id);

                    } else {
                        $data["stats"] = $this->getstats();
                        $data["status"] = 0;
                        $data["msg"] = "bereits eingelöst!";
                        set_history("essenmarke",$id,2,$this->get_kasse_id_by_mac($data["mac"]));
                    }
                } else {
                    $data["stats"] = $this->getstats();
                    $data["status"] = 0;
                    $data["msg"] = "Falsche Mahlzeit";
                    set_history("essenmarke",$id,3,$this->get_kasse_id_by_mac($data["mac"]));
                }
            } else {
                $data["stats"] = $this->getstats();
                $data["status"] = 0;
                $data["msg"] = "Marke unbekannt ?!";
            }
        }
        return json_encode($data);
    }

    function get_kasse_id_by_mac($mac)
    {
        $mc = new mysql();
        $kasse = $mc->fetch_array("SELECT * FROM kasse WHERE mac = ".$mac);

        if(isset($kasse["kasse_id"]))
            return $kasse["kasse_id"];
        else
            return false;
    }

    function add_ausweis($data)
    {
        $id = $data["id"];
        $jf = $data["jf"];
        $vorname = utf8_encode($data["vorname"]);
        $nachname = utf8_encode($data["nachname"]);
        $jf_id = $this->get_jfid_by_name(utf8_encode($jf));

        $mc = new mysql();
         $mc->query("INSERT into teilnehmer (teilnehmer_id,vorname,nachname,jf_id) VALUES ('".$id."','".$vorname."','".$nachname."','".$jf_id."')
          ON DUPLICATE key UPDATE vorname = '".$vorname."', nachname = '".$nachname."', jf_id = '".$jf_id."'");
         echo $mc->getError();
    }

    function get_jfid_by_name($name)
    {
        $mc = new mysql();
        $data = $mc->fetch_array("SELECT * FROM jugendfeuerwehr WHERE name = '".$name."'");
        if(!isset($data["jf_id"]))
        {
            $data = $mc->query("INSERT into jugendfeuerwehr (name) VALUES ('".$name."')");
            $jf_id = $mc->getID();
        }
        else
            $jf_id = $data["jf_id"];

        return $jf_id;
    }
}