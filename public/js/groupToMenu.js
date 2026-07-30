
//Add and update news
$("body").on('submit', 'form', function (e) {
    var _label = $(this).find('button[type="submit"]');
    $(_label).find('i').remove().end().prepend('<i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize() ,
        context: this,
        success: function (result) {
            $(_label).find('i').remove().end().prepend('<i class="fa fa-save "></i>');
            $(this).find('.modal-body .alert').remove();
            if (result.code == 'success') {
//                $('#table-content').html(result.content);
//                $('#table-content').find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
            } else {
//                $(this).closest('form').replaceWith(result.form);
            }

        }
    });
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





function getMenus(groupId, href) {
    $.ajax({
        url: href,
        type: 'POST',
        dataType: 'json',
        context: this,
        data: {groupId: groupId},
        success: function (result) {
//            $(_label).removeSpinner();
            if (result.code === 'success') {
                $('#table-content').html(result.content);

            } else {
                $("body").find('#modal form').replaceWith(result.form);
            }
        }
    });

}


$(function () {
    $('select[name="group"]').select2().on("change", function (e) {
        if ($('select[name="group"]').select2("val").length > 0) {
            getMenus($('select[name="group"]').select2("val"), $('select[name="group"]').data('href'));
        } else {
            $("body .save-button").addClass('hide');
        }
    });
});

