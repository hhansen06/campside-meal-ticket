function showcreateaccount() {
    $('#login').hide();
    $('#register').show();
}
function cancelcreateaccount() {
    $('#login').show();
    $('#register').hide();
}

function createaccount() {

    if($('#passwortneu').val() == $('#passwort1neu').val())
    {
        ajax_action_class("user","addaccount","createaccountfinished",$('#passwortneu').val(),$('#benutzernameneu').val());
    }
    else
        alert("Passwort und Passwort wiederholung stimmen nicht überein!");
}
function createaccountfinished(data)
{
    if(data.status == 1)
        addAlert(data.header, data.msg, 'success');
}

function send_form(str_class,str_function,formname,extradata)
{
    var data = $('#' +formname).serialize();
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: { str_class: str_class,str_function:str_function,formdata: data, extradata:extradata}
    })
        .done(function( msg ) {

            var obj = jQuery.parseJSON(msg );
            if (obj.status == 1)
            {
                if(obj.location != null)
                {
                   location.href = obj.location;
                }
                if(obj.callback != null)
                {
                    var fnname = obj.callback;
                    eval(fnname)(obj);
                }
                if(obj.alert != null)
                {
                    addAlert(obj.title,obj.message,obj.type);
                }
            }
            if (obj.status == 0) {
                addAlert("AJAX Method Fehler!", obj.msg, 'danger');
            }
            if (obj.status == 2) {
                addAlert(obj.header, obj.msg, 'warning');
            }


        })
        .fail(function( msg,errormsg,errmsgtext ) {
            addAlert("AJAX Fehler!","Fehler in der Funktion ajax_action: " + errmsgtext,'danger');
        });

}

function ajax_action(method,callback)
{
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: { method: method }
    })
        .done(function( msg ) {

            var obj = jQuery.parseJSON(msg );
            if (obj.status == 1)
            {
                callback(obj)
            }
            else
                addAlert("AJAX Method Fehler!",obj.msg,'danger');


        })
        .fail(function( msg,errormsg,errmsgtext ) {
        addAlert("AJAX Fehler!","Fehler in der Funktion ajax_action: " + errmsgtext,'danger');
        });

}

function ajax_getasync_Content(str_class,str_function,data)
{
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: { str_class: str_class,str_function:str_function,extradata: data}
    })
        .done(function( msg ) {

         $('#subcontentarea').html(msg);

        })
        .fail(function( msg,errormsg,errmsgtext ) {
            addAlert("AJAX Fehler!","Fehler in der Funktion ajax_action: " + errmsgtext,'danger');
        });

}

function ajax_action_class(str_class,str_function,callback,data,data1)
{
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: { str_class: str_class,str_function:str_function,data:data,data1:data1}
    })
        .done(function( msg ) {

            var obj = jQuery.parseJSON(msg );
            if (obj.status == 1)
            {
                eval(callback)(obj);
            }
            else {
                if(obj.hasOwnProperty("msgheader"))
                    addAlert(obj.msgheader, obj.msg, 'danger');
                else
                    addAlert("FEHLER", obj.msg, 'danger');
            }

        })
        .fail(function( msg,errormsg,errmsgtext ) {
            addAlert("AJAX Fehler!","Fehler in der Funktion ajax_action: " + errmsgtext,'danger');
        });

}

function addAlert(title,message,type) {
    $('.alert').remove();
    $('#contentarea').prepend(
        '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        '  <strong>' + title + '</strong><br>' +
        '<button type="button" class="close" data-dismiss="alert">' +
        '&times;</button>' + message + '</div>');
}

function location_index(obj)
{
    location.href = "index.php?show=home";
}
function location_reload(obj)
{
    location.reload();
}


function form_load_select_data(formid,classname,functionname,data,value)
{
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: { str_class: classname,str_function:functionname,data:data }
    })
        .done(function( msg ) {
            var obj = jQuery.parseJSON(msg );
            if (obj.status == 1)
            {
                $('#' + formid).find('option').remove();
                $.each(obj.data, function (i, item) {
                    var selected = false;
                    if(item.val == value)
                    {
                        selected = true;
                    }

                    $('#' + formid).append($('<option>', {
                        value: item.val,
                        text : item.label,
                        selected: selected
                    }));

                });
            }
            if (obj.status == 0) {
                addAlert("AJAX Method Fehler!", obj.msg, 'danger');
            }
                    })
        .fail(function( msg,errormsg,errmsgtext ) {
            addAlert("AJAX Fehler!","Fehler in der Funktion ajax_action: " + errmsgtext,'danger');
        });

}

function ajax_modal(classname,modalname,data,controlname)
{
    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: { str_class: classname,str_function:modalname,data:data,formcontrolname: controlname }
    })
        .done(function( msg ) {
            var obj = jQuery.parseJSON(msg );
            if (obj.status == 1)
            {
                var header = obj.header;
                var content = obj.content;
                doModal(header,content);
            }
            if (obj.status == 0) {
                addAlert("AJAX Method Fehler!", obj.msg, 'danger');
            }
        })
        .fail(function( msg,errormsg,errmsgtext ) {
            addAlert("AJAX Fehler!","Fehler in der Funktion ajax_action: " + errmsgtext,'danger');
        });
}

function ajax_modal_callback(obj){
    hideModal();
    var fnname = obj.formcontrol;
    eval(fnname)(obj);
}

function doModal(heading, formContent) {
     $('#modal_body').html(formContent);
    $('#modal_title').html(heading);
    $("#modal_page").modal('show');
    //$("#dynamicModal").modal('show');
    //$('#dynamicModal').on('hidden.bs.modal', function (e) {
    //    $(this).remove();
   // });
}

function hideModal()
{
    // Using a very general selector - this is because $('#modalDiv').hide
    // will remove the modal window but not the mask
    $('#modal_page').modal('hide');
}

function show_datamatrix(daten)
{
    $('#contentmatrix').html(daten.content);
    console.log(daten);
}

function upload_file_async(controlname,str_class,str_function)
{
    $.ajax({
        // Your server script to process the upload
        url: 'ajax.php',
        type: 'POST',

        // Form data
        data: new FormData($('form')[0]),

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
    });
}

function hide_dom_obj_afterdelete_file(id)
{
    console.log(id.id);
    $("#" +id.id).hide();
    reloadpage();
}


function upload_file(inputid,class_str,function_str,documentid,documenttyp,reload)
{
    var file_data = $('#'+inputid).prop('files')[0];
    var form_data = new FormData();
    form_data.append('file', file_data);
    form_data.append('str_class', class_str);
    form_data.append('str_function', function_str);
    form_data.append('documentid', documentid);
    form_data.append('documenttyp', documenttyp);

    console.log("Uploading file from inputID " + inputid + " to class " + class_str + " with remote function " + function_str + " Type of Document: "+ documenttyp + "with ID " + documentid );

    $.ajax({
        url: 'ajax.php', // point to server-side PHP script
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(msg){
            var obj = jQuery.parseJSON(msg );
            if (obj.status == 1)
            {
                if(reload == 1)
                {
                    location.reload();
                }
                else {
                    eval(obj.callback)(obj);
                }
            }
            else
                addAlert("AJAX UPLOAD Fehler!",obj.msg,'danger');
        }
    });
}

function update_dashboard()
{
    var timeout = 5000;

    $.ajax({
        method: "POST",
        url: "ajax.php",
        data: { str_class: "dashboard",str_function:"get_content"}
    })
        .done(function( msg ) {

            var obj = jQuery.parseJSON(msg );

            if(Number.isInteger(obj.timeout))
                var timeout = obj.timeout;
            else
                var timeout = 5000;

            if (obj.status == 1)
            {
                $("#contentarea").html(obj.msg);
                setTimeout(function(){ update_dashboard(); }, timeout);
            }
            else {
                addAlert("FEHLER", "Keine Daten vom Server empfangen ...", 'danger');
                setTimeout(function(){ update_dashboard(); }, timeout);
            }

        })
        .fail(function( msg,errormsg,errmsgtext ) {
            addAlert("AJAX Fehler!","Request failed! Reason: "+ errmsgtext,'danger');
            setTimeout(function(){ update_dashboard(); }, timeout);
        });

}

function uhrzeit() {
    var jetzt = new Date(),
        h = jetzt.getHours(),
        m = jetzt.getMinutes(),
        s = jetzt.getSeconds();
    m = fuehrendeNull(m);
    s = fuehrendeNull(s);
    $('.uhr').html( h + ':' + m + ':' + s);
    setTimeout(uhrzeit, 500);
}
function fuehrendeNull(zahl) {
    zahl = (zahl < 10 ? '0' : '') + zahl;
    return zahl;
}