<?php
/**
 * Created by PhpStorm.
 * User: d12hanse
 * Date: 25.09.2018
 * Time: 16:27
 */


function image($id,$urlimage)
{

    If(strlen($urlimage) == 0)
        $image = "<image class='previewimg' id='imagepreview'></image>";
    else
        $image = "<image class='previewimg' id='imagepreview' src='".$urlimage."'></image>";



    return $image."
    <form id='image_upload' enctype=\"multipart/form-data\">
    <input name=\"file\" type=\"file\" />
    <input type='hidden' name='id' value='".$id."'>
    <input type=\"button\" value=\"Hochladen\" />
</form>
<script>

$(':button').on('click', function() {
    $.ajax({
        // Your server script to process the upload
        url: 'upload.php',
        type: 'POST',

        // Form data
        data: new FormData($('#image_upload')[0]),

        // Tell jQuery not to process data or worry about content-type
        // You *must* include these options!
        cache: false,
        contentType: false,
        processData: false,

        // Custom XMLHttpRequest
        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                // For handling the progress of the upload
                myXhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        $('progress').attr({
                            value: e.loaded,
                            max: e.total,
                        });
                    }
                } , false);
            }
            return myXhr;
        }
    }).done(function( msg ) {
               var obj = jQuery.parseJSON(msg);
               if(obj.status == 1)
                   {
                       $('#imagepreview').attr('src', obj.url);
                   }
          });
});
</script>

";
}