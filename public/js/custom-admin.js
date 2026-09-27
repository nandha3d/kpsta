(function ($) {
    var SIDEBAR_KEY = 'kpstaSidebarCollapsed';
    var MOBILE_MAX = 991;

    var $wrapper = $('#modernWrapper');
    var $backdrop = $('#sidebarBackdrop');

    function isMobile() {
        return window.innerWidth <= MOBILE_MAX;
    }

    // Desktop: collapse to an icon rail. Mobile: slide the sidebar off canvas.
    // Both are driven by classes only so the responsive rules keep working —
    // inline styles here would survive a resize and strand the layout.
    function applyStoredState() {
        $('html').removeClass('pre-collapsed');
        if (isMobile()) {
            $wrapper.removeClass('sidebar-collapsed');
            return;
        }
        var collapsed = false;
        try {
            collapsed = localStorage.getItem(SIDEBAR_KEY) === '1';
        } catch (e) {}
        $wrapper.toggleClass('sidebar-collapsed', collapsed);
    }

    function closeMobileSidebar() {
        $wrapper.removeClass('sidebar-open');
    }

    $('#sidebar-toggle').on('click', function (e) {
        e.preventDefault();
        if (isMobile()) {
            $wrapper.toggleClass('sidebar-open');
            return;
        }
        var collapsed = !$wrapper.hasClass('sidebar-collapsed');
        $wrapper.toggleClass('sidebar-collapsed', collapsed);
        try {
            localStorage.setItem(SIDEBAR_KEY, collapsed ? '1' : '0');
        } catch (e) {}
    });

    $backdrop.on('click', closeMobileSidebar);

    $(document).on('keyup', function (e) {
        if (e.which === 27) {
            closeMobileSidebar();
        }
    });

    var resizeTimer = null;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            closeMobileSidebar();
            applyStoredState();
        }, 150);
    });

    applyStoredState();

    // Treeview expand / collapse
    $('.modern-sidebar-menu').on('click', '.treeview > a', function (e) {
        e.preventDefault();
        var $parent = $(this).parent();
        var $menu = $parent.children('.treeview-menu');

        if ($parent.hasClass('menu-open')) {
            $parent.removeClass('menu-open');
            $menu.slideUp(180);
        } else {
            $parent.addClass('menu-open');
            $menu.slideDown(180);
        }
    });

    // 1. Highlight the section from the admin URI segment (admin/<segment>/...)
    // Segments are slugs; strip anything else rather than build a bad selector.
    var url = ($("#url_segment").val() || '').replace(/[^A-Za-z0-9_-]/g, '');
    if (url) {
        var $menuItem = $("." + url + "-menu");
        $menuItem.addClass('active');
        if ($menuItem.hasClass('treeview')) {
            $menuItem.addClass('menu-open');
            $menuItem.children('.treeview-menu').show();
        }
    }

    // 2. Highlight the current link. Only the longest match wins, so on
    // .../office_bearer/designation the parent .../office_bearer stays quiet.
    var fullUrl = window.location.href.replace(/\/$/, "");
    var cleanUrl = window.location.href.split('?')[0].replace(/\/$/, "");
    var search = window.location.search;
    var $best = null;
    var bestLen = -1;

    $('.modern-sidebar-menu a').each(function () {
        var href = $(this).attr('href');
        if (!href || href === '#' || href === 'javascript:void(0)') {
            return;
        }
        var linkUrl = href.replace(/\/$/, "");

        // Exact match with query params (e.g. ?is_former=1)
        if (fullUrl === linkUrl) {
            $best = $(this);
            bestLen = 999999;
            return false;
        }

        // Sub-page match with query params
        if (search && linkUrl.indexOf(search) !== -1) {
            var linkBase = linkUrl.split('?')[0];
            if (cleanUrl === linkBase || cleanUrl.indexOf(linkBase + '/') === 0) {
                if (linkUrl.length > bestLen) {
                    bestLen = linkUrl.length;
                    $best = $(this);
                }
                return;
            }
        }

        // Exact match without query params or sub-page match
        if (cleanUrl === linkUrl || cleanUrl.indexOf(linkUrl + '/') === 0) {
            if (linkUrl.length > bestLen) {
                bestLen = linkUrl.length;
                $best = $(this);
            }
        }
    });

    if ($best) {
        $best.parent('li').addClass('active');

        // Expand parent treeview if it's inside one
        var $treeview = $best.closest('.treeview');
        if ($treeview.length) {
            $treeview.addClass('active menu-open');
            $treeview.children('.treeview-menu').show();
        }
    }

    $.fn.addSpinner = function () {
        this.prop('disabled', true);
        this.find('i').addClass('hide');
        return this.prepend('<i class="fa fa-spinner fa-spin"></i> ');
    };

    $.fn.removeSpinner = function () {
        this.prop("disabled", false);
        this.find('i.fa-spinner').remove();
        this.find('i.hide').removeClass('hide');
    };

    if (typeof $('.content-wrapper').offset() !== "undefined") {
        var eTop = $('.content-wrapper').offset().top;
        $(window).scroll(function () {
            var dist = eTop - $(window).scrollTop();
            if (dist < 0) {
//            $('.main-sidebar').css('margin-top', dist + 'px');
            }
        });
    }

}(jQuery));

$(document).ajaxStart(function () {
    Pace.restart();
    $('#modal .modal-loading-bar').addClass('active');
});
$(document).ajaxStop(function () {
    Pace.stop();
    $('#modal .modal-loading-bar').removeClass('active');
});

$(document).ajaxComplete(function (event, xhr, settings) {
});

function alertBox(status, title, message) {
    var className = '';
    if (status == "success") {
        className = "alert-success";
    } else if (status == "error") {
        className = "alert-danger";
    }

    var id = "alert-box-"+Math.floor((Math.random()*10) + 1);

    var html = '<div class="alert-box alert '+className+'" id="'+id+'">';
    html += '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
    html += '<strong>' + title + '</strong> ' + message;
    html += '</div>';

    $('body').append(html);
    $("#"+id).fadeIn();
    setTimeout(function(){
      $("#"+id).fadeOut(800, function(){$(this).remove();}); 
    }, 2000);
};

$('#modal').on('show.bs.modal', function (e) {
    $clickedButton = $(e.relatedTarget).data('id');
    if ($clickedButton === 'new') {
        $(e.target).find('.modal-title').text($(e.relatedTarget).data('title-new'));
        var addUrl = $(e.target).find('form').data('href-add');
        $(e.target).find('form').data('href', addUrl);
        $(e.target).find('.hide-on-new').hide();
        $(e.target).find('.show-on-new').show();
        $(e.target).find('.enable-on-new').attr('disabled', false);
        $(e.target).find('input, select, textarea').not('input[type="radio"], input[type="checkbox"], .has-default-select').val('');
        if ($(e.target).find('select').has('[class*=select2]')) {
            $.each($(e.target).find('select[class*=select2]'), function () {
                $(this).trigger('change');
            });
        }
        $(e.target).find('.modal-body .alert').remove();
        $(e.target).find('.modal-body .file-caption-name').html('');
        $(e.target).find('.form-group').removeClass('has-error');
        $(e.target).find('.btn').removeClass('active');
    } else if ($clickedButton === 'delete') {
        $(e.target).find('.modal-title').html($(e.relatedTarget).data('message'));
        $(e.relatedTarget).dropdown('toggle');
    }
});

$('#delete').on('show.bs.modal', function (e) {
    // opened by executeBatchAction, which has already filled the modal in
    if (!e.relatedTarget) {
        return;
    }
    var $relatedTarget = $(e.relatedTarget);
    var message = $relatedTarget.data('message');
    var deleteUrl = $relatedTarget.data('href');
    
    $(this).find('.modal-title').html(message);
    $(this).find('.delete').attr('onclick', 'executeAction("' + deleteUrl + '");');
    
    // hide parent dropdown if opened from one
    if ($relatedTarget.closest('.dropdown-menu').length > 0) {
        $relatedTarget.closest('.dropdown').removeClass('open');
    }
});

window.executeAction = function(url) {
    if (url) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.code === 'success') {
                    if (response.content) {
                        $('#table-content').html(response.content);
                    } else {
                        window.location.reload();
                    }
                } else {
                    var errorMsg = 'Error performing action.';
                    if (response.db_error && response.db_error.message) {
                        errorMsg += '\\n' + response.db_error.message;
                    }
                    alert(errorMsg);
                }
                $('#delete').modal('hide');
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $('#delete').modal('hide');
            }
        });
    }
};



$('#modal').on('hidden.bs.modal', function (e) {
    $(e.target).find('input').not('input[type="radio"]').removeAttr('value');
    $(e.target).find('textarea').val('');
//    $(e.target).find('form').attr('id', 'new');
    $(e.target).find('.modal-body .alert').remove();
    $(e.target).find('.form-group').removeClass('has-error');
    $(e.target).find('.btn').removeClass('active');
});



$("body").on('focus', 'input, textarea', function () {
    $(this).closest('.form-group').removeClass('has-error');
});

$("body").on('click', 'input[type="radio"]', function () {
    $(this).closest('.form-group').removeClass('has-error');
});



$(function () {
    // Remove Search if user Resets Form or hits Escape!
    $('body, .navbar-collapse form[role="search"] button[type="reset"]').on('click keyup', function (event) {
        if (event.which == 27 && $('.navbar-collapse form[role="search"]').hasClass('active') ||
                $(event.currentTarget).attr('type') == 'reset') {
            closeSearch();
        }
    });

    function closeSearch() {
        var $form = $('.navbar-collapse form[role="search"].active')
        $form.find('input').val('');
        $form.removeClass('active');
    }

    // Show Search if form is not active // event.preventDefault() is important, this prevents the form from submitting
    $(document).on('click', '.navbar-collapse form[role="search"]:not(.active) button[type="submit"]', function (event) {
        event.preventDefault();
        var $form = $(this).closest('form'),
                $input = $form.find('input');
        $form.addClass('active');
        $input.focus();

    });
    // ONLY FOR DEMO // Please use $('form').submit(function(event)) to track from submission
    // if your form is ajax remember to call `closeSearch()` to close the search container
    $(document).on('click', '.navbar-collapse form[role="search"].active button[type="submit"]', function (event) {
        event.preventDefault();
        var $form = $(this).closest('form'),
                $input = $form.find('input');
        $('#showSearchTerm').text($input.val());
        closeSearch()
    });


    $('.row .content-collapse').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        var $collapse = $this.closest('.collapse-group').find('.collapse');
        if ($collapse.hasClass('in')) {
            $this.html("View more &raquo;");
        } else {
            $this.html("Hide");
        }
        $collapse.collapse('toggle');
    });
});





function getUrlParamByName(name, url) {
    if (!url) {
        url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
    if (!results)
        return null;
    if (!results[2])
        return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


/* ------------------------------------------------------------------ *
 * Multi-row ("Delete Selected") toolbar actions
 *
 * Rows carry <input class="list-checkbox" name="ids[]" value="<id>">, the
 * header carries .list-checkbox-all, and the toolbar button carries
 * data-href plus data-precheck / data-confirm-callback names that are
 * resolved off window at click time.
 * ------------------------------------------------------------------ */

function batchSelectedIds() {
    var ids = [];
    $('#table-content input.list-checkbox:checked').each(function () {
        var val = $(this).val();
        if (val) {
            ids.push(val);
        }
    });
    return ids;
}

// header checkbox drives every row checkbox in the same table
$('body').on('change', '.list-checkbox-all', function () {
    $(this).closest('table').find('input.list-checkbox').prop('checked', $(this).prop('checked'));
});

// ...and clears itself as soon as one row is unticked
$('body').on('change', 'input.list-checkbox', function () {
    var $table = $(this).closest('table');
    var total = $table.find('input.list-checkbox').length;
    var checked = $table.find('input.list-checkbox:checked').length;
    $table.find('.list-checkbox-all').prop('checked', total > 0 && total === checked);
});

window.batchActionPrecheck = function () {
    if (batchSelectedIds().length === 0) {
        alert('Select at least one row first.');
        return false;
    }
    return true;
};

window.executeBatchAction = function (url) {
    var ids = batchSelectedIds();
    if (!url || ids.length === 0) {
        return;
    }
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {ids: ids},
        success: function (response) {
            if (response && response.content) {
                $('#table-content').html(response.content);
            }
            if (!response || response.code !== 'success') {
                alert((response && response.message) || 'Nothing was deleted.');
            } else if (response.failed) {
                alert(response.message);
            }
            if (response && !response.content) {
                window.location.reload();
            }
            $('#delete').modal('hide');
        },
        error: function () {
            alert('An error occurred. Please try again.');
            $('#delete').modal('hide');
        }
    });
};

// the toolbar button is not a bootstrap modal trigger, so drive the shared
// confirmation modal by hand after the precheck passes
$('body').on('click', '[data-toggle="confirmation"]', function (e) {
    e.preventDefault();

    var $btn = $(this);
    var precheck = $btn.data('precheck');
    if (precheck && typeof window[precheck] === 'function' && window[precheck]($btn) === false) {
        return;
    }

    var url = $btn.data('href');
    if (!url) {
        return;
    }

    var callback = $btn.data('confirm-callback') || 'executeAction';
    var count = batchSelectedIds().length;
    var message = $btn.data('message') || 'Are you sure?';
    if (count) {
        message += ' (' + count + ' selected)';
    }

    var $modal = $('#delete');
    $modal.find('.modal-title').html(message);
    $modal.find('.delete').attr('onclick', callback + '("' + url + '");');
    $modal.modal('show');
});


/* ------------------------------------------------------------------ *
 * File-upload popups: the Remove (X) button appears only when a file
 * is actually present, uniformly across every Add/Edit modal.
 *
 * bootstrap-fileinput marks an empty picker with `.file-input-new`,
 * which CSS uses to hide Remove. Forms ship that class hardcoded, so on
 * open we clear it when an existing file is shown (Edit), and manage it
 * on pick/clear. Runs alongside any per-form handlers (idempotent).
 * ------------------------------------------------------------------ */
function syncFileInput($fileInput) {
    var $caption = $fileInput.find('.file-caption-name');
    var hasFile = $.trim($caption.text()).length > 0 || $.trim($caption.attr('title') || '').length > 0;
    $fileInput.toggleClass('file-input-new', !hasFile);
}

$(document).on('shown.bs.modal', '.modal', function () {
    $(this).find('.file-input').each(function () {
        syncFileInput($(this));
    });
});

// picking a file reveals Remove; an emptied picker hides it again
$('body').on('change', '.file-input input[type="file"]', function () {
    var $fileInput = $(this).closest('.file-input');
    var name = this.value ? this.value.split(/[\/]/).pop() : '';
    if (name) {
        $fileInput.removeClass('file-input-new');
        var $cap = $fileInput.find('.file-caption-name');
        if (!$.trim($cap.text()).length) {
            $cap.html('<i class="glyphicon glyphicon-file"></i> ' + name).attr('title', name);
        }
    } else {
        syncFileInput($fileInput);
    }
});

// the X clears the picker and hides itself again
$('body').on('click', '.fileinput-remove-button', function () {
    var $fileInput = $(this).closest('.file-input');
    $fileInput.find('input[type="file"]').val('');
    $fileInput.find('.file-caption-name').html('').attr('title', '');
    $fileInput.addClass('file-input-new');
});

/* ------------------------------------------------------------------ *
 * Mobile Quick Action: Floating Action Button (FAB)
 * Whenever a page provides a [data-id="new"] action, inject a FAB
 * so mobile users can tap "+" anytime with ease.
 * ------------------------------------------------------------------ */
$(function () {
    var $newBtn = $('[data-id="new"]').first();
    if ($newBtn.length && !$('#mobileFabAdd').length) {
        var newTitle = $newBtn.data('title-new') || $newBtn.attr('title') || 'Add New';
        var $fab = $('<button type="button" id="mobileFabAdd" class="mobile-fab-add" title="' + newTitle + '" aria-label="' + newTitle + '"><i class="fa fa-plus"></i></button>');
        $('body').append($fab);
        $fab.on('click', function (e) {
            e.preventDefault();
            $newBtn.trigger('click');
        });
    }
});

/* ------------------------------------------------------------------ *
 * Mobile Table Card Enhancer (Single Reusable Model)
 * Dynamically tags table cells with semantic roles for responsive card layout
 * ------------------------------------------------------------------ */
function enhanceMobileTables() {
    $('table.table').each(function() {
        var $table = $(this);
        var headers = [];
        $table.find('thead th').each(function() {
            headers.push($.trim($(this).text()).toLowerCase());
        });
        $table.find('tbody tr').each(function() {
            var $tr = $(this);
            $tr.children('td').each(function(idx) {
                var $td = $(this);
                var h = headers[idx] || '';
                if ($td.find('input[type="checkbox"]').length) {
                    $td.addClass('td-select');
                } else if ($td.find('.btn-group.publish, .publish, [data-href*="publish"]').length || h === 'publish') {
                    $td.addClass('td-publish');
                } else if ($td.find('.modern-actions, .btn-edit, .btn-delete, .btn-view').length || h === 'action') {
                    $td.addClass('td-actions');
                } else if (h.indexOf('date') !== -1) {
                    $td.addClass('td-date');
                } else if ($td.hasClass('text-ellipsis') || h.indexOf('description') !== -1 || h.indexOf('heading') !== -1 || h.indexOf('title') !== -1) {
                    $td.addClass('td-title');
                } else if ($td.find('.btn-xs, .label').length || h.indexOf('type') !== -1 || h.indexOf('category') !== -1) {
                    $td.addClass('td-badge');
                } else if (h.indexOf('url') !== -1 || h.indexOf('path') !== -1) {
                    $td.addClass('td-meta');
                }
            });
        });
    });
}
$(document).ready(enhanceMobileTables);
$(document).ajaxComplete(enhanceMobileTables);

