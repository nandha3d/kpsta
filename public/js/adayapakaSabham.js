var imageWidthThumb = 173;
var imageHeightThumb = 214;
//upload  pdf file
$("body").on('change', '#modal input[name="file"]', function (e) {
    if ($(this)[0].files[0]) {
        $("body").find('#modal .file-caption-name-file').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
        $("body").find('#modal .file-caption-name-file').attr('title', this.value);
    }
});


$("body").on('change', '#modal input[name="image"]', function (e) {
    $("body").find('#modal .file-caption-name').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
    if (this.files && this.files[0]) {
        $("body").find('#modal .file-caption-name-image').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
        $("body").find('#modal .file-caption-name-image').attr('title', this.value);
        //Remove existing image Area seletion
        $('#modal').find('#thumbnail').imgAreaSelect({remove: true, hide: true});
        //Remove Existing Image from the Div
        $('#thumbnail', '#thumbnail_preview').removeAttr('src');

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

            //aspectRatio: '(thumbnailwidth/thumbnailHeight), i.e 173/214',
            $('#modal').find('#thumbnail').imgAreaSelect({
                aspectRatio: imageWidthThumb + ':' + imageHeightThumb,
                x1: 0,
                y1: 0,
                x2: imageWidthThumb,
                y2: imageHeightThumb,
                handles: true,
                maxHeight: imageHeightThumb + 'px',
                maxWidth: imageWidthThumb + 'px',
                parent: $('.modal-content-form'),
                onSelectChange: preview
            });
        };


    }
});

//create a preview of the selection
function preview(img, selection) {
    //get width and height of the uploaded image.
    var current_width = $('#modal').find('#thumbnail').width();
    var current_height = $('#modal').find('#thumbnail').height();

    var scaleX = imageWidthThumb / selection.width;
    var scaleY = imageHeightThumb / selection.height;

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
    var formData = new FormData();
    formData.append('image', $('input[name=image]')[0].files[0]);
    formData.append('file', $('input[name=file]')[0].files[0]);
    formData.append('x1', $('#x1').val());
    formData.append('y1', $('#y1').val());
    formData.append('x2', $('#x2').val());
    formData.append('y2', $('#y2').val());
    formData.append('w', $('#w').val());
    formData.append('h', $('#h').val());
    $($(this).serializeArray()).each(function (key, field) {
        formData.append(field.name, field.value);
    });

    if (formData) {

        $("body").find('#modal .file-caption-name').attr('title', this.value);
        var _btnFile = $(this).find('#save');
        $(_btnFile).addSpinner();
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            complete: function () {
                $(_btnFile).removeSpinner();
            },
            success: function (result) {
                if (result.code == 'success') {
                    $('#modal').find('#thumbnail, #thumbnail_preview').removeAttr('src');
                    $('#modal').find('#thumbnail').imgAreaSelect({remove: true});

                    $('#table-content').html(result.content);
                    $('#modal').modal('hide');

                    //$("body").find('#modal .file-input').removeClass('file-input-new');
                    //$("body").find('#modal input[name="pdfName"]').val(result.data.file_name);
                } else {
                    $("body").find('#modal form').replaceWith(result.form);
                }
            },
            error: function(xhr, status, error) {
                console.error('Adayapaka Sabham save error:', xhr.responseText);
                alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
            }
        });
    }
    return false;
});


//Before file upload, check whether the upload type is set to PDF
$("body").on('click', '#modal input[name="file"]', function (e) {
    var _label = $(this).closest('#modal').find('.upload-type-toggle');
    if ($(_label).find('label').hasClass('active')) {
        var _labelActiveClass = _label.find('label.active');
        var _type = $(_labelActiveClass).find('input').data('type');
        console.log(_type);
        if (_type == "file") {
            $(_label).closest('.form-group').removeClass('has-error');
            return true;
        }
    }
    $(_label).closest('.form-group').addClass('has-error');
    return false;
});

//enable and disable of URL BOX according to toggle - upload type
$("body").on('click', '#modal .upload-type-toggle .btn-default', function (e) {
    //$(this).find('.btn').toggleClass('active');
    var _type = $(this).find('input').val();
    if (_type === 'url') {
        $(this).closest('#modal').find('input[name="path"]').removeAttr('disabled');
    } else if (_type === 'file') {
        $(this).closest('#modal').find('input[name="path"]').attr('disabled', 'disabled');
    }
    $(this).closest('.form-group').removeClass('has-error');
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
                    var image = new Image();
                    image.src = $('#modal').find('#thumbnail').attr('src');
                    if (image.width !== 0) {
                        $('#modal').find('#thumbnail').imgAreaSelect({
                            aspectRatio: imageWidthThumb + ':' + imageHeightThumb,
                            x1: 0,
                            y1: 0,
                            x2: imageWidthThumb,
                            y2: imageHeightThumb,
                            handles: true,
                            maxHeight: imageHeightThumb + 'px',
                            maxWidth: imageWidthThumb + 'px',
                            parent: $('.modal-content-form'),
                            onSelectChange: preview
                        });
                    }
                });
            }
        }
    });
    return false;
});






var ajax_request;
function seach(_page) {
    var _page = _page;
    var search = $('input[name="search"]').val();

    if (typeof ajax_request !== 'undefined') {
        ajax_request.abort();
    }
    ajax_request = $.ajax({
        url: $('input[name="search"]').data('href'),
        type: 'GET',
        dataType: 'JSON',
        data: {search: search, 'page': _page},
        success: function (result) {
            if (result.code == 'success') {
                $('#table-content').html(result.content);
            }

            if (result.newUrl !== "undefined") {
                window.history.pushState("object or string", "KPSTA", result.newUrl);
            }
        }
    });
}

$('body').on('click', '.pagination a', function (e) {
    e.preventDefault();
    var page = getUrlParamByName('page', $(this).attr("href"));
    seach(page);
    return false;
});


//search
$("body").on('keyup', 'input[name="search"]', function (e) {
    var _val = $.trim($(this).val());
    if (_val) {
        seach(0);
    }
});

$("body").on('click', 'button[name="search"]', function (e) {
    seach(0);
});




$("body").on('change', 'table input[name="publish"]', function () {
    var publish = $(this).val();
    var _label = $(this).closest('label');
    $(_label).find('span').remove();
    $(_label).addSpinner();
    $.ajax({
        url: $(this).closest('.publish').data('href'),
        type: 'GET',
        dataType: 'json',
        data: {'publish': publish},
        context: this,
        success: function () {
            $(_label).removeSpinner();
            if (publish == 1) {
                $(_label).append('<span>Yes</span>');
            } else {
                $(_label).append('<span>No</span>');
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
        $(_label).find('i').remove();
        $(_label).prepend('<i class="fa fa-spinner fa-spin"></i> ');
        $.ajax({
            url: _href,
            type: 'GET',
            dataType: 'json',
            context: this,
            success: function (result) {
                $(_label).find('i').remove();
                if (result.code === 'success') {
                    $('#table-content').html(result.content);
                    $('.confirmation-modal').modal('hide');
                    //var _tr = $('#table-content').find("[data-tr='" + result.lastId + "']");
                    //$(_tr).effect("highlight", {color: '#ac2925'}, 1000).remove();
                } else {
                    $("body").find('#modal form').replaceWith(result.form);
                }
            }
        });
        return false;
    });
});