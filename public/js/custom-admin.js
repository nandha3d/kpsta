(function ($) {
    var SIDEBAR_KEY = 'kpstaSidebarCollapsed';
    var MOBILE_MAX = 768;

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
    var currentUrl = window.location.href.split('?')[0].replace(/\/$/, "");
    var $best = null;
    var bestLen = -1;

    $('.modern-sidebar-menu a').each(function () {
        var href = $(this).attr('href');
        if (!href || href === '#') {
            return;
        }
        var linkUrl = href.replace(/\/$/, "");

        // Exact match or sub-page match (like /edit/1)
        if (currentUrl === linkUrl || currentUrl.indexOf(linkUrl + '/') === 0) {
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