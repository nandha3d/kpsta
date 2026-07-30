

//ADD new record and Update record
$("body").on('submit', '#modal #save', function (e) {
    var _saveBtn = $('#modal button[type="submit"]');
    $(_saveBtn).find('i').remove();
    $(_saveBtn).prepend('<i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        context: this,
        complete: function () {
            $(_saveBtn).find('i.fa-spinner').remove();
            $(_saveBtn).prepend('<i class="fa fa-save "></i>');
        },
        success: function (result) {
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
            console.error('Form save error:', xhr.responseText);
            alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
        }
    });
    return false;
});


//enable and disable of URL BOX according to toggle - upload type
$("body").on('click', '#modal .upload-type-toggle .btn-default', function (e) {
    var _type = $(this).find('input').val();
    if (_type === 'url') {
        $(this).closest('#modal').find('input[name="path"]').removeAttr('disabled');
    } else if (_type === 'file') {
        $(this).closest('#modal').find('input[name="path"]').attr('disabled', 'disabled');
    }
    $(this).closest('.form-group').removeClass('has-error');
});

//Before file upload, check whether the upload type is set to PDF
$("body").on('click', '#modal input[name="file"]', function (e) {
    var _label = $(this).closest('#modal').find('.upload-type-toggle');
    if ($(_label).find('label').hasClass('active')) {
        var _labelActiveClass = _label.find('label.active');
        var _type = $(_labelActiveClass).find('input').data('type');
        if (_type == "file") {
            $(_label).closest('.form-group').removeClass('has-error');
            return true;
        }
    }
    $(_label).closest('.form-group').addClass('has-error');
    return false;
});

//upload  pdf file
$("body").on('change', '#modal input[name="file"]', function (e) {
    var type = $("#url_type").val();
    var formData = new FormData();
    formData.append('file', $('input[type=file]')[0].files[0]);
    formData.append('pdfName', $('input[name=pdfName]').val());
    if (formData) {

        $("body").find('#modal .file-caption-name').html('<i class="glyphicon glyphicon-file"></i> ' + this.value);
        $("body").find('#modal .file-caption-name').attr('title', this.value);
        var _btnFile = $(this).closest('.btn-file');
        $(_btnFile).find('i').remove();
        $(_btnFile).prepend('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: type + '/fileupload',
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.code == 'success') {
                    $(_btnFile).find('i').remove();
                    $(_btnFile).prepend('<i class="glyphicon glyphicon-folder-open"></i>');
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
    var _category = $('.category-search').select2("val");
    _category = _category ? _category.join(',') : '';

    if (typeof ajax_request !== 'undefined') {
        ajax_request.abort();
    }
    ajax_request = $.ajax({
        url: $('input[name="search"]').data('href'),
        type: 'GET',
        dataType: 'JSON',
        data: {search: search, category: _category, page: _page},
        success: function (result) {
            if (result.code === 'success') {
                $('#table-content').html(result.content);

                if (result.newUrl !== "undefined") {
                    window.history.pushState("object or string", "KPSTA", result.newUrl);
                }
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
                $('#modal').modal({show: true});
            }
        }
    });
    return false;
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
                } else {
                    $("body").find('#modal form').replaceWith(result.form);
                }
            }
        });
        return false;
    });
});

//remove uploaded file
$("body").on('click', '#modal .fileinput-remove', function (e) {
    var type = $("#url_type").val();
    var _fileName = $(this).closest('#modal').find('input[name=pdfName]').val();
    var _btnFile = $(this);
    if (_fileName) {
        $(_btnFile).find('i').remove();
        $(_btnFile).prepend('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: type + '/fileremove',
            type: 'POST',
            dataType: 'json',
            data: {'pdfName': _fileName},
            success: function (result) {
                if (result.code == 'success') {
                    $("body").find('#modal .file-input').addClass('file-input-new');
                    $("body").find('#modal input[name="pdfName"]').val('');
                }
            }
        });
    }
});


