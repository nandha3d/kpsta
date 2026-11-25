var imageWidthThumb = 173;
var imageHeightThumb = 214;

function updateRoleBadges(roleCounts) {
    if (!roleCounts) return;
    $.each(roleCounts, function (slug, cnt) {
        $('[data-role-count="' + slug + '"]').text(cnt);
    });
}

function getSectionSearchUrl() {
    var $input = $('#leaderSectionRoot input[name="search"]');
    return $input.data('href') || $('#leaderSectionRoot').data('base-url');
}

var sectionAjaxReq;
function sectionSearch(_page) {
    var page = _page || 1;
    var search = $('#leaderSectionRoot input[name="search"]').val() || '';
    var district = $('#leaderSectionRoot select[name="district-search"]').val() || '';
    var is_publish = $('#leaderSectionRoot select[name="publish-search"]').val() || '';
    var sort = $('#leaderSectionRoot select[name="sort-search"]').val() || 'position-asc';

    if (sectionAjaxReq) {
        sectionAjaxReq.abort();
    }
    sectionAjaxReq = $.ajax({
        url: getSectionSearchUrl(),
        type: 'GET',
        dataType: 'JSON',
        data: {
            search: search,
            district: district,
            is_publish: is_publish,
            sort: sort,
            page: page
        },
        success: function (result) {
            if (result && result.code === 'success') {
                $('#table-content').html(result.content);
                if (result.roleCounts) {
                    updateRoleBadges(result.roleCounts);
                }
                if (result.newUrl && result.newUrl !== 'undefined') {
                    window.history.pushState('object or string', 'KPSTA', result.newUrl);
                }
            }
        }
    });
}

$(function () {
    $('body').on('change', '#leaderSectionRoot .filter-control', function () {
        sectionSearch(1);
    });

    $('body').on('keyup', '#leaderSectionRoot input[name="search"]', function () {
        sectionSearch(1);
    });

    $('body').on('click', '#leaderSectionRoot button[name="btn-search"]', function () {
        sectionSearch(1);
    });

    $('body').on('click', '#btn-reset-section-filters', function () {
        $('#leaderSectionRoot input[name="search"]').val('');
        $('#leaderSectionRoot select[name="district-search"]').val('');
        $('#leaderSectionRoot select[name="publish-search"]').val('');
        $('#leaderSectionRoot select[name="sort-search"]').val('position-asc');
        sectionSearch(1);
    });

    $('body').on('click', '#leaderSectionRoot .pagination a', function (e) {
        e.preventDefault();
        var page = getUrlParamByName('page', $(this).attr('href')) || 1;
        sectionSearch(page);
        return false;
    });
});

// Publish toggle
$('body').on('change', '#leaderSectionRoot table input[name="publish"]', function () {
    var publish = $(this).val();
    var _label = $(this).closest('label');
    $(_label).find('span').remove();
    $(_label).addSpinner();
    $.ajax({
        url: $(this).closest('.publish').data('href'),
        type: 'GET',
        dataType: 'json',
        data: { publish: publish },
        success: function () {
            $(_label).removeSpinner();
            $(_label).append(publish == 1 ? '<span>Active</span>' : '<span>Inactive</span>');
        },
        error: function () {
            $(_label).removeSpinner();
            $(_label).append(publish == 1 ? '<span>Active</span>' : '<span>Inactive</span>');
        }
    });
    return false;
});

// Image preview & cropper
$('body').on('change', '#modal input[name="image"]', function () {
    if (this.files && this.files[0]) {
        $('#modal').find('#thumbnail').imgAreaSelect({ remove: true, hide: true });
        $('#thumbnail, #thumbnail_preview').removeAttr('src');
        $('#modal .thumbnail_preview_loader').prepend('<i class="fa fa-spinner fa-spin"></i>');

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#thumbnail').attr('src', e.target.result);
            $('#thumbnail_preview').attr('src', e.target.result);
        };
        reader.onloadend = function () {
            $('#modal .thumbnail_preview_loader').find('i').remove();
            setTimeout(initCropperOnThumbnail, 100);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

function initCropperOnThumbnail() {
    var $thumb = $('#modal').find('#thumbnail');
    if (!$thumb.length || !$thumb.attr('src')) return;
    $thumb.imgAreaSelect({ remove: true, hide: true });

    var curW = $thumb.width() || 250;
    var curH = $thumb.height() || 300;
    var initW = Math.round(curW * 0.75);
    var initH = Math.round(initW * (imageHeightThumb / imageWidthThumb));
    if (initH > curH) {
        initH = Math.round(curH * 0.85);
        initW = Math.round(initH * (imageWidthThumb / imageHeightThumb));
    }
    var initX1 = Math.max(0, Math.round((curW - initW) / 2));
    var initY1 = Math.max(0, Math.round((curH - initH) / 2));

    $thumb.imgAreaSelect({
        aspectRatio: imageWidthThumb + ':' + imageHeightThumb,
        x1: initX1,
        y1: initY1,
        x2: initX1 + initW,
        y2: initY1 + initH,
        handles: true,
        minWidth: 40,
        minHeight: 50,
        parent: $('.modal-content-form').length ? $('.modal-content-form') : $('#modal'),
        onSelectChange: preview,
        onInit: function (img, sel) { preview(img, sel); }
    });
}

function preview(img, selection) {
    if (!selection || !selection.width || !selection.height) return;
    var $thumb = $('#modal').find('#thumbnail');
    var current_width = $thumb.width();
    var current_height = $thumb.height();
    if (!current_width || !current_height) return;

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
    $('#w').val(selection.width);
    $('#h').val(selection.height);
}

// Form submit (Add / Edit)
$('body').on('submit', '#modal #save', function () {
    var $form = $(this);
    var formData = new FormData(this);
    formData.append('x1', $('#x1').val() || '');
    formData.append('y1', $('#y1').val() || '');
    formData.append('x2', $('#x2').val() || '');
    formData.append('y2', $('#y2').val() || '');
    formData.append('w', $('#w').val() || '');
    formData.append('h', $('#h').val() || '');

    var submitUrl = $form.data('href');
    var districtFilter = $('#leaderSectionRoot select[name="district-search"]').val() || '';
    if (districtFilter && submitUrl.indexOf('district=') === -1) {
        submitUrl += (submitUrl.indexOf('?') === -1 ? '?' : '&') + 'district=' + encodeURIComponent(districtFilter);
    }

    var _btn = $form.find('button[type="submit"]');
    $(_btn).addSpinner();
    $.ajax({
        url: submitUrl,
        type: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            $(_btn).removeSpinner();
            if (result.code === 'success') {
                $('#modal').find('#thumbnail, #thumbnail_preview').removeAttr('src');
                $('#modal').find('#thumbnail').imgAreaSelect({ remove: true, hide: true });
                $('#table-content').html(result.content);
                if (result.roleCounts) {
                    updateRoleBadges(result.roleCounts);
                }
                $('#modal').modal('hide');
                $('#table-content').find("[data-tr='" + result.lastId + "']").effect('highlight', { color: '#00a65a' }, 2000);
            } else if (result.form) {
                $('#modal .modal-content-form').html(result.form);
            } else {
                alert(result.data || result.message || 'Save failed.');
            }
        },
        error: function (xhr) {
            $(_btn).removeSpinner();
            alert('An error occurred while saving.');
            console.error(xhr.responseText);
        }
    });
    return false;
});

// Edit click
$('body').on('click', '#leaderSectionRoot .edit', function (e) {
    e.preventDefault();
    var $btn = $(this);
    if ($btn.hasClass('disabled')) return false;
    $btn.addClass('disabled').addSpinner();
    $.ajax({
        url: $btn.data('href'),
        type: 'POST',
        dataType: 'json',
        success: function (result) {
            $btn.removeClass('disabled').removeSpinner();
            if (result.code === 'success') {
                $('#modal .modal-content-form').html(result.form);
                $('#modal').modal({ show: true });
                $('#modal').one('shown.bs.modal', function () {
                    var src = $('#modal').find('#thumbnail').attr('src');
                    if (src) {
                        var img = new Image();
                        img.onload = initCropperOnThumbnail;
                        img.src = src;
                        if (img.complete) initCropperOnThumbnail();
                    }
                });
            }
        },
        error: function () {
            $btn.removeClass('disabled').removeSpinner();
        }
    });
    return false;
});

// Restore default locked values when opening "+ Add" modal after custom-admin.js runs
$('#modal').on('shown.bs.modal', function (e) {
    if ($(e.relatedTarget).data('id') === 'new') {
        var $form = $('#modal #save');
        var defDesig = $form.data('default-designation');
        var defDist = $form.data('default-district') || $('#leaderSectionRoot select[name="district-search"]').val() || '';
        var defPos = $form.data('default-position') || 25;
        $('#modal input[name="active_role"]').val($('#leaderSectionRoot').data('active-role') || '');
        $('#modal input[name="active_district"]').val($('#leaderSectionRoot select[name="district-search"]').val() || $('#leaderSectionRoot').data('active-district') || '');
        if (defDesig !== undefined && defDesig !== '') {
            $('#section_designation').val(String(defDesig));
        }
        if (defDist !== '') {
            $('#section_district').val(String(defDist));
        }
        $('#section_position').val(String(defPos));
        // Default Published = Yes
        var $yesRadio = $('#modal input[name="is_publish"][value="1"]');
        $yesRadio.prop('checked', true).closest('label').addClass('active');
        $('#modal input[name="is_publish"][value="0"]').prop('checked', false).closest('label').removeClass('active');
    }
});

$('#modal').on('hidden.bs.modal', function () {
    $('#modal').find('#thumbnail').imgAreaSelect({ remove: true, hide: true });
    $('#thumbnail, #thumbnail_preview').removeAttr('src');
});

// Single-row Delete callback
window.executeSectionAction = function (url) {
    if (!url) return;
    var districtFilter = $('#leaderSectionRoot select[name="district-search"]').val() || '';
    if (districtFilter && url.indexOf('district=') === -1) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + 'district=' + encodeURIComponent(districtFilter);
    }
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response && response.code === 'success') {
                $('#table-content').html(response.content);
                if (response.roleCounts) {
                    updateRoleBadges(response.roleCounts);
                }
            } else {
                alert((response && response.message) || 'Error deleting record.');
            }
            $('#delete').modal('hide');
        },
        error: function () {
            alert('An error occurred. Please try again.');
            $('#delete').modal('hide');
        }
    });
};

// Batch delete callback
window.executeSectionBatchAction = function (url) {
    var ids = batchSelectedIds();
    if (!url || ids.length === 0) return;
    var districtFilter = $('#leaderSectionRoot select[name="district-search"]').val() || '';
    if (districtFilter && url.indexOf('district=') === -1) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + 'district=' + encodeURIComponent(districtFilter);
    }
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: { ids: ids },
        success: function (response) {
            if (response && response.content) {
                $('#table-content').html(response.content);
            }
            if (response && response.roleCounts) {
                updateRoleBadges(response.roleCounts);
            }
            if (!response || response.code !== 'success') {
                alert((response && response.message) || 'Nothing was deleted.');
            }
            $('#delete').modal('hide');
        },
        error: function () {
            alert('An error occurred. Please try again.');
            $('#delete').modal('hide');
        }
    });
};
