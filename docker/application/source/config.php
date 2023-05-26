<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 21.03.2018
 * Time: 12:17
 */

define("MYSQL_PASSWORT",getenv('MYSQL_PASSWORD'));
define("MYSQL_USER",getenv('MYSQL_USER'));
define("MYSQL_DATENBANK",getenv('MYSQL_DATABASE'));
define("MYSQL_SERVER","database");

define("FPDF_FONTPATH","fonts/");

define("HOMECLASS","subpage_uebersicht");

define("PAGETITLE","Zeltlager");

define("BASEURL",getenv('BASE_URL'));

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

$mc = new mysql();
if(!$mc->connected)
{
   echo alert( "Datenbankfehler!","Es konnte keine Verbindung zur Datenbank <b>".MYSQL_DATENBANK."</b> hergestellt werden.","error");
   die();
}
