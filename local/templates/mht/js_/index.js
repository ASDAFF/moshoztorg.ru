$(function(){
	
	$('.products_block .product').each(function(i){
		$(this).before($('.best_products .best_product').eq(i).clone().addClass('mobile-beauty'));
	});
	
	// var area = ["Центральный","Северный","Северо-Восточный","Восточный","Юго-Восточный","Южный","Юго-Западный","Западный","Северо-Западный","Зеленоградский","Троицкий","Новомосковский"];
	
	// $("input[name='area']").autocomplete({
	// 	source: area,
	// 	minLength: 0,
	// 	position: { my: "left top", at: "left bottom", collision: "flip" }
	// }).val("Центральный");	
	
	$(document).click(
		function(){
			$("input[name='area']").autocomplete( "close" );
		}
	);
	
	$(document).on("click",".maket .product_cart",
		function(){
			product = $(this).closest(".product").find(".product_image_original");
			cart = $(".maket .header_content .backet");
			img = product
					.clone()
					.offset({
						top:product.offset().top,
						left:product.offset().left
					})
					.css({
						'opacity':'0.7',
						'position':'absolute',
						'height':product.height(),
						'width':product.width(),
						'z-index':'1000',
						'border-radius':'0%'
					})
					.appendTo($('body'))
					.animate({
						'top':cart.offset().top+10,
						'left':cart.offset().left,
						'width':35,
						'height':35,
						'border-radius':'50%'
					}, 1000,
						function(){
							img.remove();	
						}
					);
					
			return false;
		}
	);

	var onscroll = function(){
		$(".beauty_bg").css("backgroundPosition","0px "+($(document).scrollTop()*0.2)+"px");
	};

	$(window).scroll(onscroll).on('touchmove', onscroll);
	$(".best_info_central_buttons .row .section_button").click(
		function(){
			section = $(this).attr("data-section-href");
			row = $(this).closest(".row");
			index = $(row).index(".best_info_central_buttons .row");
			next_between_row = $(".best_info_central_buttons .row:eq("+(index+1)+")");
			next_row = $(".best_info_central_buttons .row:eq("+(index+2)+")");
			title = $(this).attr("data-title");
			
			$(row).find("a").removeClass("active");
			$(this).addClass("active");
			
			$(".best_info_central_buttons .row:gt("+index+")").hide();
			
			$(next_between_row).find("div").hide();

			$(next_row).find("a").removeClass("active");
			$(next_row).find("a").hide();
			$(next_row).find("a[data-section='"+section+"']").show();

			this_index = $(row).find("a:visible").index($(this));
			all_index = $(row).find("a:visible").size(); 
			
			if(all_index % 2 == 1 && this_index == Math.floor(all_index * 0.5)){
				$(next_between_row).find(".v").show();
			}else{
				if(this_index < all_index * 0.5){
					left = $(next_between_row).width() * 0.5 - $(this).width() * 0.5 - 1 - (all_index * 0.5 - this_index - 1) * $(this).width();
					$(next_between_row).find(".lt").css({
						"left":left
					});
					$(next_between_row).find(".lt").show();
					$(next_between_row).find(".h").css({
						"left":left + 25,
						"width":$(next_between_row).width() * 0.5 - 50 - left
					});
					$(next_between_row).find(".h").show();
					$(next_between_row).find(".rb").show();
				}else{
					right = $(next_between_row).width() * 0.5 - $(this).width() * 0.5 - 1 - (this_index - all_index * 0.5) * $(this).width();
					$(next_between_row).find(".rt").css({
						"right":right
					});
					$(next_between_row).find(".rt").show();
					$(next_between_row).find(".h").css({
						"left":$(next_between_row).width() * 0.5 + 25,
						"width":$(next_between_row).width() - right - $(next_between_row).width() * 0.5 - 50
					});
					$(next_between_row).find(".h").show();
					$(next_between_row).find(".lb").show();
				}
			}
			$(next_between_row).find(".title").show();
			$(next_between_row).find(".title").text(title);				
			$(next_between_row).show();
			
			$(next_row).show();
			return false;
		}
	);
});