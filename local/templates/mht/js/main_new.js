setTimeout(function () {
    $('.loader').fadeOut(500);
    setTimeout(function () {
        $('html').removeClass('loading');
    }, 500);
}, 1000);

jQuery(function ($) {
    
    $('.header.fixedheader').width($(document).outerWidth());
    
    $(window).resize(function(){
       $('.header.fixedheader').width($(document).outerWidth()); 
    });
    
	$('.rememberme').styler();
	$('.agreement-input').styler();
	
	
	
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

        $('.gtx_secondlevel').removeClass('gtxnotmob');
    }
    else {

    
        // ===================================================== video bg
        //$('#video-bg').css({'visibility': 'visible'});
        //$('#video-bg')[0].play();
    }

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

		    var bad = false;
			$('.js-zoomy img').each(function(i,o){
				if(!o.complete){
					bad = true;
					return false;
				}
			});

			if(bad){
				setTimeout(function(){
                    mht.initZoomy();
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
                    images = [],
                    imagesSRC = $big.data('imgs').split(";"),
					enter = function(event){

    					entered = true;

						if ( !$(event.relatedTarget).hasClass('js-big-img') ||
                            !$(event.relatedTarget).hasClass('js-zoomy-pane')) {
                            $pane.fadeIn(200);
                        }


						imgSize = getSize($img);
						bigSize = getSize($big);
						paneSize = getSize($pane);
					};
                //imagesSRC.push($big.attr('src'));
                $.each(imagesSRC, function( index, value ) {
                    var imgT = {};
                    imgT.src = value;
                    images.push(imgT);
                });

				function arrowsplace(instance, slide){
					var slidewid = slide.$slide.find('img').width();
					var slidehei = slide.$slide.find('img').height();
					var winwid = $('.fancybox-bg').width();
					var leftpos = ((winwid - slidewid) / 2) - 45;
					var rightpos = ((winwid + slidewid) / 2) - 44;
					$('.fancybox-button--left').css('left', leftpos);
					$('.fancybox-button--right').css('left', rightpos);
				}

				function arrowsplacedelay(instance, slide){

					setTimeout(function(){
					var slidewid = slide.$slide.find('img').width();
					var slidehei = slide.$slide.find('img').height();
					var winwid = $('.fancybox-bg').width();
					var leftpos = ((winwid - slidewid) / 2) - 45;
					var rightpos = ((winwid + slidewid) / 2) - 44;
					$('.fancybox-button--left').animate({left: leftpos}, 100);
					$('.fancybox-button--right').animate({left: rightpos}, 100);
					}, 500);
				}

                $pane
                    .mousemove(function (e) {
                        $img.trigger(e);
                    })
                    .mouseleave(function (e) {
                        $img.trigger(e);
                    })
                    .click(function (e) {
                        $img.trigger(e);
                    });


				$img
					.off('click')
					.click(function(){
                        $.fancybox.open(images,{
                            thumbs : {
                                showOnStart : true
                            },
							onComplete: function(instance, slide){
								arrowsplace(instance, slide);
							},
							onZoom: function(instance, slide){
								arrowsplacedelay(instance, slide);
							},
                            baseTpl	: '<div class="fancybox-container" role="dialog" tabindex="-1">' +
                            '<div class="fancybox-bg"></div>' +
                            '<div class="fancybox-controls">' +
                            '<div class="fancybox-infobar">' +
                            '<div class="fancybox-infobar__body">' +
                            //'<span class="js-fancybox-index"></span>&nbsp;/&nbsp;<span class="js-fancybox-count"></span>' +
                            '</div>' +
                            '</div>' +
                            '<div class="fancybox-buttons">' +
                            '<button data-fancybox-close class="fancybox-button fancybox-button--close" title="Close (Esc)"></button>' +
                            '</div>' +
                            '</div>' +
                            '<div class="fancybox-slider-wrap">' +

                            '<button data-fancybox-previous class="fancybox-button fancybox-button--left" title="Previous"></button>' +
                            '<div class="fancybox-slider"></div>' +
                            '<button data-fancybox-next class="fancybox-button fancybox-button--right" title="Next"></button>' +

                            '</div>' +
                            '<div class="fancybox-caption-wrap"><div class="fancybox-caption"></div></div>' +
                            '</div>'
                        });
                        /*$.fancybox({
							href: $big.attr('src')
						});*/
					});

				if(Modernizr.touch){
					return;
				}

				$img
					.off('mouseenter')
					.off('mousemove')
					.off('mouseleave')
					.mouseenter(function(e){
						enter(e);
					})
					.mouseleave(function(event){
						entered = false;

                        if (!$(event.relatedTarget).hasClass('js-zoomy-pane') &&
                            !$(event.relatedTarget).hasClass('js-big-img')
                        ) {
                            $pane.fadeOut(200);
                        }


					})
					.mousemove(function(e){
						enter(e);
						var
							rel = {
								x : (e.pageX - imgOffset.left) / imgSize.width,
								y : (e.pageY - imgOffset.top) / imgSize.height
							},
							css = {
								left : -bigSize.width * rel.x + paneSize.width / 2,
								top : -bigSize.height * rel.y + paneSize.height / 2,
							};

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
					}, 500,
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

    // ===================================================== Styler
    
	$('input[type=radio]').change(function () {
		
	
        var label = $(this).closest('label'),
            name = $(this).attr('name');
        $('input[name=' + name + ']').closest('label').removeClass('checked');
        if ($(this).is(':checked'))
            label.addClass('checked');
		
		if (($(this).attr('id') == 'PERSON_TYPE_2') || ($(this).attr('id') == 'PERSON_TYPE_1') || ($(this).attr('id') == 'ID_DELIVERY_ID_2') || ($(this).attr('id') == 'ID_DELIVERY_ID_1')) {
			submitForm();
        }
		
		
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

$(function(){
    
    var wheight = $(window).height();
    var headhei = $('.header').height();
    $('.layout-header.header .tabs.gtxnotmob.mhtCatalog').css('height', wheight - headhei);
})