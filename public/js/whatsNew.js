$("body").on('change', 'table input[name="publish"]', function () {
    var publish = $(this).val();
    var _label = $(this).closest('label');
    $(_label).addClass('disabled');
    $(_label).find('span').remove();
    $(_label).append('<i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        url: 'news/publish',
        type: 'POST',
        dataType: 'json',
        data: {'_id': $(this).data('id'), 'publish': publish},
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


$("body").on('change', '#modal input[name="file"]', function (e) {
    if (this.files && this.files[0]) {
        console.log($('input[name=file]')[0].files[0]);
        $("body").find('#modal .file-caption-name').html('<i class="glyphicon glyphicon-file"></i> ' + this.files[0].name);
    }
});



//Add and update news
$("body").on('submit', '#modal #save', function (e) {
    var formData = new FormData();

    if (typeof $('input[name=file]')[0] != "undefined") {
        formData.append('file', $('input[name=file]')[0].files[0]);
    }

    $($(this).serializeArray()).each(function (key, field) {
        formData.append(field.name, field.value);
    });

    var _label = $(this).find('button[type="submit"]');
    $(_label).addSpinner();
    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        data: formData,
        context: this,
        processData: false,
        contentType: false,
        complete: function () {
            $(_label).removeSpinner();
        },
        success: function (result) {
            $(this).find('.modal-body .alert').remove();
            if (result.code == 'success') {
                $('#table-content').html(result.content);
                $('#modal').modal('hide');
                $('#table-content').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
            } else {
                $(this).closest('form').replaceWith(result.form);
            }
        },
        error: function(xhr, status, error) {
            $(_label).removeSpinner();
            console.error('WhatsNew save error:', xhr.responseText);
            alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
        }
    });
    return false;
});

$("body").on('click', 'table .edit', function (e) {
    $(this).closest("tr").effect("highlight", {color: '#ecf0f5'}, 1000);
    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        success: function (result) {
            if (result.code == 'success') {
                $("body .modal-content-form").html(result.form);
                $('#modal').modal({show: true});
            }
        }
    });
    return false;
});



$('body').on('click', '.pagination a', function (e) {
    e.preventDefault();
    var link = $(this).attr("href").split(/\//g).pop();
    if (!$.isNumeric(link)) {
        link = 0;
        console.log(link);
    }
    seach(link);
    return false;
});

$("body").on('keyup', 'input[name="search"]', function (e) {
    seach(0);
});

$("body").on('click', 'button[name="search"]', function (e) {
    seach(0);
});

var ajax_request;
function seach(_page) {
    var _page = _page;
    var search = $('input[name="search"]').val();
    if (typeof ajax_request !== 'undefined') {
        ajax_request.abort();
    }
    ajax_request = $.ajax({
        url: $('input[name="search"]').data('href') + '/' + _page,
        type: 'GET',
        dataType: 'JSON',
        data: {search: search, 'catgory': $('.category-search').val()},
        success: function (result) {
            if (result.code == 'success') {
                $('#table-content').html(result.content);
            }
        }
    });
}




//delete 
$('.confirmation-modal').on('show.bs.modal', function (e) {
    $(e.target).off('click', '.delete');
    $(e.target).find('.modal-title').text($(e.relatedTarget).data('message'));
    var _href = $(e.relatedTarget).data('href');
    $(e.target).off('click');
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
                }
            }
        });
        $(e.target).off('click', '.delete');
        return false;
    });
});


//remove uploaded file
$("body").on('click', 'table .file-remove', function (e) {
    var _btnFile = $(this);

    $(_btnFile).addSpinner();
    $.ajax({
        url: $(this).data("href"),
        type: 'POST',
        dataType: 'json',
        success: function (result) {
            $(_btnFile).removeSpinner();
            if (result.code === 'success') {
                $('#table-content').html(result.content);
            }
        }
    });
});


