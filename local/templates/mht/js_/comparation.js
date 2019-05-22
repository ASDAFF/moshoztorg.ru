$(function(){
	$(".comparation .categories li").click(
		function(){
			$(this).closest(".categories").find("li").removeClass("active");
			$(this).addClass("active");	
			$(".comparation .comparation_list .compar").removeClass("active");
			$(".comparation .comparation_list .compar[data-index='"+$(this).attr("data-index")+"']").addClass("active");
		}
	);	
	$(".comparation .product_name .remove").click(
		function(){
			index = $(this).closest(".row").find(".col").index($(this).closest(".col"));
			$(this).closest(".compar").find(".row").each(
				function(){
					$(this).find(".col:eq("+index+")").remove();	
				}
			);
		}
	);
});