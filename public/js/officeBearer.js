var imageWidthThumb = 173;
var imageHeightThumb = 214;

$(function () {
    $('.category-search').select2().on("change", function (e) {
        seach(0);
    });

    $('body').on('change', '.filter-control', function () {
        seach(0);
    });

    $('body').on('click', '#btn-reset-filters', function () {
        $('input[name="search"]').val('');
        $('.category-search').val(null).trigger('change');
        $('select[name="level-search"]').val('');
        $('select[name="district-search"]').val('');
        $('select[name="former-search"]').val('');
        $('select[name="publish-search"]').val('');
        $('select[name="sort-search"]').val('position-asc');
        seach(0);
    });

    // Initialize designation select with tags enabled so users can create new ones
    if ($('.select2-category').length) {
        $('.select2-category').select2({
            tags: true,
            dropdownParent: $('#modal')
        });
    }
});

//search
$("body").on('keyup', 'input[name="search"]', function (e) {
    seach(0);
});

$("body").on('click', 'button[name="search"], button[name="btn-search"]', function (e) {
    seach(0);
});

var ajax_request;
function seach(_page) {
    var _page = _page || 0;
    var search = $('input[name="search"]').val();
    var _category = $('.category-search').select2("val");
    _category = Array.isArray(_category) ? _category.join(',') : (_category || '');

    var level = $('select[name="level-search"]').val() || '';
    var district = $('select[name="district-search"]').val() || '';
    var is_former = $('select[name="former-search"]').val() || '';
    var is_publish = $('select[name="publish-search"]').val() || '';
    var sort = $('select[name="sort-search"]').val() || 'position-asc';

    if (typeof ajax_request !== 'undefined') {
        ajax_request.abort();
    }
    ajax_request = $.ajax({
        url: $('input[name="search"]').data('href'),
        type: 'GET',
        dataType: 'JSON',
        data: {
            search: search,
            designation: _category,
            level: level,
            district: district,
            is_former: is_former,
            is_publish: is_publish,
            sort: sort,
            page: _page
        },
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
    var formData = new FormData(this);
    formData.append('x1', $('#x1').val());
    formData.append('y1', $('#y1').val());
    formData.append('x2', $('#x2').val());
    formData.append('y2', $('#y2').val());
    formData.append('w', $('#w').val());
    formData.append('h', $('#h').val());
    
    if (formData) {
        var _btnFile = $(this).find('button[type="submit"]');
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
                alert("An error occurred: " + xhr.responseText);
                console.error(xhr.responseText);
            }
        });
    }
    return false;
});



$("body").on('click', '.edit', function (e) {
    var $btn = $(this);
    if ($btn.hasClass('disabled')) return false;
    $btn.addClass('disabled').addSpinner();
    
    $btn.closest("tr").effect("highlight", {color: '#ecf0f5'}, 1000);
    $.ajax({
        url: $btn.data('href'),
        type: 'POST',
        dataType: 'json',
        success: function (result) {
            $btn.removeClass('disabled').removeSpinner();
            if (result.code == 'success') {
                $("body .modal-content-form").html(result.form);
                if ($('.select2-category').length) {
                    $('.select2-category').select2({
                        tags: true,
                        dropdownParent: $('#modal')
                    });
                }
                $('#modal').modal({show: true});

                $('#modal').one('shown.bs.modal', function (e) {
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
        },
        error: function() {
            $btn.removeClass('disabled').removeSpinner();
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
            success: function (result) {
                $(_label).removeSpinner();
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