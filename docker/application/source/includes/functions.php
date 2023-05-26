<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 20.04.2018
 * Time: 08:30
 */


function getWhereVorlagen()
{
    $orgas = array("organisation_id = 0");
    $groups = array("gruppe_id = 0");
    foreach ($_SESSION['user']->gruppen as $key => $value) {
        $groups[] = "gruppe_id = " . $value["gruppe_id"];
        $orgas[] =  "organisation_id = " . $value["organisation_id"];
    }

    $sql = "(" . implode(" OR ", $orgas) . ") AND (" . implode(" OR ", $groups) . ")";
    return $sql;
}

function set_history($typ,$ausweis_id,$action,$payload)
{
    $mc = new mysql();
    if(get_history_value($action))
    {
        $mc->query("INSERT into historie (typ,time,ausweis_id,action,payload) VALUES ('".$typ."',UNIX_TIMESTAMP(),'".$ausweis_id."','".$action."','".$payload."')");
    }
    else
        throw new Exception('History type unknown!');
}

function get_history($typ,$id)
{
    $ret = array();
    $mc = new mysql();
    $res = $mc->query("SELECT * FROM historie WHERE typ = '".$typ."' AND ausweis_id = '".$id."' ORDER by time ASC");
    while($row = mysqli_fetch_array($res))
    {
        $row["msg"] = sprintf(get_history_value($row["action"]),$row["payload"]);
        $ret[] = $row;
    }
    return $ret;
}

function get_history_value($action)
{
    $art = array(
        1 => "Essenmarke eingelöst an Kasse %s",
        2 => "Ungültige Essenmarke an Kasse %s",
        3 => "Essenmarke zur falschen Mahlzeit an Kasse %s gescannt",
        4 => "Essenmarke zurückgesetzt durch Benutzer %s",


        10 => "Ausweis gescannt an Kasse %s",
        11 => "Ausweis bereits gegessen an Kasse %s",
        12 => "Ausweis gescannt ohne aktive Mahlzeit an Kasse %s",

    );

    if(isset($art[$action]))
        return $art[$action];
    else
        return false;
}