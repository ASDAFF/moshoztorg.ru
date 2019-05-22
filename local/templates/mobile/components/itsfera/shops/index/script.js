var shops_map;

$(function(){
    //ymaps.ready(ynadex_map_init);

    $('.shopslist .itemscarousel').on('afterChange',function(event,slick, currentSlide){
        //console.log(slick);
        //console.log(currentSlide);

        var $shop = $('.shopitem').eq(currentSlide).find('.shopviewmore');
        window.location.hash = '#shop'+parseInt( $shop.data('shop-id') );

    });



    $('.shopviewmore').on('click',function(){
        $('.shopslist .itemscarousel')[0].slick.slickGoTo( parseInt( $(this).data('shop-number') ) );
        window.location.hash = '#shop'+parseInt( $(this).data('shop-id') );
        $('.accord_shops').find('.accorditemheading').trigger('click');
        return false;
    });

    $('#region-select').change(function () {
        var $selectedOption = $(this).find('option:selected');
        var regionCode = $selectedOption.attr('value');
        if (regionCode.length>0){
            $('.shopitem').hide();
            $('.region_'+regionCode).show();
        }
    });
    //

});


