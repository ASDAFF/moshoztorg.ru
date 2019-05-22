$(function(){
	$(".product_assessment_block").each(
		function(){
			$(this).attr("data-progress",$(this).find(".product_assessment").width());
		}
	);
	$(".product_assessment_block").mousemove(
		function(e){
			$(this).find(".product_assessment").width(e.pageX-$(this).offset().left);
		}
	);
	$(".product_assessment_block").hover(
		function(){},
		function(){
			$(this).find(".product_assessment").width($(this).attr("data-progress"));
		}
	);
	$(".product_assessment_block").click(
		function(){
			$(this).attr("data-progress",$(this).find(".product_assessment").width());	
		}
	);
	$(document).on("click",".maket .buttons .product_cart",
		function(){
			product = $(".product_image_original");
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
	$(document).on("click",".maket .product_price_block .product_cart",
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
	$(".product_images div").click(
		function(){
			if($(this).is(".video")){
				$.fancybox({	
					type:'iframe',
					href:$(this).attr("data-href")
				});
			}else{
				$.fancybox({
					href:$(this).attr("data-href")	
				});	
			}
		}
	);
});