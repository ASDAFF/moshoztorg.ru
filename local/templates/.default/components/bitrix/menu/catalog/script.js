function defer_menu() {

    if (window.jQuery) {

        $(document).ready(function () {
            $('.catalog-btn').click(function (event) {
                event.stopPropagation();

                $(this).toggleClass('active');

                if($(this).hasClass('active')){
                    $('#overlay_menu').addClass('show_layer');
                    $(this).siblings('.tabs').addClass('gtxactive');
                    $('ul.gtx_second_level').scrollbar();
                } else {
                    $('#overlay_menu').removeClass('show_layer');
                    $(this).siblings('.tabs').removeClass('gtxactive');
                    $('ul.gtx_second_level').scrollbar('destroy');
                }
                

                $('.sub_desc').hover(function () {
                    $(this).siblings('a').addClass('orange');
                }, function () {
                    $(this).siblings('a').removeClass('orange');
                });
            });

            $('ul.gtx_second_level li').hover(function (event) {
                event.preventDefault();

                $('.active').removeClass('active');
                $(this).addClass('active');
                $('.gtx_third_level').hide();

                $($(this).attr('data-index')).show();
                
                
                if($($(this).attr('data-index')).find('.levelcontentwrapper').innerHeight() < $($(this).attr('data-index')).find('.heightcheck').outerHeight() ||
                  $($(this).attr('data-index')).find('.levelcontentwrapper').innerHeight() < $($(this).attr('data-index')).find('.heightcheck').height()){
                    $($(this).attr('data-index')).find('.levelcontentwrapper').scrollbar();
                }   
            });

            $('.tabs').mouseleave(function () {
                $('.nav .hidden-xs').removeClass('opc');
                $('#overlay_menu').removeClass('show_layer');
                $('.catalog-btn').removeClass('active');
                $(this).removeClass('gtxactive');
                $('ul.gtx_second_level').scrollbar('destroy');
            });



        });


    } else {
        setTimeout(function () {
            defer_menu()
        }, 200);
    }

}

defer_menu();
