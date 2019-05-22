$(function(){
    $('.other-sections-brand').hide();
   $('#brand-section-select').change(function(){
       var $option = $(this).find('option:selected');
       var selectedSectionId = $option.attr('value');

       $( '.pagesletterswrap' ).slick('slickGoTo', 0);

       if (selectedSectionId==0){

           $('.category-brands').each(function(){

               $(this).parent('div').show();

               var $a = $(this).find('a');
               var link = $a.data('brand-link');
               $a.attr('href',link);
           });

           setTimeout(function(){
               $('.other-sections-brand').hide();
           },1);


       }else {


           $('.category-brands').each(function(){
               $(this).parent('div').hide();
               $(this).hide();
               var $a = $(this).find('a');
               var link = $option.data('link')+$a.data('link');
               $a.attr('href',link);
           });
           $('.category-brand-' + selectedSectionId).each(function(){
               $(this).parent('div').show();
               $(this).show();
           });
       }
       //console.log(selectedSectionId);
   });
});
