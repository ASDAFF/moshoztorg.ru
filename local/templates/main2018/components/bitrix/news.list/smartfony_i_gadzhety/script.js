// Добавляет кнопки "В избранное" и "Добавить к сравнению" в списке товаров.
function addProductHoverButtons() {
	
	$('.js-fit .product').each(function(){
	
		// Не применимо к списку сравнения
		if($(this).closest('.comparation').length == 1)
			return;
		
		var $product = $(this).css({
				background : '#fff',
				zIndex : 5,
				position : 'relative',
				overflow : 'hidden',
				top : 0
			}),
			$hover = null,
			$bg = null,
			$actions = null,
			$moveList = null,
			ids = ($product.attr('data-ids') || '').split(':'),
			productId = parseInt(ids[0]),
			iblockId = parseInt(ids[1]),
			isFaving = ids[2] == productId,
			isComparing = ids[3] == '1',
			css = null,
			leaveTimeout = null,
			visible = false,
			delta1 = 9,
			delta2 = 10,
			compareUrl = $product.data('compare-url'),

			onmouseover = function(){
				clearTimeout(leaveTimeout);
				
				if(visible){
					return;
				}
				if(!$hover){

					if(iblockId && productId && ($('.product-actions',$product).length == 0)){

						$actions = $('<div>', {
							'class' : 'product-actions',
						}).appendTo($product);

						if(!($product.closest(".products_block").is(".no-fav"))){
							$('<a>', {
								href : '#',
								'class' : 'fav action ' + (isFaving ? 'active' : ''),
								click : function(e){
									e.preventDefault();
									$product.css({
										opacity : 0.8
									});
									
									$.post('/ajax.php', {
										action : 'fav-' + (isFaving ? 'remove' : 'add'),
										id : productId
									}, function(r){
										if(typeof action !== "undefined" && action == 'fav-add'){
											$('.js-fav-switch .fav-yes').attr('data-id', r);
										}
										$product.css({
											opacity : 1
										});
										// location.href = '/personal/favorite/';
									});

									isFaving = !isFaving;
									$moveList.find('.fav').toggle(isFaving);
									$(this).toggleClass('active', isFaving);
								}
							}).appendTo($actions);
						} else {
							if($product.attr("data-fav-id")){
								
								var div = $("<div class='fav-cross fav-remove'>&nbsp;</div>");
								div.click(
									function(e){
										e.preventDefault();
										$.post('/ajax.php', {
											action : 'fav-remove',
											id : $product.attr("data-fav-id")
										}, function(r){
											$product.fadeOut(500).hide(500).remove();
										});
									}
								);
								if ($product.find('.fav-remove').length < 1) {
									$product.append(div);	
								}
							
							}
						}


						if(!($product.closest(".products_block").is(".no-compare"))){		
							$('<a>', {
								href : '#',
								'class' : 'compare action ' + (isComparing ? 'active' : ''),
								click : function(e){
									e.preventDefault();

									if(isComparing){
										$.post('/ajax.php?action=compare-delete&id1=' + iblockId + '&id2=' + productId, then);
									}
									else{
										$.post(compareUrl + '?ajax_action=Y&action=ADD_TO_COMPARE_RESULT&id=' + productId, then);
									}

									function then(){
										$product.css({
											opacity : 1
										});
									}

									$product.css({
										opacity : 0.8
									});
									isComparing = !isComparing;
									$(this).toggleClass('active', isComparing);
									$moveList.find('.compare').toggle(isComparing);
								}
							}).appendTo($actions);
						}
						
						$moveList = $('<div>', {
							'class' : 'move-list',
						}).appendTo($actions);

						$('<a>', {
							href : compareUrl,
							'class' : 'item compare',
							text : 'Перейти к сравнению'
						})
							.appendTo($moveList)
							.toggle(isComparing);

						if(!($product.closest(".products_block").is(".favorite-products"))){
							
							$('<a>', {
								href : '/personal/favorite/',
								'class' : 'item fav',
								text : 'Перейти к избранному'
							})
								.appendTo($moveList)
								.toggle(isFaving);
							
						} else {
							//хуй
							$('<a>', {
								href : '#',
								'class' : 'item fav',
								text : 'Удалить из избранного'
							})
								.appendTo($moveList)
								.toggle(isFaving)
								.click(
									function(e){
										e.preventDefault();
										$.post('/ajax.php', {
											action : 'fav-remove',
											id : $product.attr("data-fav-id")
										}, function(r){
											$product.fadeOut(500).hide(500).remove();
										});
									}
								);
							
						}
						
						
					}

				}
				visible = true;

				var pos = $product.offset();

				css = {
					background : '#f00',
					position : 'absolute',
					top : pos.top,
					left : pos.left,
					width : $product.width(),
					height : $product.height(),
					zIndex : 3
				};

				$actions && $actions.show();
			},
			onmouseleave = function(){
				$actions && $actions.remove();
				leaveTimeout = setTimeout(function(){
					visible = false;
					if(typeof $hover !== "undefined" && $hover){
						$hover.stop().animate({
							opacity : 0,
							top: css.top - delta2 + (css.height / 2),
							height : 0
						}, 300, function(){
							$bg.hide();
							$hover.hide();
						});
					}
				}, 150);
			};

		$product
			.mouseover(onmouseover)
            .mouseleave(onmouseleave);
			
		$product.mouseover().mouseleave();
	});
	
}


addProductHoverButtons();



	$(".product_cart").click(function(){
		var $this = $(this),
			href = $this.attr('href');
		mht.animateToBasket($this.closest(".product").find(".product_image_original"));
		$.post(href, function(){
            mht.updateBasket();
		});
		return false;
	});


	$(document).on("click",".product-actions .move-list a",
		function(e){
			location.href = $(this).attr("href");
			return false;
		}
	);
	$(document).on("click",".product-actions",
		function(e){
			if($(this).is(e.target)){
				location.href = $(this).closest(".product").find("*[data-click-me]:first").attr("data-click-me");
			}
			return false;
		}
	);


    $(".product_assessment_block").not('.voted').each(function(){
		var $block = $(this),
			$line = $block.find(".product_assessment"),
			maxWidth = $block.width(),
			width = $line.width(),
			editable = true,
			id = $block.attr('data-id');

		$block
			.hover(function(){}, function(){
				if(!editable){
					return;
				}
				$line.width(width);
			})
			.click(function(){
				if(!editable){
					return;
				}
				width = $line.width();
				$.post('/ajax.php', {
					action : 'vote',
					id : id,
					value : Math.ceil(width * 5 / maxWidth)
				});
				$block.addClass('voted');
				editable = false;
			})
			.mousemove(function(e){
				if(!editable){
					return;
				}
				$line.width(e.pageX - $block.offset().left);
			});
	});
