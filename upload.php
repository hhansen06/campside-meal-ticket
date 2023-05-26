<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 25.09.2018
 * Time: 16:31
 */
include("config.php");
if($_FILES["file"]["error"] == "0")
{
    $id = (int)$_POST["id"];
    $extension = pathinfo($_FILES["file"]["name"]);
    $dstpath ="images/logos/".$id.".".$extension['extension'];

    if(move_uploaded_file($_FILES["file"]["tmp_name"],$dstpath))
    {
        $mc = new mysql();
        $mc->query("UPDATE unternehmen set image = '".$dstpath."' WHERE unternehmen_id = '".$id."'");
        echo json_encode(array("status" => "1","url" => $dstpath));
    }

}

