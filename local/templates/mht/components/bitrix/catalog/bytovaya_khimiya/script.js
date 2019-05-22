$(document).ready(function(){
	var phei = $('.seotext_block p').height();
	if (phei > 80){
		$('.seotext_block .viewfulltext').addClass('active');
	}
	
	$('.seotext_block .viewfulltext').bind('click', function(){
		$('.seotext_block').css('max-height', 50000);
		$(this).hide();
	});

	$('nav.breadcrumbs').addClass('custom-breadcrumbs');
});