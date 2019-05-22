setTimeout(function () {
    $('.loader').fadeOut(500);
    setTimeout(function () {
        $('html').removeClass('loading');
    }, 500);
}, 1000);
// Добавляет кнопки "В избранное" и "Добавить к сравнению" в списке товаров.
function addProductHoverButtons() {

    //
    //TODO для вавода кнопок на новом шаблоне нужна доработка, далее просто копия кода со старого
    //

	// $('.js-fit .product').each(function(){
    //
	// 	// Не применимо к списку сравнения
	// 	if($(this).closest('.comparation').length == 1)
	// 		return;
    //
	// 	var $product = $(this).css({
	// 			background : '#fff',
	// 			zIndex : 5,
	// 			position : 'relative',
	// 			overflow : 'hidden',
	// 			top : 0
	// 		}),
	// 		$hover = null,
	// 		$bg = null,
	// 		$actions = null,
	// 		$moveList = null,
	// 		ids = ($product.attr('data-ids') || '').split(':'),
	// 		productId = parseInt(ids[0]),
	// 		iblockId = parseInt(ids[1]),
	// 		isFaving = ids[2] == productId,
	// 		isComparing = ids[3] == '1',
	// 		css = null,
	// 		leaveTimeout = null,
	// 		visible = false,
	// 		delta1 = 9,
	// 		delta2 = 10,
	// 		compareUrl = $product.data('compare-url'),
	// 		onmouseover = function(){
	// 			clearTimeout(leaveTimeout);
    //
	// 			if(visible){
	// 				return;
	// 			}
	// 			if(!$hover){
	// 				if($product.find('img').filter(function(i, img){
	// 					return !img.complete
	// 				}).length){
	// 					return;
	// 				}
	// 				$hover = $('<div></div>');
	// 				$bg = $('<div></div>');
    //
	// 				if(iblockId && productId){
	// 					$actions = $('<div>', {
	// 						'class' : 'product-actions',
	// 					}).appendTo($product);
    //
	// 					if(!($product.closest(".products_block").is(".no-fav"))){
	// 						$('<a>', {
	// 							href : '#',
	// 							'class' : 'fav action ' + (isFaving ? 'active' : ''),
	// 							click : function(e){
	// 								e.preventDefault();
	// 								$product.css({
	// 									opacity : 0.8
	// 								});
    //
	// 								$.post('/ajax.php', {
	// 									action : 'fav-' + (isFaving ? 'remove' : 'add'),
	// 									id : productId
	// 								}, function(r){
	// 									if(typeof action !== "undefined" && action == 'fav-add'){
	// 										$('.js-fav-switch .fav-yes').attr('data-id', r);
	// 									}
	// 									$product.css({
	// 										opacity : 1
	// 									});
	// 									// location.href = '/personal/favorite/';
	// 								});
    //
	// 								isFaving = !isFaving;
	// 								$moveList.find('.fav').toggle(isFaving);
	// 								$(this).toggleClass('active', isFaving);
	// 							}
	// 						}).appendTo($actions);
	// 					}else{
	// 						if($product.attr("data-fav-id")){
	// 							var div = $("<div class='fav-cross fav-remove'>&nbsp;</div>");
	// 							div.click(
	// 								function(e){
	// 									e.preventDefault();
	// 									$.post('/ajax.php', {
	// 										action : 'fav-remove',
	// 										id : $product.attr("data-fav-id")
	// 									}, function(r){
	// 										$product.fadeOut(500).hide(500).remove();
	// 									});
	// 								}
	// 							);
	// 							$product.append(div);
	// 						}
	// 					}
	// 					if(!($product.closest(".products_block").is(".no-compare"))){
	// 						$('<a>', {
	// 							href : '#',
	// 							'class' : 'compare action ' + (isComparing ? 'active' : ''),
	// 							click : function(e){
	// 								e.preventDefault();
    //
	// 								if(isComparing){
	// 									$.post('/ajax.php?action=compare-delete&id1=' + iblockId + '&id2=' + productId, then);
	// 								}
	// 								else{
	// 									$.post(compareUrl + '?ajax_action=Y&action=ADD_TO_COMPARE_RESULT&id=' + productId, then);
	// 								}
    //
	// 								function then(){
	// 									$product.css({
	// 										opacity : 1
	// 									});
	// 								}
    //
	// 								$product.css({
	// 									opacity : 0.8
	// 								});
	// 								isComparing = !isComparing;
	// 								$(this).toggleClass('active', isComparing);
	// 								$moveList.find('.compare').toggle(isComparing);
	// 							}
	// 						}).appendTo($actions);
	// 					}
	// 					$moveList = $('<div>', {
	// 						'class' : 'move-list',
	// 					}).appendTo($actions);
    //
	// 					$('<a>', {
	// 						href : compareUrl,
	// 						'class' : 'item compare',
	// 						text : 'Перейти к сравнению'
	// 					})
	// 						.appendTo($moveList)
	// 						.toggle(isComparing);
    //
	// 					$('<a>', {
	// 						href : '/personal/favorite/',
	// 						'class' : 'item fav',
	// 						text : 'Перейти к избранному'
	// 					})
	// 						.appendTo($moveList)
	// 						.toggle(isFaving);
	// 				}
    //
	// 				$('body').append($hover).append($bg);
	// 			}
	// 			visible = true;
    //
	// 			var pos = $product.offset();
    //
	// 			css = {
	// 				background : '#f00',
	// 				position : 'absolute',
	// 				top : pos.top,
	// 				left : pos.left,
	// 				width : $product.width(),
	// 				height : $product.height(),
	// 				zIndex : 3
	// 			};
    //
	// 			$bg
	// 				.show()
	// 				.css($.extend({}, css, {
	// 					position : 'absolute',
	// 					background : '#fff',
	// 					zIndex : 4,
	// 					left : css.left - delta1,
	// 					top: css.top - delta1,
	// 					width : css.width + delta1 * 2,
	// 					height : css.height + delta1 * 2
	// 				}));
	// 			$hover
	// 				.show()
	// 				.css($.extend({}, css, {
	// 					position : 'absolute',
	// 					left : css.left - delta2,
	// 					top: css.top - delta2 + (css.height / 2),
	// 					width : css.width + delta2 * 2,
	// 					opacity : 0,
	// 					height : 0
	// 				}))
	// 				.stop()
	// 				.animate({
	// 					top: css.top - delta2,
	// 					height : css.height + delta2 * 2,
	// 					opacity : 1
	// 				}, 300);
    //
	// 			$actions && $actions.show();
	// 		},
	// 		onmouseleave = function(){
	// 			$actions && $actions.hide();
	// 			leaveTimeout = setTimeout(function(){
	// 				visible = false;
	// 				if(typeof $hover !== "undefined" && $hover){
	// 					$hover.stop().animate({
	// 						opacity : 0,
	// 						top: css.top - delta2 + (css.height / 2),
	// 						height : 0
	// 					}, 300, function(){
	// 						$bg.hide();
	// 						$hover.hide();
	// 					});
	// 				}
	// 			}, 150);
	// 		};
    //
	// 	$product
	// 		.mouseover(onmouseover)
	// 		.mouseleave(onmouseleave);
    //
	// 	$product.mouseover().mouseleave();
	// });

}



jQuery(function ($) {

    // ===================================================== Fix fixed bg's jump

    /MSIE [6-8]|Mac/i.test(navigator.userAgent) || $("header, article, footer").each(function () {
        if ("fixed" == $(this).css("backgroundAttachment")) {
            var i = $(this), a = /WebKit/i.test(navigator.userAgent) ? 9 : 8;
            i.addClass("froid-fixed-bg").data({
                bgX: i.css("backgroundPosition").slice(0, i.css("backgroundPosition").indexOf(" ")),
                bgY: i.css("backgroundPosition").slice(i.css("backgroundPosition").indexOf(" ")),
                margin: a
            })
        }
    }), $(window).bind("SIModals.modalsOpen", function () {
        $(".froid-fixed-bg").each(function () {
            var i = $(this);
            i.css("backgroundPosition", "calc(" + i.data("bgX") + " - " + i.data("margin") + "px) " + i.data("bgY"))
        })
    }), $(window).bind("SIModals.modalsClose", function () {
        $(".froid-fixed-bg").each(function () {
            var i = $(this);
            i.css("backgroundPosition", i.data("bgX") + " " + i.data("bgY"))
        })
    });

    // ===================================================== Mobile full-width && disable animation

    if (is_mobile()) {

        // Fix mobile fixed bg's
        $("header, section, article, footer, .section-bg-block::before").each(function () {
            if ("fixed" == $(this).css("backgroundAttachment")) $(this).css('backgroundAttachment', 'scroll');
        });

        // Remove animation
        function removeAnimation(block, className) {
            block.css({
                'transform': 'none',
                '-webkit-transform': 'none',
                '-moz-transform': 'none',
                '-ms-transform': 'none',
                '-o-transform': 'none',
                'transition': 'none',
                '-webkit-transition': 'none',
                'opacity': 1
            }).removeClass(className);
        }

        function removeTransform(block, className) {
            block.css({
                'transform': 'none',
                '-webkit-transform': 'none',
                '-moz-transform': 'none',
                '-ms-transform': 'none',
                '-o-transform': 'none'
            }).removeClass(className);
        }

        removeAnimation($('.cre-animate'), 'cre-animate');
        removeTransform($('.si-floating'), 'si-floating');
        removeTransform($('.si-floating2'), 'si-floating2');
        removeTransform($('.si-floating3'), 'si-floating3');
        removeTransform($('.si-floating4'), 'si-floating4');

        // Mobile stretch
        // $('html, body').css('min-width', '1280px').addClass('mobile');
        // $('html').css('width', window.innerWidth + 'px');

        $('html').css('width', window.innerWidth + 'px');
        $(window).resize(function () {
            $('html').css('width', window.innerWidth + 'px');
        });
        $(window).bind('scroll', function () {
            $('html').css('width', window.innerWidth + 'px');
        });

        // ===================================================== All sound load
        $.ionSound({
            sounds: ["bip-1", "bip-2", "wuf-1", "wuf-2", "wuf-3", "wuf-4"],
            path: template_url + "/sounds/",
            volume: 0
        });

        $('.gtx_secondlevel').removeClass('gtxnotmob');
    }
    else {

        // ===================================================== All sound load
        $.ionSound({
            sounds: ["bip-1", "bip-2", "wuf-1", "wuf-2", "wuf-3", "wuf-4"],
            path: template_url + "/sounds/",
            volume: 0.3
        });

        // ===================================================== Sounds
        $(document).on('mouseenter',
            '.btn, ' +
            '.si-close, ' +
            '.phone-link, ' +
            '.si-jump, ' +
            '.swiper-button-prev, ' +
            '.swiper-button-next, ' +
            '.swiper-pagination-bullet, ' +
            '.tab-link', function () {
                $.ionSound.play('bip-2');
            });
        SIModals.beforeOpen = function () {
            $.ionSound.play('wuf-4');
        };
        SIModals.beforeClose = function () {
            $.ionSound.play('wuf-3');
        };

        // ===================================================== smooth scrolling
        if (!navigator.userAgent.match(/Trident\/7\./)) { // if not IE
            SmoothScroll({stepSize: 100});
        } else {
            document.body.addEventListener("mousewheel", function () {
                event.preventDefault();
                var wd = event.wheelDelta;
                var csp = window.pageYOffset;
                window.scrollTo(0, csp - wd);
            });
        }

        // ===================================================== parallax
        $('.element-parallax').scrollingParallax({
            staticSpeed: 1.4,
            staticScrollLimit: false
        });

        // ===================================================== video bg
        //$('#video-bg').css({'visibility': 'visible'});
        //$('#video-bg')[0].play();
    }

    if (is_OSX()) {
        $('html, body').addClass('osx');
        $('.osx-img').css('max-width','100%').css('height','auto');
    }

    // ===================================================== Init all plugins and scripts
    $.fn.SIInit = function () {

        //Modal photos
        $("[data-fancybox]").fancybox({
            loop: true,
            thumbs: {
                autoStart: true
            },
            youtube: {},
            vimeo: {}
        });

        //Forms
        $('.send-form').SIForms({
            'validateFields': {
                'client_name': 'Укажите ваше имя',
                'client_phone': 'Укажите ваш телефон',
                'client_mail': 'Укажите ваш e-mail'
            },
            'checkExtra': function (form) {

                console.log( $(form) );

                console.log( $(form).find('.form-agree-check').size() );

                if ( ($(form).find('.form-agree-check').size() > 0 ) && !$(form).find('.form-agree-check').hasClass('checked')) {
                    SIPageMessages.show('Для отправки формы вы должны согласиться на обработку персональных данных.');
                    return false;
                }
            },
            'sendSuccess': function (res) {
                //grecaptcha.reset(recaptcha);
                //yaCounter.reachGoal('target' + res.id);
                //ga('send', 'event', res.gcode, res.gcode);
            }
        });

        //Jump links
        $('.si-jump').SIJump();

        //Page messages
        SIPageMessages.init();
    };

    $.fn.SIInit();


    // ===================================================== Modals
    $.fn.SIModalInit = function () {
        SIModals.init();

        // Init modals
        SIModals.attachModal('.open-phone-modal', '.phone-modal', {'.send-extra': 'extra'});
        SIModals.attachModal('.open-authorization-modal', '.authorization-modal', {'.send-extra': 'extra'});

        SIModals.attachModal('.open-text-modal', '.text-modal', false, function () {
            return '.text-modal-' + $(this).data('id');
        });

        // Modal controls
        SIModals.attachClose('.si-close');
    };

    $.fn.SIModalInit();

    //SIModals.afterOpen = function () {
    //grecaptcha.reset(recaptcha);
    //};

    // ===================================================== Styler
    $('input[type=file], input[type=radio], input[type=checkbox], select').styler();
    $('input[type=radio]').change(function () {
        var label = $(this).closest('label'),
            name = $(this).attr('name');
        $('input[name=' + name + ']').closest('label').removeClass('checked');
        if ($(this).is(':checked'))
            label.addClass('checked');
    });
    $('input[type=checkbox]').change(function () {
        var label = $(this).closest('label');
        if ($(this).is(':checked'))
            label.addClass('checked');
        else
            label.removeClass('checked');
    });

    // ===================================================== Counter
    var tomorrow = new Date();
    tomorrow.setHours(24, 0, 0, 0);
    $('.counter').countdown({
        until: tomorrow,
        layout: '<div class="counter-item"><b>{dnn}</b>{dl}</div><div class="counter-separator">:</div>' +
        '<div class="counter-item"><b>{hnn}</b>{hl}</div><div class="counter-separator">:</div>' +
        '<div class="counter-item"><b>{mnn}</b>{ml}</div><div class="counter-separator">:</div>' +
        '<div class="counter-item"><b>{snn}</b>{sl}</div>'
    });

    // ===================================================== spoiler
    $(".spoiler").spoiler();

    // ===================================================== swiper
    if ($('html').find('.block-slider-holder')) {
        var blockSlider = new Swiper('.block-slider', {
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: '.block-pagination',
            nextButton: '.block-next',
            prevButton: '.block-prev',
            paginationClickable: true,
            loop: true,
            autoplay: 2500,
            autoplayDisableOnInteraction: false,
            onSlideChangeStart: function (swiper) {
                $.ionSound.play('wuf-1');
            }
        });
    }
    <!-- Initialize swiper heading -->
    if ($('html').find('.heading-slider-holder')) {
        var headingSlider = new Swiper('.heading-slider', {
            slidesPerView: 1,
            pagination: '.heading-pagination',
            nextButton: '.heading-next',
            prevButton: '.heading-prev',
            paginationClickable: true,
            loop: true,
            onSlideChangeStart: function (swiper) {
                $.ionSound.play('wuf-1');
            }
        });
    }
    <!-- Initialize swiper review -->
    if ($('html').find('.review-slider-holder')) {
        var reviewSlider = new Swiper('.review-slider', {
            slidesPerView: 1,
            autoHeight: 'true',
            pagination: '.review-pagination',
            nextButton: '.review-next',
            prevButton: '.review-prev',
            paginationClickable: true,
            loop: true,
            onSlideChangeStart: function (swiper) {
                $.ionSound.play('wuf-1');
            }
        });
    }

    // =====================================================dotdotdot
    $('.ellipsis').dotdotdot();
    $(window).resize(function () {
        $('.ellipsis').dotdotdot();
    });

    // ===================================================== custom scripts

    //label
    $('.ani-label').click(function () {
        var label = $(this),
            holder = label.parent(),
            input = holder.find('.ani-input');

        holder.toggleClass('active');
        input.focus();
    });

    //menu
    function headerBehaviour() {
        //     if ($(window).scrollTop() > 0) {
        //         $('.layout-header').addClass('active');
        //     }
        //     else {
        //         $('.layout-header').removeClass('active');
        //     }
    }

    headerBehaviour();
    $(window).resize(function () {
        headerBehaviour();
    });
    $(window).bind('scroll', function () {
        headerBehaviour();
    });

    //accordion
    $('.question-item:first').addClass('active').find('.answer').css('display', 'block');
    $('.question-item').each(function () {
        var item = $(this),
            question = item.find('.question'),
            answer = item.find('.answer');
        answer.slideUp();
        if (item.hasClass('active')) {
            $(this).find('.answer').slideDown();
        }
        question.click(function () {
            if (question.parents('.question-item').hasClass('active')) {
                answer.slideUp();
                item.removeClass('active');
            }
            else {
                item.parents('.questions-block').find('.question-item').find('.answer').slideUp();
                answer.slideDown();
                item.parents('.questions-block').find('.question-item').removeClass('active');
                item.addClass('active');
            }
        });
    });

    //equal height
    function setEqualHeight(block) {
        var maxHeight = 0;

        block.each(function () {
            var height = $(this).innerHeight();

            if (height > maxHeight) {
                maxHeight = height;
            }
        });

        return block.css('height', maxHeight);
    }

    setEqualHeight($('.block'));

    // ===================================================== search input for header
    $("header:not(.fixedheader) .input-search").focus(function () {
        $('.layout-header').addClass("focusSearch");
    });
    $(".catalog-search .si-close").click(function () {
        $('.layout-header').removeClass("focusSearch");
    });
    $(document).keyup(function (e) {
        if (e.keyCode === 27) { // escape key maps to keycode `27`
            $('.layout-header').removeClass("focusSearch");
            $(".input-search").blur();
            $('.gtx_secondlevel').removeClass('gtxactive');// close header menu
            $('.gtx_secondlevel.gtxnotmob > li > a').removeClass('active');
            $('.gtx_thirdlevelwrap').html('');
        }
    });
    // ===================================================== loader
    

     // копируем выход с основного шаблона
    $('.js-unlog-button').click(function(e){
        e.preventDefault();
        $.post('/ajax.php', {
            action : 'unlog'
        }, function(){
            location.reload();
        })
    });

    //переход в корзину
    $('div.ordered').click(function(e){
        window.location.href = '/catalog/basket/';
    });

    //переход в избранное
    $('div.favorited').click(function(e){
        window.location.href = '/personal/favorite/';
    });


    //поиск    
    
    
    $(document).ready(function(){
        $input = $('input.input-search.inheader');
        $input.keypress(function(e){
            if(e.keyCode==13){
                $(".catalog-search .input_search_submit.inheader, .js-search-button.inheader").trigger('click');
            }
        });


    });
    
	(function($block){
		var $content = $block.children('.wrapper'),
			$results = $content.find('.pre_result_list'),
			loaded = false;


		$(".hihop_search_block .close").click(function(){
			$('html, body').css({overflow : 'auto'});
			$('.hihop_search_block').css({overflow : 'hidden'});
			$block.animate({
				"left":	$(".search_block").offset().left,
				"top": $(".search_block").offset().top-$(document).scrollTop(),
				"height":$(".search_block").height(),
				"width":$(".search_block").width()
			}, 300,
				function(){
					$block.animate({
						"opacity":0
					},200,
						function(){
							$block.css({
								"display":"none"
							});

						}
					);
				}
			);
		});
	})($(".hihop_search_block"));


    $.extend(mht, {
		updateBasket : function(){

		    // для этого и далее все оригиналы кода в \local\templates\mht\js\global.js
            //console.log( 'mht.updateBasket' );

			$.post('/ajax.php', {
				action : 'basket-get-amount',
			}, function(n){
				n = parseInt(n);
				//для полного наименования
				//n = n + ' товар' + mht.runum(n, 'ов', '', 'а');
				$('.header .ordered .count span').html(n);

				try { ga('send', 'event', 'ecommerce', 'addtocart'); } catch(err) { }
				try { yaCounter26064957.reachGoal('add_to_cart_mht'); } catch(err) { }

			});
		},
		runum : function(v, w0, w1, w2){

            //console.log( 'mht.runum' );

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

            //console.log( 'mht.initZoomy' );

		 },
		animateToBasket : function($image){

            //console.log( 'mht.animateToBasket' );

			if(window.innerWidth <= 640){
				return;
			}

			try{
				var $cart = $(".headerspacerfixed .spaceforanimation"),
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
					}, 2000,
						function(){
							$img.remove();
							//sbbl.refreshCart();
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

            //console.log( 'mht.notify2' );

		 },
		 fitImages : function(settings){

		    //console.log( 'mht.fitImages' );

		 },
		 fit : function(settings){

		    //console.log( 'mht.fit' );

		},
		 modal : (function(){

		    //console.log( 'mht.modal' );

		 })()
	});

    
    $(window).scroll(function () {

        var $up = $('.go-up');
        $up[($(window).scrollTop() > (window.innerHeight / 2)) ? 'fadeIn' : 'fadeOut'](500);

        if ($(window).width() > 767) {
            if ($(this).scrollTop() >= 100) {
                $('header').addClass('fixed');
            } else {
                $('header').removeClass('fixed');
            }
        }
    });
});




$(function() {

    $('.go-up').click(function (e) {
        e.preventDefault();
        $('body, html').animate({
            'scroll-top': 0
        });
    });

});


    $(function() {

        var arFilterParams = {};

        var $form = $('.catalog-search').find('form');
        var sSearchAddress = $form.data('ajax-action');
        var $input = $form.find("input[name=q]");

        $form.submit(function(){
            document.cookie = "search_request=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
        });

        $input.keyup(function(e){
            if(e.keyCode == 13){
                $form[0].submit();
            }
        });

        var $submit = $form.find("input.search_submit");
        $submit.click(function(e){
                $form[0].submit();
        });


        $input.autocomplete({

            open: function(){
                $(this).autocomplete('widget').css('z-index', 5001);
                return false;
            },

            source: function (request, response) {
                $.ajax({
                    url: sSearchAddress,
                    dataType: "json",
                    data: {
                        term: request.term,
                        form: 'main',
                        sessid: $("#sessid").val(),
                        debug: 'Y'

                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },

            select: function (event, ui) {
                var prefix = $input.val();
                var selection = ui.item.label;

                $input.val(selection);
                $form[0].submit();
            }
        }).keyup(function (e) {

            arFilterParams = {};

            if (e.which === 13) {
                $(".ui-autocomplete").hide();
            }
        });
    });


$(function(){

    var wheight = $(window).height();
    var headhei = $('.header').height();
    $('.layout-header.header .tabs.gtxnotmob.mhtCatalog').css('height', wheight - headhei);
})