<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 22.03.2018
 * Time: 13:58
 */

class subpage_jugendfeuerwehren extends subpage {

    function getContent($page){
        $this->title = "Jugendfeuerwehren";



        $table = new autotable();
        $table->add_customrow("statistik");

        $table->add_customrow("buttons");
        $table->init("jugendfeuerwehr",array("jf_id","name","zeltdorf_id"));
        $table->id_row = "jf_id";
        $table->table_name = $this->title;


        $table->set_disabled("jf_id");
        $table->set_headername("jf_id","# ID");
        $table->set_headername("name","Bezeichnung");
        $table->set_headername("zeltdorf_id","Zeltdorf");
        $table->set_headername("buttons","Auswählen");

        $zeltdoerfer = array();
        $res = $page->sql->query("SELECT * FROM zeltdorf");

        $select = "";
        while($row = mysqli_fetch_array($res))
        {
            $zeltdoerfer[$row["zeltdorf_id"]] = utf8_encode($row["name"]);
            $select .= "<option value='".$row["zeltdorf_id"]."'>".utf8_encode($row["name"])."</option>";
        }

        $table->set_fieldsource("zeltdorf_id",$zeltdoerfer);


        $btn = "JF einem Zeltdorf zuordnen: <select id='selectedzeltdorf'>".$select."</select>  <a class='btn btn-primary btn-sm' href='#' onclick=\"send_form('subpage_jugendfeuerwehren','modal_set_jf','editjf',$('#selectedzeltdorf').val())\">Zeltdorf setzen</a><br>";

        function gen_checkbox($data)
        {
            return "<input style='zoom:1.5;' type='checkbox' name='".$data["jf_id"]."'>";
        }
        function gen_checkbox_sta($data)
        {
            return "<a href='index.php?s=statistik&jf_id=".$data["jf_id"]."'>Statistik</a>";
        }
        $table->set_fieldfunction("statistik","gen_checkbox_sta");

        $table->set_fieldfunction("buttons","gen_checkbox");
        return $this->card($btn."<form name='editjf' id='editjf'>".$table->getContent()."</form>",$this->title);
    }

    function modal_set_jf($data)
    {
        $zeltdorf_id  = $data["extradata"];
        unset($data["extradata"]);

        $mc = new mysql();
        foreach($data as $jf_id => $value)
        {
          $sql = "UPDATE jugendfeuerwehr set zeltdorf_id = '".$zeltdorf_id."' WHERE jf_id = '".$jf_id."'";
          echo $sql;
            $mc->query($sql);
        }
    }
}
