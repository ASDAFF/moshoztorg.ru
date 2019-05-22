$(function(){

    $('.phone').attr("placeholder","+7 (___) ___ - __ - __").attr("type","tel").mask('+7? (999) 999 - 99 - 99');


    $("#submit_one_click_order").on("click",function(){
       var form$ = $(this).closest("form");
       var resultWrapper = form$.closest("div").find(".result_message");
       form$.show();

       $.post( one_click_order_ajax_path,form$.serialize(), function( data ) {
           resultWrapper.html( data );
           if ( data.indexOf('errortext')<0 ) {
               form$[0].reset;
               form$.hide();
           }
//           form$.hide();
       });

       return false;
   });
});
