jQuery(document).ready(function($) {
    $('.ccp3o1-carousel').each(function() {
        var $carousel = $(this);
        var visibleItems = parseInt($carousel.data('visible')) || 3;
        var speed = parseInt($carousel.data('speed')) || 300;
        var gap = parseInt($carousel.data('gap')) || 10;

        $carousel.css('--ccp3o1-gap', gap + 'px').slick({
            slidesToShow: visibleItems,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: speed,
            arrows: true,
            dots: true,
            prevArrow: '<button type="button" class="slick-prev"><span class="dashicons dashicons-arrow-left-alt2"></span></button>',
            nextArrow: '<button type="button" class="slick-next"><span class="dashicons dashicons-arrow-right-alt2"></span></button>'
        });
    });
});