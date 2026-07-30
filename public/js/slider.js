var imageWidthThumb = 155;
var imageHeightThumb = 50.14;



$(function () {
    $('.category-search').select2().on("change", function (e) {
        seach(0);
    });
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

var ajax_request;
function seach(_page) {
    var _page = _page;
    var search = $('input[name="search"]').val();
    var _category = $('.category-search').select2("val");
    _category = _category ? _category.join(',') : '';

    if (typeof ajax_request !== 'undefined') {
        ajax_request.abort();
    }
    ajax_request = $.ajax({
        url: $('input[name="search"]').data('href'),
        type: 'GET',
        dataType: 'JSON',
        data: {search: search, designation: _category, 'page': _page},
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



//upload  pdf file
$("body").on('change', '#modal input[name="image"]', function (e) {
    var formData = new FormData();
    formData.append('file', $('input[type=file]')[0].files[0]);
    if (formData) {
        $("body").find('#modal .file-caption-name').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
        $("body").find('#modal .file-caption-name').attr('title', this.value);
    }
});

$("body").on('change', '#modal input[name="image"]', function (e) {
    $("body").find('#modal .file-caption-name').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
    if (this.files && this.files[0]) {
        var $thumbnail = $('#modal').find('#thumbnail');
        var $thumbnailPreview = $('#modal').find('#thumbnail_preview');
        //Remove existing image Area seletion
        $thumbnail.imgAreaSelect({remove: true, hide: true});
        //Remove Existing Image from the Div
        $($thumbnail, $thumbnailPreview).removeAttr('src');
        //loader on Preview Div
        $thumbnailPreview.addSpinner();

        //create Preview of selected Image
        var reader = new FileReader();
        reader.onload = function (e) {
            $thumbnail.attr('src', e.target.result);
            $thumbnailPreview.attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);

        reader.onloadend = function (e) {

            var image = new Image();
            image.src = e.target.result;
            image.onload = function () {

                $thumbnailPreview.removeSpinner();


                //aspectRatio: '(thumbnailwidth/thumbnailHeight), i.e 173/214',
                $thumbnail.imgAreaSelect({
                    aspectRatio: imageWidthThumb + ':' + imageHeightThumb,
//                    x1: 0,
//                    y1: 0,
//                    x2: imageWidthThumb,
//                    y2: imageHeightThumb,
                    handles: true,
//                    maxHeight: imageHeightThumb + 'px',
//                    maxWidth: imageWidthThumb + 'px',
                    parent: $('.modal-content-form'),
                    show: true,
                    onInit: preview,
                    onSelectChange: preview
                });


//                var height = (this.width / imageWidthThumb) * imageHeightThumb;
//                if (height <= this.height) {
//                    var diff = (this.height - height) / 2;
//                    var coords = {x1: 0, y1: diff, x2: this.width, y2: height + diff};
//                }
//                else { // if new height out of bounds, scale width instead
//                    var width = (this.height / imageHeightThumb) * imageWidthThumb;
//                    var diff = (this.width - width) / 2;
//                    var coords = {x1: diff, y1: 0, x2: width + diff, y2: this.height};
//                }
//                $thumbnail.imgAreaSelect(coords);



            };


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
        var _btnFile = $(this).find('#save');
        $(_btnFile).addSpinner();
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {

                $(_btnFile).removeSpinner();

                if (result.code == 'success') {
                    $('#modal').find('#thumbnail').removeAttr('src');
                    $('#modal').find('#thumbnail_preview').removeAttr('src');
                    $('#modal').find('#thumbnail').imgAreaSelect({remove: true});

                    $('#table-content').html(result.content);
                    $('#modal').modal('hide');
                    $('#table-content').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);

                    //$("body").find('#modal .file-input').removeClass('file-input-new');
                    //$("body").find('#modal input[name="pdfName"]').val(result.data.file_name);
                } else {
                    $("body").find('#modal form').replaceWith(result.form);
                }
            },
            error: function(xhr, status, error) {
                $(_btnFile).removeSpinner();
                console.error('Slider save error:', xhr.responseText);
                alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
            }
        });
    }
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
                $("body").find(".modal-content-form").html(result.form);
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



$('#modal').on('show.bs.modal', function (e) {
    $clickedButton = $(e.relatedTarget).data('id');
    if ($clickedButton === 'new') {
        $('#modal').find('#thumbnail').imgAreaSelect({remove: true, hide: true});
        $('#thumbnail , #thumbnail_preview').removeAttr('src');
    }
});

$('#modal').on('hidden.bs.modal', function (e) {
    $('#modal').find('#thumbnail').imgAreaSelect({remove: true, hide: true});
    $('#thumbnail , #thumbnail_preview').removeAttr('src');
});



$('.confirmation-modal').on('show.bs.modal', function (e) {
    $(e.target).off('click', '.delete');
    $(e.target).find('.modal-title').text($(e.relatedTarget).data('message'));
    var _href = $(e.relatedTarget).data('href');
    $(e.target).on('click', '.delete', function () {
        var _label = $($(e.target).find('.delete'));
        $(_label).addSpinner();
        $.ajax({
            url: _href + '?page=' + getUrlParamByName('page'),
            type: 'GET',
            dataType: 'json',
            context: this,
            success: function (result) {
                $(_label).removeSpinner();
                if (result.code === 'success') {
                    $('#table-content').html(result.content);
                    $('.confirmation-modal').modal('hide');
                }
            }
        });
        return false;
    });
});
