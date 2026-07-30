function loadContent(e) {
    var $groupSearch = $("#group-search");
    var group = $groupSearch.select2("val");
    var office = $("#office-search").select2("val");

    if (group > 0 || $groupSearch.data('action') == "teacher") {
        var _view = $('input[name="view_type"]:checked').val();
        $('.box .overlay').show();
        $.ajax({
            url: $('input[name="search"]').data('href'),
            type: 'GET',
            dataType: 'json',
            data: {group: group, office: office, view: _view},
            complete: function () {
                $('.box .overlay').hide();
            },
            success: function (result) {
                if (result.code === 'success') {
                    $('#table-content').html(result.content);
                    if (result.newUrl !== "undefined") {
                        window.history.pushState("object or string", "KPSTA", result.newUrl);
                    }
                }
            }
        });
        return false;
    }
}

function loadOffice() {
    var $groupSearch = $("#group-search");
    var $officeSearch = $("#office-search");
    var val = $groupSearch.select2("val");
    var _href = $groupSearch.data('href');
    if ($groupSearch.data('action') == "main") {
        val = val - 1;
    }
    if (val > 0) {
        $('#modal .modal-loading-bar').addClass('active');
        $('#modal .overlay').show();
        $.ajax({
            url: _href,
            type: 'POST',
            dataType: 'json',
            data: {groupId: val},
            context: this,
            complete: function () {
                $('#modal .modal-loading-bar').removeClass('active');
                $('#modal .overlay').hide();
            },
            success: function (result) {
                if (result.code === 'success') {
                    var officeLabel = result.officeLabel;
                    if (officeLabel == false || val != result.groupId) {
                        $officeSearch.closest('.form-group').hide();
                    } else {
                        $officeSearch.closest('.form-group').show();
                        var option = '';
                        $.each(result.officeSelect, function (k, val) {
                            option += '<option value="' + k + '">' + val + '</option> ';
                        });
                        $officeSearch.find('option').remove().end().append(option);
                        $officeSearch.select2();
                    }
                }

            }
        });
        return false;
    } else {
        $officeSearch.closest('.form-group').hide();
    }
}

$(function () {
    var $groupSearch = $("#group-search");
    var $officeSearch = $("#office-search");

    $groupSearch.select2().on("change", function (e) {
        $officeSearch.select2().val(0);
        var group = $groupSearch.select2("val");
        if (($groupSearch.data('action') != "teacher" && group > 0) || group == 0) {
            loadContent();
        }
        loadOffice();
    });

    $officeSearch.select2().on("change", function (e) {
        loadContent();
    });
});