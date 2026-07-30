//upload  pdf file
$("body").on('change', '#modal input[name="image"]', function (e) {
    var formData = new FormData();
    formData.append('file', $('input[name="image"]')[0].files[0]);
    if (formData) {
        $("body").find('#modal .file-caption-name').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
        $("body").find('#modal .file-caption-name').attr('title', this.value);
    }
});

$("body").on('change', '#modal input[name="image"]', function (e) {
    $("body").find('#modal .file-caption-name').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
    if (this.files && this.files[0]) {
        //Remove existing image Area seletion
        $('#modal').find('#thumbnail').imgAreaSelect({remove: true, hide: true});
        //Remove Existing Image from the Div
        $('#thumbnail').removeAttr('src');
        $('#thumbnail_preview').removeAttr('src');

        //loader on Preview Div
        $("#modal .thumbnail_preview_loader").prepend('<i class="fa fa-spinner fa-spin"></i>');

        //create Preview of selected Image
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#thumbnail').attr('src', e.target.result);

            $('#thumbnail_preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);

        reader.onloadend = function () {
            $("#modal .thumbnail_preview_loader").find('i').remove();
//            var current_width = $('#thumbnail').width();
//            var current_height =$('#thumbnail').height();
//            if (current_width > current_height) {
//                var _w = current_height;
//            } else {
//                var _w = current_width;
//            }
//            var _x1 = 0;
//            var _y1 = 0;
//            var _x2 =  _w;
//            var _y2 =  _w;


            //aspectRatio: '1:(thumbnailwidth/thumbnailHeight)',
            $('#modal').find('#thumbnail').imgAreaSelect({
                aspectRatio: '1:1',
                onSelectChange: preview,
                x1: 10,
                y1: 10,
                x2: 100,
                y2: 100,
                handles: true,
                parent: $('.modal-content-form')
            });
        };


    }
});

//create a preview of the selection
function preview(img, selection) {
    //get width and height of the uploaded image.
    var current_width = $('#modal').find('#thumbnail').width();
    var current_height = $('#modal').find('#thumbnail').height();

    var scaleX = 150 / selection.width;
    var scaleY = 150 / selection.height;

    $('#modal').find('#thumbnail_preview').css({
        width: Math.round(scaleX * current_width) + 'px',
        height: Math.round(scaleY * current_height) + 'px',
        marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
        marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
    });
    $('#x1').val(selection.x1);
    $('#y1').val(selection.y1);
    $('#x2').val(current_width);
    $('#y2').val(current_height);
    // $('#x2').val(selection.x2);
    //    $('#y2').val(selection.y2);    
    $('#w').val(selection.width);
    $('#h').val(selection.height);
}





$("body").on('submit', '#modal #save', function (e) {
    var formData = new FormData(this);
    formData.append('x1', $('#x1').val());
    formData.append('y1', $('#y1').val());
    formData.append('x2', $('#x2').val());
    formData.append('y2', $('#y2').val());
    formData.append('w', $('#w').val());
    formData.append('h', $('#h').val());
    if (formData) {

        $("body").find('#modal .file-caption-name').attr('title', this.value);
        var _btnFile = $(this).find('button[type="submit"]');
        $(_btnFile).addSpinner();
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            complete: function() {
                $(_btnFile).removeSpinner();
            },
            success: function (result) {
                if (result.code == 'success') {
                    $('#modal').find('#thumbnail').removeAttr('src');
                    $('#modal').find('#thumbnail_preview').removeAttr('src');
                    $('#modal').find('#thumbnail').imgAreaSelect({remove: true});

                    $('#table-content').html(result.content);
                    $('#modal').modal('hide');
                } else {
                    alert("Error: " + (result.data || "Unknown error"));
                }
            },
            error: function (xhr, status, error) {
                console.error("Upload error:", xhr.responseText);
                alert("Server Error: " + (xhr.status ? xhr.status + " " : "") + error);
            }
        });
    }
    return false;
});



$("body").on('click', '.make-cover', function (e) {
    var _btnFile = $(this).find('span');
    $(_btnFile).addSpinner();
    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        success: function (result) {
            $(_btnFile).removeSpinner();
        }
    });
    return false;
});


$("body").on('click', '.edit', function (e) {
    $(this).closest("tr").effect("highlight", {color: '#ecf0f5'}, 1000);
    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        success: function (result) {
            if (result.code == 'success') {
                $("body .modal-content-form").html(result.form);
                $('#modal').modal({show: true});
                
                $('#modal').on('shown.bs.modal', function (e) {
                    $('#modal').find('#thumbnail').imgAreaSelect({
                        aspectRatio: '1:1',
                        onSelectChange: preview,
                        x1: 10,
                        y1: 10,
                        x2: 100,
                        y2: 100,
                        handles: true,
                        parent: $('.modal-content-form')
                    });
                });
            }
        }
    });
    return false;
});



//delete 
$('.confirmation-modal').on('show.bs.modal', function (e) {
    $(e.target).off('click', '.delete');
    $(e.target).find('.modal-title').text($(e.relatedTarget).data('message'));
    var _href = $(e.relatedTarget).data('href');
    $(e.target).on('click', '.delete', function () {
        var _label = $($(e.target).find('.delete'));
        $(_label).addSpinner();
        $.ajax({
            url: _href,
            type: 'GET',
            dataType: 'json',
            context: this,
            complete: function() {
                $(_label).removeSpinner();
            },
            success: function (result) {
                if (result.code === 'success') {
                    $('#table-content').html(result.content);
                    $('.confirmation-modal').modal('hide');
                } else {
                    $("body").find('#modal form').replaceWith(result.form);
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete error:', xhr.responseText);
                alert("Error deleting: " + (xhr.status ? xhr.status + ' ' : '') + error);
            }
        });
        $(e.target).off('click', '.delete');
        return false;
    });
});
