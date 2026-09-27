

//ADD new record and Update record
$("body").on('submit', '#modal #save', function (e) {
    var _saveBtn = $('#modal button[type="submit"]');
    $(_saveBtn).addSpinner();
    $.ajax({
        url: $(this).data('href') + '?page=' + getUrlParamByName('page'),
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        context: this,
        success: function (result) {
            $(_saveBtn).removeSpinner();
            $(this).find('.modal-body .alert').remove();
            if (result.code === 'success') {
                $('#table-content').html(result.content);
                $('#modal').modal('hide');
                if (typeof $.fn.effect === 'function') {
                    $('#table-content').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
                }
            } else {
                $("body").find('#modal form').replaceWith(result.form);
            }
        },
        error: function(xhr, status, error) {
            $(_saveBtn).removeSpinner();
            alert("Error: " + xhr.status + " " + error + "\n" + xhr.responseText);
            console.error(xhr.responseText);
        }
    });
    return false;
});


//enable and disable of URL BOX according to toggle - upload type
$("body").on('click', '#modal .upload-type-toggle .btn-default', function (e) {
    var _type = $(this).find('input').val();
    var $modal = $(this).closest('#modal');
    if (_type === 'url') {
        $modal.find('input[name="path"]').removeAttr('disabled');
    } else if (_type === 'file') {
        $modal.find('input[name="path"]').attr('disabled', 'disabled');
    }
    $(this).closest('.form-group').removeClass('has-error');
});

//When user clicks browse file button, ensure upload type automatically switches to PDF
$("body").on('click', '#modal .btn-file, #modal input[name="file"]', function (e) {
    var $modal = $(this).closest('#modal');
    var $pdfRadio = $modal.find('input[name="upload_type"][value="file"]');
    var $urlRadio = $modal.find('input[name="upload_type"][value="url"]');
    
    $pdfRadio.prop('checked', true).closest('label').addClass('active');
    $urlRadio.prop('checked', false).closest('label').removeClass('active');
    $modal.find('input[name="path"]').attr('disabled', 'disabled');
    $modal.find('.upload-type-toggle').closest('.form-group').removeClass('has-error');
});

//upload  pdf file
$("body").on('change', '#modal input[name="file"]', function (e) {
    var formData = new FormData();
    formData.append('file', $('input[type=file]')[0].files[0]);
    formData.append('pdfName', $('input[name=pdfName]').val());
    if (formData) {
        var $fileCaptionName = $("body").find('#modal .file-caption-name');
        $fileCaptionName.html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
        $fileCaptionName.attr('title', this.value);
        var _btnFile = $(this).closest('.btn-file');
        $(_btnFile).addSpinner();
        var $submitButton = $(this).closest('#modal').find('button[type="submit"]');
        $submitButton.addSpinner();
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                $(_btnFile).removeSpinner();
                $submitButton.removeSpinner();
                if (result.code == 'success') {
                    $("body").find('#modal .file-input').removeClass('file-input-new');
                    $("body").find('#modal input[name="pdfName"]').val(result.data.file_name);
                }
            }
        });
    }
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

var ajax_request;
function seach(_page) {
    var _page = _page;
    var search = $('input[name="search"]').val();
    var _category = '';
    if ($('.category-search').length) {
        try {
            var catVal = $('.category-search').val();
            _category = Array.isArray(catVal) ? catVal.join(',') : (catVal || '');
        } catch (e) {
            _category = '';
        }
    }

    if (typeof ajax_request !== 'undefined') {
        ajax_request.abort();
    }
    ajax_request = $.ajax({
        url: $('input[name="search"]').data('href'),
        type: 'GET',
        dataType: 'JSON',
        data: {search: search, category: _category, 'page': _page},
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
//    var link = $(this).attr("href").split(/\//g).pop();
//    if (!$.isNumeric(link)) {
//        link = 0;
//    }
    var page = getUrlParamByName('page', $(this).attr("href"));
    seach(page);
    return false;
});

//To generate Edit Form
$("body").on('click', '.edit', function (e) {
    $(this).closest("tr").effect("highlight", {color: '#ecf0f5'}, 1000);
    $.ajax({
        url: $(this).data('href'),
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            if (result.code == 'success') {
                $(this).dropdown('toggle');
                $("body .modal-content-form").html(result.form);
                if ($('.select2-category').length) {
                    $('.select2-category').select2({
                        tags: true,
                        dropdownParent: $('#modal')
                    });
                }
                $('#modal').modal({show: true});
            }
        }
    });
    return false;
});

$('#modal').on('shown.bs.modal', function () {
    if ($('.select2-category').length) {
        $('.select2-category').select2({
            tags: true,
            dropdownParent: $('#modal')
        });
    }
});



$("body").on('change', 'table input[name="publish"]', function () {
    var publish = $(this).val();
    var _label = $(this).closest('label');
    $(_label).addClass('disabled');
    $(_label).find('span').remove();
    $(_label).append('<i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        url: $(this).closest('.publish').data('href'),
        type: 'GET',
        dataType: 'json',
        data: {'publish': publish},
        context: this,
        success: function () {
            $(this).closest('label').removeClass('disabled');
            $(this).closest('label').find('i').remove();
            if (publish == 1) {
                $(_label).append('<span>Yes</span>');
            } else {
                $(_label).append('<span>No</span>');
            }
        }
    });
    return false;
});

//delete order
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
//                    var _tr = $('#table-content').find("[data-tr='" + result.lastId + "']");
//                    $(_tr).effect("highlight", {color: '#ac2925'}, 1000).remove();
                } else {
//                    $("body").find('#modal form').replaceWith(result.form);
                }
            }
        });
        return false;
    });
});

//remove uploaded file
$("body").on('click', '#modal .fileinput-remove', function (e) {
    var _fileName = $(this).closest('#modal').find('input[name=pdfName]').val();
    var _btnFile = $(this);
    if (_fileName) {
        $(_btnFile).find('i').remove();
        $(_btnFile).prepend('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: $(this).data("href"),
            type: 'POST',
            dataType: 'json',
            data: {'pdfName': _fileName},
            success: function (result) {
                $(_btnFile).find('i').remove();
                $(_btnFile).prepend('<i class="glyphicon glyphicon-trash"></i>');
                if (result.code == 'success') {
                    $("body").find('#modal .file-caption-name').attr('title', '');
                    $("body").find('#modal .file-caption-name').html('');
                    $("body").find('#modal .file-input').addClass('file-input-new');
                    $("body").find('#modal input[name="pdfName"]').val('');
                }
            }
        });
    }
});


