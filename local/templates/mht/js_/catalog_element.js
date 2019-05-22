$(function(){
	$('.js-compare-switch a.js-compare-change').click(function(e){
		e.preventDefault();

		var $button = $(this),
			$holder = $button.closest('.js-compare-switch');
		if($button.is('.compare-yes')){
			$holder.removeClass('compare-yes').addClass('compare-no');
		}
		else{
			$holder.removeClass('compare-no').addClass('compare-yes');
		}

		$.post($button.attr('href'));
	});

	$('.js-fav-switch a.js-fav-change').click(function(e){
		e.preventDefault();

		var $button = $(this),
			$holder = $button.closest('.js-fav-switch');
		if($button.is('.fav-yes')){
			$holder.removeClass('fav-yes').addClass('fav-no');
		}
		else{
			$holder.removeClass('fav-no').addClass('fav-yes');
		}

		var action = 'fav-' + ($button.is('.fav-no') ? 'add' : 'remove'),
			id = parseInt($button.attr('data-id'));

		if(!id){
			return;
		}
		$.post('/ajax.php', {
			action : action,
			id : id
		}, function(r){
			if(action == 'fav-add'){
				$('.js-fav-switch .fav-yes').attr('data-id', r);
			}
			// location.href = '/personal/favorite/';
		});
	});

	$('.js-buy-holder').each(function(){
		var $holder = $(this),
			$amount = $holder.find('.js-amount'),
			$buy = $holder.find('.js-buy'),
			id = $holder.attr('data-id');

		$amount.spinner({
			min : 1,
			max : 999,//$amount.attr('data-max'),
			step : 1,
			icons : {
				down: "ui-icon-minus",
				up: "ui-icon-plus"
			} 	
		});

		(function(){
			var callback = function(e){
					var initValue = $amount.val(),
						value = parseInt(initValue.replace(/[^\d]/, '')) || 0;

					if(!initValue){
						return;
					}

					if(value > 999){
						value = 999;
					}

					if(initValue == value){
						return;
					}

					$amount.val(value);
				};

			$amount
				.keyup(callback)
				.change(callback);
		})();
/*
		$buy.click(function(e){
			e.preventDefault();
			$.post('/ajax.php', {
				action : 'basket-add',
				id : id,
				amount : $amount.val()
			}, function(){
				mht.updateBasket();
			});
			mht.animateToBasket($holder.closest('.about').find('.product_image_original'));
		});
*/

	$buy.click(function(e){
			var amount = $amount.val()||1;
			for(var i=0;i<amount;i++){
				$.post($(this).attr("href"), {}, function(){
					mht.updateBasket();
				});
			}
			mht.animateToBasket($holder.closest('.about').find('.product_image_original'));
			e.preventDefault();
		});
	})
		
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
	$(".product_images .image").click(function(e){
		e.preventDefault();
		var	$cur = $(this),
			big = $cur.attr('data-href'),
			img = $cur.attr('data-big'),
			$holder = $cur.closest('.product'),
			$big = $holder.find('.js-big-img'),
			$img = $holder.find('.product_image_original');

		$big.attr('src', big);
		$img.attr('src', img);
		mht.fitImages();
		mht.initZoomy();
	});
	$(".product_images .video").click(function(){
		$.fancybox({	
			type:'iframe',
			href:$(this).attr("data-href")
		});
	});
});