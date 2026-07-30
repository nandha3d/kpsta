$(window).load(function () {
    $('#hero-carousel').carousel({
        interval: 3000,
        pause: false
    });

    $('#reaction-carousel').carousel({
        interval: 3000,
        pause: false
    });


    $("#flexiselDemo3").flexisel({
        visibleItems: 8,
        itemsToScroll: 1,
        autoPlay: {
            enable: true,
            interval: 5000,
            pauseOnHover: true
        }
    });


    if ($(window).width() <= 767) {
        $('.header-top').addClass("navbar-fixed-top");
        $('.header .header-top-two').addClass("header-top-two-margin");
    } else {
        $('.header-top').removeClass("navbar-fixed-top");
        $('.header .header-top-two').removeClass("header-top-two-margin");
    }


});


$(function () {
    $('body').tooltip({selector: '[data-toggle="tooltip"]'});
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="tooltip"]').tooltip({animation: false});

    $('.ui-newsticker').newsticker({
        row_height: 40,
        max_rows: 1,
        speed: 600,
        direction: 'down',
        duration: 4000,
        autostart: 1,
        pauseOnHover: 1
    });
});


$("#disclaimer").on("shown.bs.collapse", function () {
    $('html, body').animate({
        scrollTop: $("#disclaimer").offset().top
    }, 1000);
});


var scrolling_velocity = 89; // 1-99
var scrolling_from = 'right';

//startScrolling($('.news-box p#kpsta-news'), scrolling_velocity, scrolling_from);
startScrolling($('.news-box p#flash-news'), scrolling_velocity, scrolling_from);


$('ul.nav li.dropdown').hover(function () {
    $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
}, function () {
    $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
});


$(function () {
    $("#loader-wrapper").css({display: "none"});
    $('html, body').css({'overflow': 'visible', 'height': '100%'});




    $(window).scroll(function () {
        if ($(this).scrollTop() > 400) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });


});