jQuery.fn.reverse = [].reverse;


function getDocumentScroll(){
	scrollTop = window.pageYOffset || document.documentElement.scrollTop;
	return scrollTop;
}

function getWindowHeight(){
	if (document.body && document.body.offsetWidth) {
		winH = document.body.offsetHeight;
	}
	if (document.compatMode=='CSS1Compat' &&
		document.documentElement &&
		document.documentElement.offsetWidth ) {
		winH = document.documentElement.offsetHeight;
	}
	if (window.innerWidth && window.innerHeight) {
		winH = window.innerHeight;
	}
	return winH;
}

function getDocumentHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

function getWindowWidth(){
	if (document.body && document.body.offsetWidth) {
		winW = document.body.offsetWidth;
	}
	if (document.compatMode=='CSS1Compat' &&
		document.documentElement &&
		document.documentElement.offsetWidth ) {
		winW = document.documentElement.offsetWidth;
	}
	if (window.innerWidth && window.innerHeight) {
		winW = window.innerWidth;
	}
	return winW;
}

function getDocumentWidth() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollWidth, D.documentElement.scrollWidth),
        Math.max(D.body.offsetWidth, D.documentElement.offsetWidth),
        Math.max(D.body.clientWidth, D.documentElement.clientWidth)
    );
}


function getBoundingClientRect(){
	var ret;
	if (node.getBoundingClientRect ){
		ret = node.getBoundingClientRect();
	}else
	{
		var element = node;
		var coords = { x: 0, y: 0, width: element.offsetWidth,
		height:element.offsetHeight };
		while (element) {
			coords.x += element.offsetLeft;
			coords.y += element.offsetTop;
			element = element.offsetParent;
		}
		ret =  {left:coords.x, right:coords.x +
		coords.width,top:coords.y,bottom:coords.y+coords.height};
	}
}

$(function(){


	// События для Метрики и GA
	// Event hayhop.show генерирует функция mht.modal при показе окна
	$(document).on('hayhop.show', function(e, id){
		switch(id){
			case '#one_click_buy':
				try { ga('send', 'event', 'UX', 'open-1click-form'); } catch(err) { console.log(err); }
				try { yaCounter26064957.reachGoal('1click_form_open'); } catch(err) { console.log(err); }
				$("#one_click_buy input[name='count']").val($(".amount input").val());
				break;
		}
	});

	$(document).on('formajax.success', function(e, id, form){
		switch(id){
			case 'oneclickform':
				oneClickBuyAnalytics();
				break;
		}
	});

	function oneClickBuyAnalytics(){

		// Собираем информацию о товаре
		var product = {};
		var element = $('.product-element-js-info').eq(0);
		product.id = element.data('id');
		product.name = element.data('name');
		product.sku = element.data('sku');
		product.category = $('nav.breadcrumbs li').eq(2).text();
		product.price = element.data('price');

		var order_id = '1click-' + new Date().getTime();

		// Google Analytics

		ga('require', 'ecommerce', 'ecommerce.js');
		ga('set', 'dimension1', '1click');

		ga('ecommerce:addTransaction', {
		  'id': order_id,  // Номер заказа. Неизвестен, на самом деле
		  'revenue': product.price,               // Полная стоимость заказа. Обязательно.
		  'shipping': '0',                  // Стоимость доставки.
		  'tax': '0'                     // Налог.
		});

		ga('ecommerce:addItem', {
		  'id': order_id,                     // Номер заказа. Обязательно.
		  'name': product.name,    // Название товара. Обязательно.
		  'sku': product.id,                 // Артикул товара. Обязательно.
		  'category': product.category,         // Категория товара. Обязательно.
		  'price': product.price,                 // Цена одного товара. Обязательно.
		  'quantity': '1'                   // Количество товара (integer). Обязательно.
		});

		ga('ecommerce:send');
		ga('ecommerce:clear');
		ga('send', 'event', 'ecommerce', 'buy', '1click');

		// Яндекс.Метрика

		var yaParams = {
		  order_id: order_id, //номер заказа
		  order_price: product.price, //сумма заказа
		  goods: 
		     [
		        {
		          id: product.id, //идентификатор или артикул товара
		          name: product.name, //название товара
		          price: parseFloat(product.price), //цена товара
		          quantity: 1 //количество товара
		        }
		      ]
		};
		if (typeof(yaCounter26064957)!=='undefined') {
			yaCounter26064957.reachGoal('order_done_mht', yaParams);
		}

		//console.log('Order 1 hit done!', product, order_id);

	}

	(function(data){
		if(!data){
			return;
		}

		var bbs = new BigBanners(),
			slider = new BannersSlider();

		$.each(data, function(i, oneData){
			bbs.add((new BigBanner())
				.setHeight     (oneData.height)
				.setImages     (oneData.images)
				.setLink       (oneData.link)
				.setPhoneColors(oneData.phones));
		});

		slider
			.setHolder($('.beauty'))
			.setBanners(bbs)
			.updateDots()
			.updateArrows()
			.delaySlide();

		window.bbs = bbs;
		window.slider = slider;

		/*setInterval(function(){
			slider.setBanner(bbs.getNext());
		}, 5000);*/

		function BannersSlider(){
			var me = this,
				banners,
				$holder,
				$bg,
				$phones,
				$phoneNumber,
				$fgLink,
				$dots,
				$buttons,
				$left,
				$right,
				isAuto = true;

			$.extend(me, {
				setBanners : function(banners_){
					banners = banners_;
					
					var phoneColors = banners.getByIndex(0).getPhoneColors();
					$(".beauty .logotype img").attr("src" , phoneColors.shadow ? '/local/templates/mht/images/logotype-white@2x.png' : '/local/templates/mht/images/logotype-black@2x.png');
					
					return me;
				},
				delaySlide : function(){
					setTimeout(function(){
						if(!isAuto){
							return;
						}
						me
							.setBanner(banners.getNext())
							.setActiveDotByIndex(banners.getLastIndex())
						;
						me.delaySlide();
					}, 10000);
				},
				setIsAuto : function(isAuto_){
					isAuto = !!isAuto_;
					return me;
				},
				updateDots : function(){
					$dots.html('');
					for(var i=0; i < banners.getAmount(); i++){
						(function(){
							var $dot = $('<span>', {
								'class' : 'dot ' + (i == 0 ? 'active' : ''),
								click : function(event){
									event.preventDefault();
									event.stopPropagation();
									$dots.children().removeClass('active');
									$dot.addClass('active');
									me
										.setBanner(banners.getByIndex($dot.index()))
										.setIsAuto(false);
								}
							}).appendTo($dots);
						})();

					}
					return me;
				},
				setActiveDotByIndex : function(index){
					$dots
						.children()
						.removeClass('active')
						.eq(index)
							.addClass('active');
					return me;
				},
				updateArrows : function(){
					$left.click(function(event){
						event.preventDefault();
						event.stopPropagation();
						me
							.setBanner(banners.getPrev())
							.setIsAuto(false)
							.setActiveDotByIndex(banners.getLastIndex());
					});

					$right.click(function(event){
						event.preventDefault();
						event.stopPropagation();
						me
							.setBanner(banners.getNext())
							.setIsAuto(false)
							.setActiveDotByIndex(banners.getLastIndex());
					});

					return me;
				},
				setHolder : function(holder_){
					$holder      = holder_;
					$bg          = $holder.find('.beauty_bg');
					$fgLink      = $holder.find('.beauty_fg');
					$phones      = $holder.find('.phone_number a, .phone_notis');
					$phoneNumber = $holder.find('.phone_number');
					$fg          = $fgLink.find('img');
					$buttons 	 = $holder.find('.buttons');
					$dots        = $buttons.find('.dots');
					$left        = $buttons.find('.left');
					$right       = $buttons.find('.right');
					$logotype 	= $holder.find('.logotype img');
					
					return me;
				},
				setBanner : function(banner){
					var back         = banner.getBackSmallImage(),
						front        = banner.getFrontSmallImage(),
						phoneColors  = banner.getPhoneColors(),
						bannerHeight = banner.getHeight(),
						height       = 825; //bannerHeight + 880;

					$bg.animate({
						opacity : 0.3
					}, then);

					function then(){
						/* $holder.attr({
							style : '\
								height: ' + height + 'px;\
							'
						}); */

						/* $bg.attr({
							style : '\
								background-image: url(' + back.src + ');\
								width           : ' +  back.width + 'px;\
								height          : ' +  height + 'px;\
								margin-left     : ' + -back.width/2 + 'px;\
								opacity         : 0.3;\
							'
						}); */

						$bg.attr({
							style : '\
								background-image: url(' + back.src + ');\
								opacity         : 0.3;\
							'
						});


						$fg.attr({	
							'src' : front.src
						});

						$fgLink.attr({
							'href' : banner.getLink()
						});

						$phones.css({
							color: phoneColors.color
						});

						$phoneNumber.css({
							textShadow : phoneColors.shadow ? '1px 1px 2px rgba(0, 0, 0, 0.75)' : 'none'
						});
						
						$logotype.attr("src" , phoneColors.shadow ? '/local/templates/mht/images/logotype-white@2x.png' : '/local/templates/mht/images/logotype-black@2x.png');
						
						$bg.animate({
							opacity : 1
						});
					}


					return me;
				},
			});
		}

		function BigBanners(){
			var me = this,
				all = [],
				lastIndex = 0;

			$.extend(me, {
				add : function(bigBanner){
					all.push(bigBanner);
					return me;
				},
				getAll : function(){
					return all;
				},
				getAmount : function(){
					return all.length;
				},
				getNext : function(){
					lastIndex++;
					if(lastIndex >= all.length){
						lastIndex = 0;
					}
					return me.getByIndex(lastIndex);
				},
				getPrev : function(){
					lastIndex--;
					if(lastIndex < 0){
						lastIndex = all.length - 1;
					}
					return me.getByIndex(lastIndex);
				},
				getLastIndex : function(){
					return lastIndex;
				},
				getByIndex : function(index){
					return all[index] || null;
				}
			});

		}

		function BigBanner(data){
			var me = this,
				height,
				images,
				link,
				phoneColors;

			$.extend(me, {
				init : function(data){
					$.each(data, function(oneData){
						all.push(new BigBanner(oneData));
					});
				},
				setHeight : function(height_){
					height = parseInt(height_);
					return me;
				},
				getHeight : function(){
					return height;
				},
				setImages : function(images_){
					images = images_;
					return me;
				},
				getBackSmallImage : function(){
					return images.back.small;
				},
				getFrontSmallImage : function(){
					return images.front.small;
				},
				setLink : function(link_){
					link = link_;
					return me;
				},
				getLink : function(){
					return link;
				},
				setPhoneColors : function(phoneColors_){
					phoneColors = phoneColors_;
					return me;
				},
				getPhoneColors : function(){
					return phoneColors;
				}
			});
		}

	})(mht.bigbanners);

	$('.js-unsubscribe').click(function(e){
		e.preventDefault();
		$.post('/ajax.php', {
			action : 'unsubscribe',
		})
	});

	$(document).on('keyup', 'input[name=name][type=text]', function(){
		var $this = $(this),
			previousValue = $this.data('previousValue'),
			value = $this.val();

		if(value == previousValue){
			return;
		}

		previousValue = value;
		value = value.replace(/[^a-zа-яё]+/gi, '');

		if(value == previousValue){
			return;
		}

		$this.data('previousValue', value).val(value);
	});

	/*
	$('.discount_number_field').inputmask('999999999999999999999999999999999999');

	$('input[type*="tel"],input[name*="phone"],input.mask-phone').val("");
	$('input[type*="tel"],input[name*="phone"],input.mask-phone').attr("placeholder","+7 (___) ___ - __ - __");
	$('input[type*="tel"],input[name*="phone"],input.mask-phone').attr("type","tel");
	$('input[type*="tel"],input[name*="phone"],input.mask-phone').attr("pattern","^\+7\s\([0-9]{3}\)\s[0-9]{3}\s-\s[0-9]{2}\s-\s[0-9]{2}$");
	$('input[type*="tel"],input[name*="phone"],input.mask-phone').mask('+7? (999) 999 - 99 - 99');*/
	/*
	(function($plusos) {
		if(!$plusos.length){
			return;
		}
		
		$('body').append(
			$('<script>').attr({
				type : 'text/javascript',
				charset : 'UTF-8',
				async : true,
				src : 'http://share.pluso.ru/pluso-like.js'
			})
		);

		$plusos.fadeIn(200);
	})($('.pluso'));
	*/

	// Добавляет кнопки "В избранное" и "Добавить к сравнению" в списке товаров.

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
			delta1 = 15,
			delta2 = 16,
			compareUrl = $product.data('compare-url'),
			onmouseover = function(){
				clearTimeout(leaveTimeout);
				
				if(visible){
					return;
				}
				if(!$hover){
					if($product.find('img').filter(function(i, img){
						return !img.complete
					}).length){
						return;
					}
					$hover = $('<div></div>');
					$bg = $('<div></div>');
					
					if(iblockId && productId){
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
						}else{
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
								$product.append(div);	
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

						$('<a>', {
							href : '/personal/favorite/',
							'class' : 'item fav',
							text : 'Перейти к избранному'
						})
							.appendTo($moveList)
							.toggle(isFaving);
					}

					$('body').append($hover).append($bg);
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

				$bg
					.show()
					.css($.extend({}, css, {
						position : 'absolute',
						background : '#fff',
						zIndex : 4,
						left : css.left - delta1,
						top: css.top - delta1,
						width : css.width + delta1 * 2,
						height : css.height + delta1 * 2
					}));
				$hover
					.show()
					.css($.extend({}, css, {
						position : 'absolute',
						left : css.left - delta2,
						top: css.top - delta2 + (css.height / 2),
						width : css.width + delta2 * 2,
						opacity : 0,
						height : 0
					}))
					.stop()
					.animate({
						top: css.top - delta2,
						height : css.height + delta2 * 2,
						opacity : 1
					}, 300);

				$actions && $actions.show();
			},
			onmouseleave = function(){
				$actions && $actions.hide();
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

	(function(){
		var $up = $('.go-up'),
			onscroll = function(){
				$up[($(window).scrollTop() > (window.innerHeight / 2)) ? 'fadeIn' : 'fadeOut'](500);
			};
		$(window).scroll(onscroll);
		onscroll();
	})();
	$('.go-up').click(function(e){
		e.preventDefault();
		$('body, html').animate({
			'scroll-top' : 0
		});
	});

	$('form[action="/search/"]').submit(function(e){
		if(!$(this).find('[name="q"]').val().length){
			e.preventDefault();
		}
	});
	
	(function($holder){
		var visible = false,
			loading = false,
			$links = $holder.find('.js-links'),
			prependLi = function(content){
				$links
					.prepend(
						$('<li></li>')
							.append(content)
					);
			};

		$('.header_content .top_menu a').reverse().each(function(){
			prependLi($(this).clone())
		});

		// if(location.pathname != '/'){
		// 	prependLi($('<a href="/">Главная</a>'));
		// }

		(function($menu){
			var $button = null;
			$('.js-menu-toggler').click(onclick);

			function onclick(e){
				e.preventDefault();

				if($menu.length == 0){
					getMenu($(this));
					return;
				}

				toggle();
			}

			function getMenu($button){
				$.post('/ajax.php', {
					action : 'get-section-menu',
					data : $button.attr('data-data')
				}, function(html){
					if(!html.length){
						return;
					}
					$menu = $('<div class="catalog_page"><div class="catalog_block">' + html + '</div></div>');
					$button.after($menu);
					$menu = $menu.find('.catalog_menu');
					$menu.find('.js-menu-toggler').click(onclick);
					toggle();
				});
			}

			function toggle(){
				if(!$menu.toggleClass('visible').is('.visible')){
					return;
				}

				$('html, body').animate({
					scrollTop : $menu.offset().top - $('header.header').height()
				}, 500);
				$menu
					.transit({
						perspective : '1000px',
						rotateX : '-40deg',
						opacity : 0,
						transformOrigin : '50% 0'
					}, 0)
					.transit({
						rotateX : '0deg',
						opacity : 1,
					}, 500);
			}
		})($('.catalog_block .catalog_menu'));

		function toggle(){
			if(loading){
				return;
			}
			loading = true;
			visible = !visible;
			$('body, html').css({
				overflow : visible ? 'hidden' : 'auto'
			});
			if(visible){
				$holder.show().transit({
					rotate3d : '1,0,0,0deg'
				}, 600, 'ease', function(){
					loading = false;
				});
				return;
			}

			$holder.transit({
				rotate3d : '1,0,0,-90deg'
			}, 600, 'ease', function(){
				$holder.hide();
				loading = false;
			});
		}

		/*$holder.transit({
			perspective : '500px',
			rotate3d : '1,0,0,-90deg',
			transformOrigin : '50% 0'
		}, 0);*/

		$('.header_content .menu_button').each(function(){
			var $button = $(this);
			$button.click(function(){
				toggle();
			});
		});
		
	})($('.phone-menu'));
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

	(function(){
		var $output = $('.js-vacancy-form [name=job]');

		$('.js-respond-vacation-holder').each(function(){
			var $holder = $(this),
				$button = $holder.find('.js-respond-vacation'),
				name = $holder.find('.js-respond-vacation-name').text();

			$button.click(function(){
				$output.val(name);
			});
		});
		
	})();
	$(document).on("click", ".pay_system .one",
		function(e){
			if ($(e.target).is(":not(label)") && $(e.target).is(":not(input)")) {
				if($(e.target).closest('label').length == 0){
					console.log(e.target);
					$(this).find("label").get(0).click();
				}
			}
		}
	);


		$('.js-height-fit').each(function(){
			var $holder = $(this),
				getSize = function($o){
					return {
						width : $o.width(),
						height : $o.height(),
					};
				},
				holderSize = getSize($holder);

			if($holder.css('position') == 'static'){
				$holder.css({
					position : 'relative',
					top : 0
				});
			}

			if(window.innerWidth < 640){
				return;
			}

			$holder.find('.js-to-middle').each(function(){
				var $element = $(this),
					elementSize = getSize($element),
					position = $element.position();

				if($element.css('display') == 'inline'){
					$element.css('display', 'inline-block');
				}
				$element.css({
					position : 'absolute',
					top : (holderSize.height/* - elementSize.height*/) / 2,
					left : position.left
				});
			});
		});
	
	$('.js-select-filter-helper').each(function(){
		var $holder = $(this),
			$select = $holder.find('select'),
			$input = $holder.find('input[type="hidden"]');
		$select.selectmenu({
			change : function(){
				var $option = $select.find('option:selected');

				$input.attr({
					name : $option.attr('data-name'),
					value : $option.attr('value'),
				});
				$select.selectmenu('refresh');
			}
		})
	});

	$('.js-remove-from-basket').click(function(){
		var $row = $(this).closest('.row'),
			id = $row.attr('data-id');
		$row.animate({
			opacity : 0.3
		});
		$.post('/ajax.php', {
			action : 'basket-remove',
			id : id
		}, function(){
			$row.slideUp(function(){
				$row.detach();
			});
		});
		return false;
	});



	$.extend(mht, {
		updateBasket : function(){
			$.post('/ajax.php', {
				action : 'basket-get-amount',
			}, function(n){
				n = parseInt(n);
				//n = n + ' товар' + mht.runum(n, 'ов', '', 'а');
				$('#header-cart-number').html(n);

				try { ga('send', 'event', 'ecommerce', 'addtocart'); } catch(err) { }
				try { yaCounter26064957.reachGoal('add_to_cart_mht'); } catch(err) { }

			});
		},
		runum : function(v, w0, w1, w2){
		    var v00 = v % 100,
		        v0 = v % 10;

		    if(v0 == 0 || (v0 > 4 && v0 < 10) || (v00 > 10 && v00 < 20)){
		        return w0;
		    }
		    if(v0 == 1){
		        return w1;
		    }
		    return w2;
		},
		initZoomy : function(){
			console.log('boom');
			var bad = false;
			$('.js-zoomy img').each(function(i,o){
				if(!o.complete){
					bad = true;
					return false;
				}
			});

			if(bad){
				setTimeout(function(){
					this.initZoomy();
				}, 100);
			}
			
			$('.js-zoomy').each(function(){
				var $holder = $(this),
					$img = $holder.find('.product_image_original'),
					$big = $holder.find('.js-big-img'),
					$pane = $holder.find('.js-zoomy-pane'),
					imgOffset = $img.offset(),
					getSize = function($o){
						return {
							width : $o.width(),
							height : $o.height(),
						}
					},
					imgSize = getSize($img),
					bigSize = getSize($big),
					paneSize = getSize($pane),
					entered = false,
					enter = function(){
						entered = true;
						$pane.fadeIn(200);
						imgSize = getSize($img);
						bigSize = getSize($big);
						paneSize = getSize($pane);
					};

				$img
					.off('click')
					.click(function(){
						$.fancybox({	
							href: $big.attr('src')
						});
					});

				if(Modernizr.touch){
					return;
				}

				$img
					.off('mouseenter')
					.off('mousemove')
					.off('mouseleave')
					.mouseenter(function(){
						enter();
					})
					.mouseleave(function(){
						entered = false;
						$pane.fadeOut(200);
					})
					.mousemove(function(e){
						if(!entered){
							enter();
						}
						var
							rel = {
								x : (e.pageX - imgOffset.left) / imgSize.width,
								y : (e.pageY - imgOffset.top) / imgSize.height
							},
							css = {
								left : -bigSize.width * rel.x + paneSize.width / 2,
								top : -bigSize.height * rel.y + paneSize.height / 2,
							};

						// console.log(css);

						$.each([
							['left', 'width'],
							['top', 'height']
						], function(i, o){
							var
								pos = o[0],
								size = o[1];

							if(css[pos] > 0){
								css[pos] = 0;
								return;
							}
						});

						// console.log(css);
						$big.css(css);
					});
			});
		},
		animateToBasket : function($image){
			if(window.innerWidth <= 640){
				return;
			}

			try{
				var $cart = $(".maket .header_content .backet"),
				cartOffset = $cart.offset(),
				imageOffset = $image.offset(),
				$img = $image
					.clone()
					.offset({
						top : imageOffset.top,
						left : imageOffset.left
					})
					.css({
						'opacity' : '0.7',
						'position' : 'absolute',
						'height' : $image.height(),
						'width' : $image.width(),
						'z-index' : '1000',
						'border-radius' : '0%'
					})
					.appendTo($('body'))
					.animate({
						'top' : cartOffset.top+10,
						'left' : cartOffset.left,
						'width' : 35,
						'height' : 35,
						'border-radius' : '50%'
					}, 1000,
						function(){
							$img.remove();
							sbbl.refreshCart();
						}
					);
				$(window).scroll(function(){
					$img.hide();
				});
			}
			catch(e){
				return;
			}
		},
		notify2 : function(){
			$(".form_success").animate({"top":"55px"},250,
				function(){
					$(".form_success .image").animate({"background-size":"35px"},250);		
				}
			);	
			setTimeout(
				function(){
					$(".form_success .close").click();	
				},
				3000
			);
		},
		notify : function(html, time){
			time = parseInt(time) || 5000;
			var $holder = $('.top-notification').clone();
			$holder.find('.text').html(html);
			$('body').append($holder);
			$holder.slideDown(400, function(){
				setTimeout(function(){
					$holder.slideUp(400, function(){
						$holder.remove();
					});
				}, time);
			})
		},
		fitImages : function(settings){
			settings = settings || {};

			$('.product_image').each(function(){
				var
					$holder = $(this),
					$imgs = $holder.find('img').not('.js-big-img');

				resize();
				function resize(){
					var loaded = true;
					$imgs.each(function(i, o){
						if(!o.complete || $(o).height() == 0){
							setTimeout(resize, 100);
							loaded = false;
							return false;
						}
					});

					if(!loaded){
						return;
					}

					// $holder.css('min-width', Math.max.apply(null, $imgs.map(function(){return $(this).width()})));
					var sizes = $imgs.map(function(){
						return parseInt($(this).height()) || 0;
					});
					
					try{
						$holder.css('min-height', Math.max.apply(
							null,
							sizes
						));
					}catch(e){}
					
					var
						holderWidth = $holder.width(),
						holderHeight = $holder.height(),
						zoom = 1.4;

					$imgs.each(function(){
						var $img = $(this),
							imageWidth = $img.width(),
							imageHeight = $img.height(),
							imagePostion = getPosition(imageWidth, imageHeight);

						$img.css(imagePostion);

						/*
						if(!zoomy){
							return;
						}

						$holder
							.off('mouseover')
							.off('mouseleave')
							.on('mouseover', function(){
								var
									newImageWidth = imageWidth * zoom,
									newImageHeight = imageHeight * zoom,
									newImagePosition = getPosition(newImageWidth, newImageHeight);

								$img.stop().animate($.extend({
									width : newImageWidth,
									height : newImageHeight
								}, newImagePosition), 200);
							})
							.on('mouseleave', function(){
								$img.animate($.extend({
									width : imageWidth,
									height : imageHeight
								}, imagePostion), 200);
							});
						*/
					});

					settings.complete && settings.complete();

					function getPosition(imageWidth, imageHeight){
						return{
							left : (holderWidth - imageWidth) / 2,
							top : (holderHeight - imageHeight) / 2,
						};
					}


				}
			});
		},
		fit : function(settings){
			settings = settings || {};

			$('.js-fit').each(function(){
				var $holder = $(this),
					FIX_HEIGHT = 10,
					fit = function($elements){
						var height = Math.max.apply(
							null,
							$elements.map(function(){
								return $(this).height();
							})
						);
						if(!settings.noauto){
							$elements.css('height', 'auto');
						}
						$elements.height(height + FIX_HEIGHT);
						return height;
					};

				for(var i=1; i<1000; i++){
					var $elements = $holder.find('.js-fit-' + i);
					if(!$elements.length){
						break;
					}
					for(var j=1; j<1000; j++){
						var $subset = $elements.filter('.js-subset-' + j);
						if($subset.length == 0){
							if(j > 1){
								break;
							}
							$subset = $elements;
						}

						var height = fit($subset);
						$subset.height(height + FIX_HEIGHT);
						$holder.find('.js-also-' + i).height(height + FIX_HEIGHT);
					}
				}

				var groups = {};
				$holder.find('[data-fit-group]').each(function(){
					var $group = $(this),
						name = $group.attr('data-fit-group');

					if(groups[name]){
						groups[name] = groups[name].add($group);
					}
					else{
						groups[name] = $group;
					}

				});

				var groupElements = {};
				$.each(groups, function(groupName, $group){
					var elements = {};
					$group.find('[data-fit-element]').each(function(){
						var $element = $(this),
							by = $element.attr('data-fit-by'),
							name = $element.attr('data-fit-element'),
							$by = by ? $element.find(by) : null,
							o = {
								$element : $element,
								height : function(){
									// console.log($by);
									return $by ? $by.height() : $element.height();
								}
							};

						if(elements[name]){
							elements[name].push(o);
						}
						else{
							elements[name] = [o];
						}
					});
					groupElements[groupName] = elements;
				});
				
				$.each(groupElements, function(groupName, groups){
					$.each(groups, function(elementName, elements){
						if(!settings.noauto){
							$.each(elements, function(i, o){
								o.$element.css('height', 'auto');
							});
						}

						var height = Math.max.apply(
							null,
							$.map(elements, function(o){
								return o.height();
							})
						);

						$.each(elements, function(i, o){
							o.$element.height(height);
						});
					});
				});

				// console.log(groupElements);
			});
		},
		modal : (function(){
			var $modal = $('.hayhop').hide(),
				$title = $modal.find('.title'),
				$note = $modal.find('.note'),
				$wind = $modal.find('.window'),
				$close = $modal.find('.close'),
				$content = $wind.find('.content');

			$close.click(function(){
				show(false);
			});

			/*$wind.transit({
				perspective : '1000px',
				rotateX : '-40deg',
				transformOrigin : '50% 0'
			}, 0);*/

			function setVisible(visible){
				if(visible){
					$wind.css({
						opacity : 0
					});

					$modal.css({
						opacity : 0
					}).show().transit({
						opacity : 1
					}, 50, function(){
						$wind.transit({
							rotateX : '0deg',
							opacity : 1
						}, 200);
					});
					return;
				}

				$wind.transit({
					rotateX : '-40deg',
					opacity : 0
				}, 100, function(){
					$modal.transit({
						opacity : 0
					}, 100, function(){
						$modal.hide();
					});
				});
			}

			function show(v){
				setVisible(v);

				if(!v){
					return;
				}

				var wind = {
					width : $wind.width(),
					height : $wind.height()
				};

				if(wind.width > window.innerWidth){
					$wind.css({
						marginLeft : 0,
						left : 0
					});
				}
				else{
					$wind.css({
						marginLeft : -wind.width / 2,
						left : '50%'
					});
				}

				if(wind.height > window.innerHeight){
					$wind.css({
						marginTop : 0,
						top : 0
					});
				}
				else{
					$wind.css({
						marginTop : -wind.height / 2,
						top : '50%'
					});
				}
			}

			return function(data){
				if(data.hide == true){
					show(false);
					return;
				}

				$element = data.element;
				$title.html(data.title);
				$note.html(data.note);
				$content.append($element.show());
				show(true);
				$(document).trigger('hayhop.show', [data.id]);
			}
		})()
	});


	$('[data-hayhop]').click(function(e){
		e.preventDefault();
		var $button = $(this),
			q = $button.attr('data-hayhop'),
			$content = $(q).detach();
		mht.modal({
			element : $content,
			title : $button.attr('data-title'),
			id: $button.attr('data-hayhop')
		});
	});
	$('.js-unlog-button').click(function(e){
		e.preventDefault();
		$.post('/ajax.php', {
			action : 'unlog'
		}, function(){
			location.reload();
		})
	})
	$('.js-mouse-horisontal-scroll').each(function(){
		var $holder = $(this),
			direction = 0,
			interval = 0,
			LIMIT = 250,
			DELTA = 8;

		$holder
			.mousemove(function(e){
				var offset = $holder.offset(),
					size = {
						width : $holder.width(),
						height : $holder.height()
					},
					mouseX = e.pageX - offset.left,
					newDirection = 0;


				if(mouseX < LIMIT){
					newDirection = -1;
				}
				else if(mouseX > size.width - LIMIT){
					newDirection = 1;
				}

				if(newDirection == direction){
					return;
				}

				direction = newDirection;
				clearInterval(interval);
				if(direction == 0){
					return;
				}
				interval = setInterval(function(){
					scroll(direction);
				}, 30)
			})
			.mouseleave(function(){
				clearInterval(interval);
				direction = 0;
			});

		function scroll(direction){
			$holder.scrollLeft($holder.scrollLeft() + DELTA * direction);
		}
	})

	mht.fit();
	mht.fitImages({
		complete : function(){
			mht.fit({
				noauto : true
			});
		}
	});

	$(window).load(function(){
		mht.initZoomy();
	});

});

