<?php
/**
 * Created by PhpStorm.
 * User: henrik
 * Date: 25.11.2018
 * Time: 12:52
 */



function get_settings($name)
{
    $mc = new mysql();
    $data = $mc->fetch_array("SELECT set_value FROM settings WHERE set_name LIKE '".$name."'");
    if (isset($data["set_value"]))
    {
        return $data["set_value"];
    }
    else
    {
        $mc->query("INSERT into settings (set_name,set_value) VALUES ('".$name."','')");
        return false;
    }

}

function setting_exits($name)
{
    $mc = new mysql();

    $data = $mc->fetch_array("SELECT set_value FROM settings WHERE set_name LIKE '".$name."'");
    if (isset($data["set_value"]))
    {
        return true;
    }
    else
    {
        return false;
    }

}

function set_settings($name, $value)
{
    $mc = new mysql();
    if(setting_exits($name))
    {
        $mc->query("UPDATE settings set set_value = '".$value."' WHERE set_name = '".$name."'");
    }
    else
    {
        $mc->query("INSERT into settings (set_name,set_value) VALUES ('".$name."','".$value."')");
    }
}
?>