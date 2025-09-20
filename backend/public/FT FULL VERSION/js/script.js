(function($) { 
"use strict";

$('[data-toggle="offcanvas"]').on('click', function () {
    $('.navbar-collapse').toggleClass('show');
    });


 /* ================ Revolution Slider. ================ */
      if($('.tp-banner').length > 0){
            $('.tp-banner').show().revolution({
                  delay:6000,
              startheight: 750,
              startwidth: 1170,
              hideThumbs: 1000,
              navigationType: 'none',
              touchenabled: 'on',
              onHoverStop: 'on',
              navOffsetHorizontal: 0,
              navOffsetVertical: 0,
              dottedOverlay: 'none',
              fullWidth: 'on'
            });
      }
      if($('.tp-banner-full').length > 0){
            $('.tp-banner-full').show().revolution({
                  delay:6000,
              hideThumbs: 1000,
              navigationType: 'none',
              touchenabled: 'on',
              onHoverStop: 'on',
              navOffsetHorizontal: 0,
              navOffsetVertical: 0,
              dottedOverlay: 'none',
              fullScreen: 'on'
            });
      }     

/* ================ property_scroll ================ */
    $(document).ready(function() {
        $(".jobs_list").owlCarousel({
            loop: true,
            rewind: true, 
            nav: false,
            margin:20,
            dots:false,
    autoplay:true,
    autoplayTimeout:3000,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                700: {
                    items: 2,
                    nav: false
                },
                900: {
                    items: 3,
                    nav: false
                },
                1170: {
                    items: 6,
                    nav: true
                }
            }
        });
    });


/* ================ property_scroll ================ */
    $(document).ready(function() {
        $(".property_scroll").owlCarousel({
            loop: true,
            rewind: true, 
            nav: false,
            dots:false,
    autoplay:true,
    autoplayTimeout:3000,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true
                },
                700: {
                    items: 4,
                    nav: false
                },
                900: {
                    items: 5,
                    nav: false
                },
                1170: {
                    items: 4,
                    nav: true
                }
            }
        });
    });


    /* ================ Slider ================ */
    $(document).ready(function() {
        $(".owl-carousel").owlCarousel({
            loop: true,
            rewind: true,
            margin: 20,
            nav: false,
            dots:false,
    autoplay:true,
    autoplayTimeout:3000,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true
                },
                700: {
                    items: 4,
                    nav: false
                },
                900: {
                    items: 5,
                    nav: false
                },
                1170: {
                    items: 6,
                    nav: true
                }
            }
        });
    });
 

 /* ================ Close Sidebar ================ */
    $('.fa-caret-down').on("click", function(e) {
        e.preventDefault();
        $(this).next().slideToggle('');
    });


// FAQ Accordion JS
    $('.accordion').find('.accordion-title').on('click', function(){
        $(this).toggleClass('active');
        $(this).next().slideToggle('fast');
        $('.accordion-content').not($(this).next()).slideUp('fast');
        $('.accordion-title').not($(this)).removeClass('active');       
    });
     

/*MAGNIFIC POPUP JS*/

  
  /*MAGNIFIC POPUP JS*/

    $('.video-play').magnificPopup({
        type: 'iframe'
    });
    var magnifPopup = function() {
        $('.work-popup').magnificPopup({
            type: 'image',
            removalDelay: 300,
            mainClass: 'mfp-with-zoom',
            gallery: {
                enabled: true
            },
            zoom: {
                enabled: true, // By default it's false, so don't forget to enable it

                duration: 300, // duration of the effect, in milliseconds
                easing: 'ease-in-out', // CSS transition easing function

                // The "opener" function should return the element from which popup will be zoomed in
                // and to which popup will be scaled down
                // By defailt it looks for an image tag:
                opener: function(openerElement) {
                    // openerElement is the element on which popup was initialized, in this case its <a> tag
                    // you don't need to add "opener" option if this code matches your needs, it's defailt one.
                    return openerElement.is('img') ? openerElement : openerElement.find('img');
                }
            }
        });
    };



})(jQuery);