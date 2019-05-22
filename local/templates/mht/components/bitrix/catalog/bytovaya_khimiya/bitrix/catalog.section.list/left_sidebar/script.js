$(function(){
    $('li.showhidden a').bind('click', function(){
        $(this).parent('li').hide();
        $(this).parent('li').siblings('li.hidehidden').show();
        $(this).parent('li').siblings('div.hidden_list').slideDown();
        return false;
    });

    $('li.hidehidden a').bind('click', function(){
        $(this).parent('li').hide();
        $(this).parent('li').siblings('li.showhidden').show();
        $(this).parent('li').siblings('div.hidden_list').slideUp();
        return false;
    });

});