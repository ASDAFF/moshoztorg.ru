$(function(){
   $('#brand-section-select').change(function(){
       var $option = $(this).find('option:selected');
       var selectedSectionId = $option.attr('value');
       if (selectedSectionId==0){
           $('.category-brands').show();
       }else {
           $('.category-brands').hide();
           $('.category-brand-' + selectedSectionId).show();
       }
       //console.log(selectedSectionId);
   });
});
