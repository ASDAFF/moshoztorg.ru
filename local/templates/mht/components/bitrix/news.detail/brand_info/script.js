$(function(){
   $('.brand-preview').find('a.show-more').on('click',function(){
        $('.brand-preview').hide();
       $('.brand-detail').show();
       return false;
   });
});