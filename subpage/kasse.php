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
        return "...";
    }

    function check_barcode($data)
    {
        if(startsWith($data["barcode"],"*E"))
        {
            return $this->check_essenmarke($data);
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

            $teilnehme_mahlzeit = $mc->fetch_array("SELECT * FROM teilnehmer_mahlzeit WHERE teilnehmer_id = '" . $id . "' AND mahlzeit_id = '".$aktive_mahlzeit["mahlzeit_id"]."'");
            if(!isset($teilnehme_mahlzeit["teilnehmer_mahlzeit_id"]))
            {
                $mc->query("INSERT into teilnehmer_mahlzeit (teilnehmer_id,mahlzeit_id,time,kasse) VALUES (".$id.",".$aktive_mahlzeit["mahlzeit_id"].",".time().",".$this->get_kasse_id_by_mac($data["mac"]).")");
                $data["stats"] = $this->getstats();
                $data["status"] = 1;
                $data["msg"] = "OK!";
                set_history("ausweis",$id,10,$this->get_kasse_id_by_mac($data["mac"]));

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

    function batterie_state($data)
    {
        // Port: 32768
        $database = InfluxDB\Client::fromDSN(sprintf('influxdb://user:pass@%s:%s/%s', get_settings("influx_ip"),  get_settings("influx_port"),  get_settings("influx_database")));

        $points = array(
            new InfluxDB\Point(
                "kasse", // name of the measurement
                null,
                ['host' => $data["mac"]], // optional tags
                ['batterie' => (float)$data["batterie"]],
                time()
            )
        );
        $result = $database->writePoints($points, InfluxDB\Database::PRECISION_SECONDS);
        print_r($result);
        $mc = new mysql();
        $mc->query("INSERT into kasse (mac,battstate,time_last_seen) VALUES ('".$data["mac"]."','".$data["batterie"]."','".time()."') ON DUPLICATE KEY UPDATE time_last_seen = '".time()."', battstate = '".$data["batterie"]."'");
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
        $vorname = $data["vorname"];
        $nachname = $data["nachname"];
        $jf_id = $this->get_jfid_by_name($jf);

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