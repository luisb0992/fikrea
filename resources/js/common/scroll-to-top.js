$(window).scroll(function () {
    if ($(this).scrollTop() > 410) {
        $('#scroll-to-top').fadeIn();
    } else {
        $('#scroll-to-top').fadeOut();
    }
});
//Click event to scroll to top
$('#scroll-to-top').click(function () {
    $('html, body').animate({scrollTop: 0});
    return false;
});
