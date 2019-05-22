$(function(){
    //TODO убрать это на рабочем сайте
    $('img').each(function(){

        var src = $(this).attr('src');
        if ( src.indexOf('/upload')>=0 && !imageExists(src)){
            $(this).attr('src','http://mht.ru/'+src);
        }

    });

    $('.mainaccordwrap').find('.menuicon').each(function(){
        var background = $(this).css('background-image').replace('url("http://m.mht.site','url("http://mht.ru') ;
        if ( background.indexOf('/upload')>=0 && !imageExists(background)) {
            $(this).css('background-image',  background);
        }
    });

});


function imageExists(image_url){

    var http = new XMLHttpRequest();

    http.open('HEAD', image_url, false);
    http.send();

    return http.status != 404;

}