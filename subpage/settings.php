<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */

class subpage_settings extends subpage {

    function getContent($page){
        $this->title = "Einstellungen";


        $table = new autotable();
        $table->init("settings",array("set_name","set_value"));
        $table->id_row = "set_name";
        $table->table_name = "settings";

        $table->set_headername("set_name","Schlüssel");
        $table->set_disabled("set_name");

        $table->set_headername("set_value","Wert");
        $table->edit = true;

        return $this->card($table->getContent(),$this->title);
    }
}
