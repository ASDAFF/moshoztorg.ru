$(function(){
	$(".faq_page").hide();
	$(".vacancy_header").click(
		function(){
			$(".faq_page input[name='job']").val($(this).find(".js-respond-vacation-name").text());
			$(".faq_page").show();
			
			vacancy = $(this).closest(".vacancy");
			button = $(vacancy).find(".button");
			
			if($(vacancy).is(".active")){
				$(vacancy).find(".vacancy_description").slideUp(500,
					function(){
						$(vacancy).removeClass("active");
						$(button).text("развернуть ↓");
					}
				);	
			}else{
				$(vacancy).find(".vacancy_description").slideDown(500,
					function(){
						$(vacancy).addClass("active");
						$(button).text("свернуть ↑");	
					}
				);		
			}
		}
	);
});