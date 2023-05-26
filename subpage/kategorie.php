<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */

class subpage_kategorie extends subpage {

    function getContent($page){
            $this->title = "Kategorie";

            $sql = "SELECT  * FROM kategorie ORDER BY name ASC";
        $table = new table();
        $table->addHeader("Bezeichnung der Kategorie");
        $table->addHeader("Bearbeiten");

        $res = $page->sql->query($sql);

        echo $page->sql->getError();

            while ($row = mysqli_fetch_array($res)) {
                $row_data = array();

                $row_data[] = $row["name"];

                $ret ="
                     <a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=\"bearbeiten\">
                        <span class=\"fas fa-cog lnk-blue\"></span>
                      </a>
                  ";

                $row_data[] = $ret;

                $table->addRow($row_data);
            }

        $ret = "<a class='btn btn-primary btn-sm' href='#' onclick=\"ajax_modal('subpage_kategorie','modal_add_kategorie','','')\">Neue Kategorie anlegen</a>";

        if(is_admin()) {
            return $this->card($ret . $table->getContent(), $this->title);
        }
        else
            return "";
    }

    function modal_add_kategorie ($data)
    {
        $ms = new mysql();
        $form = new form("add_kategorie");
        $form->add_textbox("Name der Kategorie","","","","text",false,true);

        $form->setTargetClassFunction("subpage_kategorie","save_kategorie");
        return json_encode(array("status" => "1","content" => $form->getContent(),"header"=> "Neue Kategorie anlegen"));
    }

    function save_kategorie($data)
    {
        $mc = new mysql();
        if(strlen($data["add_kategorie_namederkategorie"]) > 0) {
            $sql = "INSERT into kategorie (name) VALUES ('".$data["add_kategorie_namederkategorie"]."')";
            if ($mc->query($sql))
                return json_encode(array("status" => "1", "msg" => "!", "callback" => "    location.href = \"?s=kategorie\";", "formcontrol" => ""));
            else
                return json_encode(array("status" => "0", "msg" => "DB ERROR: " . $mc->getError()));
        }
    }
}
