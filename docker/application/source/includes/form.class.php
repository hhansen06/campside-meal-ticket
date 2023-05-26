<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 23.03.2018
 * Time: 12:18
 */

class form
{
    var $elements = array();
    var $formname = "";
    var $targetclass = "";
    var $targetfunction = "";
    var $disabled;
    var $disablebtn = false;
    var $disablebtnurl = "";

    function form($formname,$writeable = true){
        $this->writeable = $writeable;
        $this->formname = $formname;
        $this->elements[] = "<form name='form_".$this->formname."' enctype='multipart/form-data' id='form_".$this->formname."'>";
    }

    function add_hidden($name,$value)
    {
        $controlname = preg_replace("/[^a-zA-Z]/", "", $name);
        $id = $this->formname."_".strtolower($controlname);

        $ret = "<input type=\"hidden\" id=\"".$id."\" name=\"".$id."\" value='".$value."'\">";
        $this->elements[] = $ret;
    }

    function add_textarea($name,$value="",$helptext = "")
    {
        $controlname = preg_replace("/[^a-zA-Z]/", "", $name);
        $id = $this->formname."_".strtolower($controlname);

        $ret = "  <div class=\"form-group\">
                       <label for=\"for".$id."\">".$name."</label>
                       <textarea class=\"form-control\" rows=\"5\" id=\"".$id."\" name=\"".$id."\">".$value."</textarea>
                       
                       <small id=\"help".$id."\" class=\"form-text text-muted\">".$helptext."</small>
                </div>";
        $this->elements[] = $ret;
    }

    function add_plaintext($name)
    {

    $ret = "  <div class=\"form-group\">
                       <center><b>".$name."</b></center>
                </div>";
    $this->elements[] = $ret;
    }

    function add_infotext($name)
    {

        $ret = "  <div class=\"form-group\">
                       <small>".$name."</small>
                </div>";
        $this->elements[] = $ret;
    }


    function add_label($name)
    {

        $ret = "  <div class=\"form-group\">
                       <p class='formptext'>".$name."</p>
                </div>";
        $this->elements[] = $ret;
    }



    function add_header($name)
    {

        $ret = "  <div class=\"form-group\">
                       <h5>".$name."</h5>
                </div>";
        $this->elements[] = $ret;
    }


    function add_checkbox($name,$helptext,$checked = false,$id = false)
    {
        $controlname = preg_replace("/[^a-zA-Z]/", "", $name);

        if($id == false)
        $id = $this->formname."_".strtolower($controlname);

        if($checked)
            $checked = " checked ";
        else
            $checked = "";
        $ret = "  <div class=\"form-check\">
                       <input ".$checked." class=\"form-check-input\" type='checkbox' id=\"".$id."\" name=\"".$id."\">
                        <label class=\"form-check-label\" for=\"for".$id."\">".$name."</label>
                       <small id=\"help".$id."\" class=\"form-text text-muted\">".$helptext."</small>
                </div>";

        $this->elements[] = $ret;
    }

    function add_textbox($name,$value="",$placeholder="",$helptext = "",$type= "text",$id = false, $writeable = false)
    {
        $controlname = preg_replace("/[^a-zA-Z]/", "", $name);

        if(!$id)
        $id = $this->formname."_".strtolower($controlname);

        if(!$writeable)
            $disabled = " disabled ";
        else
            $disabled = "";

        $ret = "  <div class=\"form-group\">
                       <label for=\"for".$id."\">".$name."</label>
                       <input ".$disabled." type=\"".$type."\" class=\"form-control\" id=\"".$id."\" name=\"".$id."\" value='".$value."' aria-describedby=\"help".$id."\" placeholder=\"".$placeholder."\">";
        if(strlen($helptext) > 0)
        {
        $ret .="<small id=\"help".$id."\" class=\"form-text text-muted\">Hinweis: ".$helptext."</small></div>";
        }
        else
        {
            $ret .="<small id=\"help".$id."\" class=\"form-text text-muted\"></small></div>";
        }
        $this->elements[] = $ret;
    }

    function add_select($name,$value="",$data,$helptext = "",$id = false, $writeable = false)
    {
        $controlname = preg_replace("/[^a-zA-Z]/", "", $name);
        if(!$id)
        $id = $this->formname."_".strtolower($controlname);

        if(!$writeable)
                 $disabled = "disabled";
                else
                    $disabled = "";

        $ret = "  <div class=\"form-group\">
                       <label for=\"for".$id."\">".$name."</label>  
                       <select ".$disabled." class=\"custom-select\" id=\"".$id."\" name=\"".$id."\">";

                foreach($data as $key => $wert)
                {
                    if($value == $key)
                        $selected = "selected";
                    else
                        $selected = "";

                    $ret .= "<option ".$selected." value='".$key."'>".$wert."</option>";
                }

                $ret .="</select><small id=\"help".$id."\" class=\"form-text text-muted\">".$helptext."</small>
                </div>";
        $this->elements[] = $ret;
    }

    function add_AJAXselect($name,$value="",$class,$method,$data,$helptext = "",$modalfunction, $id = false, $writeable = false)
    {

            $controlname = preg_replace("/[^a-zA-Z]/", "", $name);

            if(!$id)
            $id = $this->formname."_".strtolower($controlname);

        if(!$writeable)
        {
            $disabled = "disabled";
            $width = "12";

        }
        else
        {
            $disabled = "";
            $width = "10";
        }

        $fnnmae = "reloaddata".str_replace(array("-","_"),"",$id);
        $ret = "<div class=\"form-row\">";
        $ret .= "  <div class=\"form-group col-md-".$width."\">
                       <label for=\"for".$id."\">".$name."</label>  
                       <select ".$disabled." class=\"custom-select\" id=\"".$id."\" name=\"".$id."\">";
        $ret .="</select>
                </div>
                ";
        if($writeable) {
            $ret .= "<div class=\"form-group col-md-2\">
                <label for=\"inputAddress\">&nbsp;</label>
                   <a class='btn btn-primary form-control' href='#' onclick=\"ajax_modal('" . $class . "','" . $modalfunction . "','" . $data . "','" . $id . "');\" id='addnew_" . $id . "'>Auswahlliste bearbeiten</a>
                </div>";
        }

        $ret .="</div>
                <script>
                function ".$fnnmae."()
                {
                 form_load_select_data('" . $id . "','" . $class . "','" . $method . "','" . $data . "','" . $value . "');
                 }
                 ".$fnnmae."();
                </script>
                
                ";
        $this->elements[] = $ret;
    }

    function setTargetClassFunction($class,$function)
    {
        $this->targetclass = $class;
        $this->targetfunction = $function;
    }

    var $buttontext = "Speichern";

    function  getContent()
    {
        if ($this->writeable) {
            $this->elements[] = "<a class='btn btn-primary btn-sm btn' href='#' 
            onclick=\"send_form('" . $this->targetclass . "','" . $this->targetfunction . "','form_" . $this->formname . "')\">" . $this->buttontext . "</a>";
        }
        else
        {
            if($this->disablebtn)
            $this->elements[] = "<a class='btn btn-primary btn-sm btn' href='".$this->disablebtnurl."'>Nächster Schritt</a>";
        }
        $this->elements[] = "</form>";

        $this->elements[] = "
<script>
    $(function () {
    $('#form_".$this->formname."').submit(function() {
        send_form('".$this->targetclass."','".$this->targetfunction."','form_".$this->formname."');
    return false;
    });});
</script>";

        $ret = "";
        foreach($this->elements as $elem)
        {
            $ret .= $elem;
        }
        return $ret;
    }
}