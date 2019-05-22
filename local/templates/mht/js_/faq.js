$(function(){
	$(".question_block .question").click(
		function(){
			question_block = $(this).closest(".question_block");
			if($(question_block).is(".active")){
				$(question_block).find(".answer").slideUp(500,
					function(){
						$(question_block).removeClass("active");
					}
				);	
			}else{
				$(question_block).find(".answer").slideDown(500,
					function(){
						$(question_block).addClass("active");
					}
				);		
			}
		}
	);	
});