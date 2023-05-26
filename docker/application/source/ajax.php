<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:42
 */
session_start();
include("config.php");

if(isset($_REQUEST["method"])) {
    if (function_exists($_REQUEST["method"])) {
        $fn = $_REQUEST["method"];

        echo $fn($_REQUEST);
    } else
        echo json_encode(array("status" => "0", "msg" => "Methode " . $_REQUEST["method"] . " nicht gefunden!"));
}

if(isset($_REQUEST["str_class"]) AND isset($_REQUEST["str_function"]))
{
    $class = $_REQUEST["str_class"];
    $fn = $_REQUEST["str_function"];

    if(class_exists($class))
    {
        $cl = new $class();
        if(method_exists ($cl,$fn))
        {
            $dat = array();

            if(isset($_REQUEST["extradata"])) {
                $dat["extradata"] = $_REQUEST["extradata"];
            }

                if(isset($_REQUEST["formdata"])) {
                $p = explode("&", $_REQUEST["formdata"]);
                foreach ($p as $key => $value) {
                    $kv = explode("=", $value);
                    $dat[$kv[0]] = urldecode($kv[1]);
                }
            }
            else
            {
                $dat = $_REQUEST;
            }

            echo $cl->$fn($dat);
        }
        else
            echo json_encode(array("status" => 0,"msg" => "Methode ".$fn." existiert nicht in Klasse ".$class));

    }
    else
    echo json_encode(array("status" => 0,"msg" => "Klasse ".$class." existiert nicht"));
}
else
{
    echo json_encode(array("status" => 0,"msg" => "not enough data"));
}