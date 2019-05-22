$(function(){

    $('nav.submenu ul li a[href="/catalog/"]').on('mouseover', function(){

        $('.triplegtmenu').addClass('active');
        return false;
    });

    $('.triplegtmenu .level1 a').on('mouseover', function(){

        var index = $(this).data('index');
        $(this).parent('li').siblings('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('.triplegtmenu .level2').removeClass('active');
        $('.secondlevel'+index).addClass('active');
        return false;
    });

    $('.triplegtmenu').hover('',function(){
        setTimeout(function(){
            if($('.triplegtmenu:hover').length == 0){
                $('.triplegtmenu .level1 li').removeClass('active');
                $('.triplegtmenu .level2').removeClass('active');
                $('.triplegtmenu').removeClass('active');
            };
        }, 500);
    });

    $('.level2_col').find('.seeall').on('click',function(){
        $(this).closest('.category').find('li').show();
        $(this).remove();
        return false;
    });

});