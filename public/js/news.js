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






//Add and update news
$("body").on('submit', '#modal #save', function (e) {
    e.preventDefault();
    // Sync Summernote content back to the textarea before serializing
    $(this).find('.content-textarea').each(function() {
        if ($(this).hasClass('note-editor') === false && $(this).next('.note-editor').length > 0) {
            $(this).val($(this).summernote('code'));
        }
    });

    var _label = $(this).find('button[type="submit"]');
    $(_label).find('i').remove().end().prepend('<i class="fa fa-spinner fa-spin"></i>');
    
    var formData = new FormData(this);

    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        context: this,
        complete: function () {
            $(_label).find('i.fa-spinner').remove();
            $(_label).prepend('<i class="fa fa-save "></i>');
        },
        success: function (result) {
            $(this).find('.modal-body .alert').remove();
            if (result.code == 'success') {
                $('#table-content').html(result.content);
                $('#modal').modal('hide');
                $('#table-content').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
            } else {
                $(this).closest('form').replaceWith(result.form);
                // Re-initialize summernote on the new form
                $('.content-textarea').summernote({
                    height: 150,
                    tabsize: 2,
                });
            }

        },
        error: function(xhr, status, error) {
            console.error('News save error:', xhr.responseText);
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
//    $(e.target).off('click', '.delete');
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


