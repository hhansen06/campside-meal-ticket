<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 21.03.2018
 * Time: 12:17
 */

define("MYSQL_PASSWORT","");
define("MYSQL_USER","root");
define("MYSQL_DATENBANK","zeltlager");
define("MYSQL_SERVER","localhost");

define("FPDF_FONTPATH","fonts/");

define("HOMECLASS","subpage_unternehmen");

define("PAGETITLE","Zeltlager");

define("BASEURL","http://172.18.5.132/");

ini_set("display_errors","1");

include("vendor/autoload.php");

foreach(scandir("includes/",SCANDIR_SORT_ASCENDING ) as $file)
{
    if($file != "." AND $file != "..")
    {
        if(is_file("includes/".$file)) {
            include("includes/" . $file);
        }
        else
        {
            // INCLUDE REKURSIV FOLDERS
            foreach(scandir("includes/".$file,SCANDIR_SORT_ASCENDING ) as $subfile)
            {
                if($subfile != "." AND $subfile != "..")
                {
                    if(is_file("includes/".$file."/".$subfile)) {
                        include("includes/" . $file . "/" . $subfile);
                    }
                }
            }
        }
    }
}

foreach(scandir("subpage/") as $file)
{
    if($file != "." AND $file != "..")
    {
        if(is_file("subpage/".$file)) {
            include("subpage/" . $file);
        }
    }
}

define("basemenu", array(
    "Essenmarken" => array("class" => "essenmarke"),
    "Teilnehmer" => array("class" =>  "teilnehmer"),
));

define("configmenu", array(
    "Mahlzeiten" => array("class" =>  "mahlzeiten"),
    "Jugendfeuerwehren" => array("class" =>  "jugendfeuerwehren"),
    "Zeltdörfer" => array("class" =>  "zeltdoerfer"),
    "Kostenstellen" => array("class" =>  "kostenstellen"),
    "Systemeinstellungen" => array("class" =>  "settings"),
));

$mc = new mysql();
if(!$mc->connected)
{
   echo alert( "Datenbankfehler!","Es konnte keine Verbindung zur Datenbank <b>".MYSQL_DATENBANK."</b> hergestellt werden.","error");
   die();
}
