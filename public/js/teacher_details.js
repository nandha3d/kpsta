var $tableContent = $("#table-content");

function yearChange() {
    var $content = $(".content");
    var data = $tableContent.find('.data').data();
    var configYear = data["yearConfig"];
    var year = data["year"];

    if (data["view"] == 2) {
        var $viewBtnGroup = $('.view-btn-group');
        $viewBtnGroup.find('.btn').removeClass("active");
        $viewBtnGroup.find('.confirmed-btn').addClass('active');
        $viewBtnGroup.find('.confirmed-btn input').attr("checked", true);
    }

    if (year == configYear) {
        $content.find(".tab-content").show();
        $content.find(".modal-delete-btn").show();
        $(".entered-btn").show();
    } else {
        $content.find(".tab-content").hide();
        $content.find(".modal-delete-btn").hide();
        $(".entered-btn").hide();
    }
}


//ADD new record and Update record
$("body").on('submit', '#modal #save', function (e) {
    var _saveBtn = $('#modal button[type="submit"]');
    var queryString = {};
    var title = $(this).data("title");
    if (title != "Add") {
        queryString = {
            page: getUrlParamByName('page'),
            search: $('input[name="search"]').val(),
            group: $('#group-search').select2("val"),
            office: $('#office-search').select2("val"),
            view: $('input[name="view_type"]:checked').val(),
            year: $('#year-search').select2("val")
        };
    }

    $(_saveBtn).addSpinner();
    $.ajax({
        url: $(this).data('href') + '?' + $.param(queryString),
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        context: this,
        complete: function () {
            $(_saveBtn).removeSpinner();
        },
        success: function (result) {
            $(this).find('.modal-body .alert').remove();
            if (result.code === 'success') {
                $tableContent.html(result.content);
                $('#modal').modal('hide');
                $tableContent.find("[data-tr='" + result.lastId + "']").effect("highlight", {color: '#00a65a'}, 2000);
            } else {
                $("body").find('#modal form').replaceWith(result.form);
            }
        },
        error: function(xhr, status, error) {
            console.error('Teacher Details save error:', xhr.responseText);
            alert("Error saving: " + (xhr.status ? xhr.status + ' ' : '') + error);
        }
    });
    return false;
});





//search
//$("body").on('keyup', 'input[name="search"]', function (e) {
//    var _val = $.trim($(this).val());
//    if (_val) {
//        seach(0);
//    }
//});

$("body").on('change', 'input[name="view_type"]', function (e) {
    var aBtn = $(this).closest('a.btn');
    aBtn.addSpinner();
    var callback = function () {
        aBtn.removeSpinner()
    };
    seach(0, callback);
});

$("body").on('click', 'button[name="search"]', function (e) {
    seach(0);
});

$("body").on('change', '.limit', function (e) {
    seach(0);
});

var ajax_request;
function seach(_page, callback) {
    var _page = _page;
    var search = $('input[name="search"]').val();
    var _group = $('#group-search').select2("val");
    var _office = $('#office-search').select2("val");
    var _year = $('#year-search').select2("val");
    var _view = $('input[name="view_type"]:checked').val();
    var $tableOverlay = $('section.content').find('.overlay');

    if (typeof ajax_request !== 'undefined') {
        ajax_request.abort();
    }

    $tableOverlay.show();

    ajax_request = $.ajax({
        url: $('input[name="search"]').data('href'),
        type: 'GET',
        dataType: 'JSON',
        data: {search: search, group: _group, 'page': _page, office: _office, view: _view, year: _year,
            limit:  $('.limit').val()
        },
        complete: function () {

            if (typeof (callback) != "undefined") {
                callback();
            }
            $tableOverlay.hide();
        },
        success: function (result) {
            if (result.code == 'success') {
                $tableContent.html(result.content);
            }

            if (result.newUrl !== "undefined") {
                window.history.pushState("object or string", "KPSTA", result.newUrl);
            }
            yearChange();
        }
    });
}



$('body').on('click', '.pagination a', function (e) {
    e.preventDefault();
    if ($(this).closest('li').hasClass('active')) {
        return false;
    }
    var page = getUrlParamByName('page', $(this).attr("href"));
    seach(page);
    return false;
});

var $modal = $('#modal');

$modal.on("show.bs.modal", function () {
    $modal.find(".select2").select2({dropdownParent: $modal});

});


//To generate Edit Form
$("#table-content").on('click', '.edit', function (e) {
    $(this).closest("tr").effect("highlight", {color: '#ecf0f5'}, 1000);
    var _year = $('#year-search').select2("val");
    var $pageActive = $(".pagination").find('li.active');
    var _page = getUrlParamByName('page', $pageActive.attr("href"));
    $.ajax({
        url: $(this).data('href'),
        type: 'GET',
        dataType: 'json',
        data: {year: _year},
        context: this,
        success: function (result) {
            if (result.code === 'success') {
//                $(this).dropdown('toggle');
                $modal.find(".modal-content-form").html(result.form);
                $modal.modal({show: true});
            }
        }
    });
    return false;
});


//delete order
$('.confirmation-modal').off().on('show.bs.modal', function (e) {
    var $target = $(e.target);
    var $relatedTarget = $(e.relatedTarget);
    var ids = [];

    var message = "Delete these selected records ?";
    if ($relatedTarget.data("select") == "single") {
        message = "Delete this record ( " + $relatedTarget.data("name") + " ) ?";
    } else {
        var checked = $tableContent.find("tbody .checkbox-list:checked");
        if (checked.length == 0) {
            alertBox("error", "", "Please select the records to be deleted");
            return false;
        } else {
            $.each(checked, function (e, val) {
                ids.push($(val).val());
            });
        }
    }

    $target.find('.modal-title').text(message);

    $target.off('click', '.delete');
    $target.on('click', '.delete', function () {
        var _href = $relatedTarget.data('href');
        var _label = $($target.find('.delete'));
        var _year = $('#year-search').select2("val");

        var search = $('input[name="search"]').val();
        var _group = $('#group-search').select2("val");
        var _office = $('#office-search').select2("val");
        var _view = $('input[name="view_type"]:checked').val();

        var $pageActive = $(".pagination").find('li.active');
        var _page = getUrlParamByName('page', $pageActive.attr("href"));

        $(_label).addSpinner();
        $.ajax({
            url: _href + '?page=' + getUrlParamByName('page'),
            type: 'GET',
            dataType: 'json',
            data: {search: search, group: _group, office: _office, view: _view, year: _year, ids: ids.join(',')},
            context: this,
            complete: function () {
                $(_label).removeSpinner();
            },
            success: function (result) {
                if (result.code === 'success') {
                    $tableContent.html(result.content);
                    $target.modal('hide');
                    alertBox("success", "Deleted!!!", "Selected records are deleted");
//                    var _tr = $tableContent.find("[data-tr='" + result.lastId + "']");
//                    $(_tr).effect("highlight", {color: '#ac2925'}, 1000).remove();
                }else{
                    alertBox("error", "", "Something went wrong");
                }
            }
        });
        return false;
    });
});



$("body").on('submit', '#modalProcess #save', function (e) {
    var _saveBtn = $('#modalProcess').find('button[type="submit"]');
    $(_saveBtn).addSpinner();
    $.ajax({
        url: $(this).data('href'),
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        context: this,
        complete: function () {
            $(_saveBtn).removeSpinner();
        },
        success: function (result) {
            $(this).find('.modal-body .alert').remove();
            if (result.code === 'success') {
                $tableContent.html(result.content);
                $('#modalProcess').modal('hide');
            }

        }
    });
    return false;

});


$('#modalProcess').on('show.bs.modal', function (e) {
    $(e.target).find('.modal-title').text($(e.relatedTarget).data('model-title'));
    $(e.target).find('button[type="submit"]').text($(e.relatedTarget).data('button-label'));
});

$(".download").on("click", function () {
    var aauthGroup = $(this).data('aauthgroup');
    if (parseInt(aauthGroup) < 4) {
        $("#download-modal").modal("show");
        return false;
    }
    var href = $(this).data("href");
    var _groupView = $(this).data('group_view');

    downloadPdf(false, false, href, _groupView);
});

$('#download-modal').off().on('show.bs.modal', function (e) {
    var officeId;
    var $officeProcess = $(e.target).find('#select2-office-process');

    $(e.target).find('.confirm').text('Generate');


    $(e.target).find('.form-office').removeClass('hide');
    var _href = $("#group-search").data('href');
    $.ajax({
        url: _href, type: 'POST',
        dataType: 'json', data: {groupId: 4},
        context: this,
        complete: function () {
        },
        success: function (result) {
            var officeLabel = result.officeLabel;
            $(e.target).find('.control-label').text(officeLabel);

            var option = '';
            $.each(result.officeSelect, function (k, val) {
                option += '<option value="' + k + '">' + val + '</option> ';
            });
            $officeProcess.find('option').remove().end().append(option);
            $officeProcess.select2({dropdownParent: $("#download-modal")});

        }
    });

    $(e.target).off('click', '.confirm');
    $(e.target).on('click', '.confirm', function () {
        var href = $(this).data("href");
        var _groupView = $(this).data('group_view');
        officeId = $officeProcess.val();
        downloadPdf(4, officeId, href, _groupView);
        return false;
    });
});


function downloadPdf(_group, _office, href, _groupView) {
    var search = $('input[name="search"]').val();
    if (!_group) {
        var _group = $('#group-search').select2("val");
    }
    if (!_office) {
        var _office = $('#office-search').select2("val");
    }
    var _view = $('input[name="view_type"]:checked').val();
//    var _groupView = $(".download").data('group_view');
    var _year = $('#year-search').select2("val");

    window.open(href + '?office=' + _office + '&group=' + _group + '&search=' + search + '&view=' + _view + '&group_view=' + _groupView + '&year=' + _year, '_blank');
}


$tableContent.on('click', '#consoliated-count', function (e) {
    var _this = $(this);
    $(_this).addSpinner();
    var search = $('input[name="search"]').val();
    var _group = $('#group-search').select2("val");
    var _office = $('#office-search').select2("val");
    var _year = $('#year-search').select2("val");
    var _view = $('input[name="view_type"]:checked').val();
    var _href = $(this).data('href');
    $.ajax({
        url: _href + '?office=' + _office + '&group=' + _group + '&search=' + search + '&view=' + _view + '&year=' + _year,
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        context: this,
        complete: function () {
            $(_this).removeSpinner();
            $(_this).html(' <i class="fa fa-arrow-up"></i> View less');
            $(_this).attr('id', "consoliated-count-hide");
        },
        success: function (result) {
            $(this).find('.modal-body .alert').remove();
            if (result.code === 'success') {
                $tableContent.find('.consolidated-count-content').html(result.content).fadeIn();
            }

        }
    });
    return false;
});

$tableContent.on('click', '#consoliated-count-hide', function (e) {
    $(this).html(' <i class="fa fa-arrow-down"></i> View more');
    $(this).attr('id', "consoliated-count");
    $tableContent.find('.consolidated-count-content').fadeOut();
});



$tableContent.on('change', '.checkbox-all', function (e) {
    if ($(this).is(':checked')) {
        $tableContent.find("tbody .checkbox-list").prop("checked", true);
    } else {
        $tableContent.find("tbody .checkbox-list").prop("checked", false);
    }
});



//delete order
$('.process-modal').off().on('show.bs.modal', function (e) {
    var checked, processGroup, ids = [], officeId;
    var $officeProcess = $(e.target).find('#select2-office-process');
    $(e.target).find('.modal-title').text($(e.relatedTarget).data('message'));
    $(e.target).find('.confirm').text($(e.relatedTarget).data('confirm-text'));

    processGroup = $(e.relatedTarget).data('process-group');
    checked = $tableContent.find("tbody .checkbox-list:checked");

    if (typeof (processGroup) === "undefined") {
        $(e.target).find('.form-office').addClass('hide');

        if (checked.length === 0) {
            alertBox("error", "", "Please select the record to process");
            return false;
        }

        $.each(checked, function (e, val) {
            ids.push($(val).val());
        });

    } else {
        ids = [];
        $(e.target).find('.form-office').removeClass('hide');
        var _href = $("#group-search").data('href');
        $.ajax({
            url: _href,
            type: 'POST',
            dataType: 'json',
            data: {groupId: processGroup},
            context: this,
            complete: function () {
            },
            success: function (result) {
                if (result.code === 'success') {
                    var officeLabel = result.officeLabel;
                    $(e.target).find('.control-label').text(officeLabel);
                    if (officeLabel == false || processGroup != result.groupId) {
                        return false;
                    } else {
                        var option = '';
                        $.each(result.officeSelect, function (k, val) {
                            option += '<option value="' + k + '">' + val + '</option> ';
                        });
                        $officeProcess.find('option').remove().end().append(option);
                        $officeProcess.select2({dropdownParent: $("#process-modal")});
                    }
                }

            }
        });

    }


    $(e.target).off('click', '.confirm');
    $(e.target).on('click', '.confirm', function () {
        officeId = $officeProcess.val();
        var groupId = $(e.relatedTarget).data('process-group');
        var _href = $(e.relatedTarget).data('href');
        var _label = $(e.target).find('.confirm');
        var _process = $(e.relatedTarget).data('process');
        var _action = $(e.relatedTarget).data('action');
        var _year = $('#year-search').select2("val");
        var search = $('input[name="search"]').val();
        
        if(ids.length == 0 && officeId == 0){
            alertBox("error", "", "Please select record/office to process");
            return false;
        }

        $(_label).addSpinner();
        $.ajax({
            url: _href + '?view=' + getUrlParamByName('view'),
            dataType: 'json',
            type: 'GET',
            context: this,
            data: {
                process: _process,
                action: _action,
                ids: ids.join(','),
                officeProcess: officeId,
                groupProcess: groupId,
                year: _year,
                search: search,
                groupSel: $('#group-search').select2("val"),
                officeSel: $('#office-search').select2("val"),
                limit:  $('.limit').val()
                        
            },
            complete: function(){
              $(_label).removeSpinner();  
            },
            success: function (result) {
                if (result.code === 'success') {
                    $tableContent.html(result.content);
                    $('.process-modal').modal('hide');
                    alertBox('success', '', 'Record\'s are successfully submitted');
                }else{
                    alertBox('error', '', 'Something went wrong. Please contact website admin');
                }
            }
        });
        return false;
    });
});

