//(123456789.12345).formatMoney(2, '.', ',');
Number.prototype.formatMoney = function(c, d, t){
	var n = this,
		c = isNaN(c = Math.abs(c)) ? 2 : c,
		d = d == undefined ? "." : d,
		t = t == undefined ? "," : t,
		s = n < 0 ? "-" : "",
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
		j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};



$(document).ready(function(){
	
	$.smartbanner({
	  title: 'MosHozTorg',
	  author: 'MosHozTorg'
	});


	/* Слайдер буковок в аккордеоне */
	$('.pagesletterswrap').slick({
		dots: false,
		infinite: true,
		prevArrow: $('.pagesleft'),
		nextArrow: $('.pagesright'),
		speed: 300,
		slidesToShow: 8,
		slidesToScroll: 8,
		responsive: [
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 8,
					slidesToScroll: 8
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 6,
					slidesToScroll: 6
				}
			}
		]
	});
	
	/* Слайдер популярные на главной */
	
	$('.popularitems .itemscarousel').slick({
		dots: false,
		infinite: true,
		prevArrow: $('.poparrowleft'),
		nextArrow: $('.poparrowright'),
		speed: 300,
		slidesToShow: 1,
		slidesToScroll: 1
	});
	
	/* Слайдер рекомендуемых на главной */
	
	$('.recomendeditems .itemscarousel').slick({
		dots: false,
		infinite: true,
		prevArrow: $('.recarrowleft'),
		nextArrow: $('.recarrowright'),
		speed: 300,
		slidesToShow: 1,
		slidesToScroll: 1
	});
	
	/* Слайдер популярных брендов на главной */
	
	$('.popularbrands .itemscarousel').slick({
		dots: false,
		infinite: true,
		prevArrow: $('.popbarrowleft'),
		nextArrow: $('.popbarrowright'),
		speed: 300,
		slidesToShow: 1,
		slidesToScroll: 1
	});
	
	/* Слайдер похожих товаров на деталке */
	
	$('.catalogitempoh .itemscarousel').slick({
		dots: false,
		infinite: true,
		prevArrow: $('.poharrowleft'),
		nextArrow: $('.poharrowright'),
		speed: 300,
		slidesToShow: 1,
		slidesToScroll: 1
	});
	
	/* Слайдер просмотренных товаров в корзине*/
	
	$('.seenitems .itemscarousel').slick({
		dots: false,
		infinite: true,
		prevArrow: $('.seearrowleft'),
		nextArrow: $('.seearrowright'),
		speed: 300,
		slidesToShow: 1,
		slidesToScroll: 1
	});
	
	/* Слайдер магазинов на странице магазины */
	
	$('.shopslist .itemscarousel').slick({
		dots: false,
		infinite: true,
		prevArrow: $('.shoparrowleft'),
		nextArrow: $('.shoparrowright'),
		speed: 300,
		slidesToShow: 1,
		slidesToScroll: 1
	});
	
	/* Слайдер с картинками на главной */
	
	$('.mainpageslider').bxSlider();
	
	/* Аккордеон на главной */

	$('.accorditemheading').bind('click', function(){
		
		$('.accorditem > ul').each(function(){
			$(this).slideUp(300);
		});
		$(this).parents('.accorditem').toggleClass('opened');
		if($(this).parents('.accorditem').hasClass('opened')){
			$(this).parents('.accorditem').children('ul').slideDown(300);
			$('.accorditem').each(function(){$(this).removeClass('opened')});
			$(this).parents('.accorditem').addClass('opened');
			$('.accorditem a i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
			$(this).children('i').removeClass('flaticon-right');
			$(this).children('i').addClass('flaticon-bottom');
		} else {
			$('.accorditem').each(function(){$(this).removeClass('opened')});
			$('.accorditem a i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
		};
		$('.accorditem ul li').each(function(){$(this).removeClass('opened')});
		$('.accorditem ul li i').each(function(){
			$(this).removeClass('flaticon-bottom');
			$(this).addClass('flaticon-right');
		});
		var $object = $(this);
		setTimeout(function () {
		var offtop = $object.offset().top;
		$('html, body').animate({
			scrollTop: offtop
		}, 300);
		}, 300);
	
	});
	
	/* Дочерние пункты аккордеона на главной */
	
	$('.accord_cats ul li p').bind('click', function(){
		$('.accord_cats ul li ul').each(function(){$(this).slideUp(300)});
		$(this).parents('li').toggleClass('opened');
		if($(this).parents('li').hasClass('opened')){
			$(this).parents('li').children('ul').slideDown(300);
			$('.accord_cats ul li').each(function(){$(this).removeClass('opened')});
			$(this).parents('li').addClass('opened');
			$('.accord_cats ul li i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
			$(this).parents('li').children('i').removeClass('flaticon-right');
			$(this).parents('li').children('i').addClass('flaticon-bottom');
		} else {
			$('.accord_cats ul li').each(function(){$(this).removeClass('opened')});
			$('.accord_cats ul li i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
		};

	});
	
	/* Нажатие по буковкам в аккордеоне */
	
	$('.letterlink').bind('click', function(){
		var letter = $(this).text();
		$('.letterlink').each(function(){$(this).removeClass('active')});
		$(this).addClass('active');
		$(this).parents('li').children('.lettersdatacontent').children('.lettersdatacontentitem').each(function(){
			$(this).removeClass('shown');
		})
		$(this).parents('li').children('.lettersdatacontent').children('.lettersdatacontentitem[data-letter=' + letter + ']').addClass('shown');
	})
	
	/* Переинициализация слайдера с буковками в аккордеоне */
	
	$('.accorditemheading').bind('click', function(){
		if ($(this).parents('.accorditem').hasClass('accord_brands')){
			$('.pagesletterswrap').slick("unslick");
			$('.pagesletterswrap').slick({
				dots: false,
				infinite: false,
				prevArrow: $('.pagesleft'),
				nextArrow: $('.pagesright'),
				speed: 300,
				slidesToShow: 8,
				slidesToScroll: 8,
				responsive: [
					{
						breakpoint: 600,
						settings: {
							slidesToShow: 8,
							slidesToScroll: 8
						}
					},
					{
						breakpoint: 480,
						settings: {
							slidesToShow: 6,
							slidesToScroll: 6
						}
					}
				]
			});
		}
	}).each(function(){
		if ($(this).hasClass('opened')) $(this).trigger('click');
	});

	/* Демо кнопка авторизации УДАЛИТЬ ПОСЛЕ ПОСАДКИ */
	
	$('.demoauto').bind('click', function(){
		$('header').toggleClass('autorized');
	});
	
	/* Открытие меню для авторизованных */
	
	$('.cabinetbutton p').bind('click', function(){
		console.log('show cabinet menu');
		/*
		$('.autorizedmenu').toggle();
		$('.cabinetbutton p').toggleClass('toggled');
		*/ 
	});
	
	/* Закрытие меню для авторизованных */
	
	$('.closepopup').bind('click', function(){
		$('.autorizedmenu').hide();
		$('.cabinetbutton p').removeClass('toggled');
	});
	$(function($){
		$(document).mouseup(function (e){ 
			var div = $(".autorizedmenu");
			var div2 = $(".cabinetbutton p");
			if (!div.is(e.target) && !div2.is(e.target) && div.has(e.target).length === 0 && div2.has(e.target).length === 0) { 
				div.hide();
				$('.cabinetbutton p').removeClass('toggled');
			}
		});
	});
	
	/* Мобильное меню - аккордеон */
	
	$('.mainmobilemenu > ul > li > a').bind('click', function(){
		$('.mainmobilemenu > ul > li > ul').each(function(){
			$(this).slideUp(300);
		});
		$(this).parents('li').toggleClass('opened');
		
		if($(this).parents('li').hasClass('opened')){
			$('.mainmobilemenu > ul > li').each(function(){$(this).removeClass('opened')});
			$(this).parents('li').addClass('opened');
			$(this).parents('li').children('ul').slideDown(300);
			$('.mainmobilemenu > ul > li a i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
			$(this).children('i').removeClass('flaticon-right');
			$(this).children('i').addClass('flaticon-bottom');
		} else {
			$('.mainmobilemenu > ul > li').each(function(){$(this).removeClass('opened')});
			$('.mainmobilemenu ul li a i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
		};
		$('.mainmobilemenu > ul > li > ul > li').each(function(){$(this).removeClass('opened')});
		$('.mainmobilemenu > ul > li > ul > li i').each(function(){
			$(this).removeClass('flaticon-bottom');
			$(this).addClass('flaticon-right');
		});
	});
	$('.mainmobilemenu > ul > li > ul > li p').bind('click', function(){
		$('.mainmobilemenu > ul > li > ul > li > ul').each(function(){$(this).slideUp(300)});
		$(this).parent('li').toggleClass('opened');
		if($(this).parent('li').hasClass('opened')){
			$(this).parent('li').children('ul').slideDown(300);
			$('.mainmobilemenu > ul > li > ul > li').each(function(){$(this).removeClass('opened')});
			$(this).parent('li').addClass('opened');
			$('.mainmobilemenu > ul > li > ul > li i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
			$(this).parent('li').children('i').removeClass('flaticon-right');
			$(this).parent('li').children('i').addClass('flaticon-bottom');
		} else {
			$('.mainmobilemenu > ul > li > ul > li').each(function(){$(this).removeClass('opened')});
			$('.mainmobilemenu > ul > li > ul > li i').each(function(){
				$(this).removeClass('flaticon-bottom');
				$(this).addClass('flaticon-right');
			});
		};

	});
	
	/* Выдвигание мобильного меню */
	
	$('.menubutton').bind('click', function(){
		$('.mainmobilemenu > ul > li ul').each(function(){$(this).slideUp()});
		$('.mainmobilemenu > ul li').each(function(){$(this).removeClass('opened')});
		$('.mainmobilemenu > ul i').each(function(){$(this).removeClass().addClass('flaticon-right')});
		$('.mainmobilemenu').toggleClass('toggled');
		$('header').toggleClass('slided');
		$('footer').toggleClass('slided');
		$('section').toggleClass('slided');
		$('body').toggleClass('overflow');
		$('.menubutton').toggleClass('toggled');
	});
	
	/* Выдвигание панельки регистрации */
	
	$('.cabinetbutton').bind('click', function(){
		if ($('header').hasClass('autorized')) {} else {
			$(this).toggleClass('clicked');	
			$('.registerheading').slideToggle();
			if($(this).hasClass('clicked')){
				$('#signin_form').slideDown();
				$('#signin-header-link').addClass('active');
			} else {
				$('#signin_form').slideUp();
				$('#signup_form').slideUp();
				$('#signin-header-link').removeClass('active');
				$('#signup-header-link').removeClass('active');
			}
		}
	});
	

	/* Рэйндж в фильтре */
	
	// Загоняем инпут в переменную
	var $range = $(".rangewrap input[type='text']");
	// Инициализируем в ней рэйндж
	var options = {
		type: "double",
		grid: false,
		min: parseInt($(".gtxfrom").data('min')),
		max: parseInt($(".gtxto").data('max')),
		from:parseInt($(".gtxfrom").data('from')),
		to:parseInt($(".gtxto").data('to'))
	};
	$range.ionRangeSlider(options);
	
	//console.log($(".gtxfrom").val());
	// Передаем значение с рейнджа в поля
	$range.on("change", function () {
		var rangefrom = $(this).data("from");
		var rangeto = $(this).data("to");
		$range.parents('.filterinput').find('.gtxfrom').val(rangefrom);
		$range.parents('.filterinput').find('.gtxto').val(rangeto);
	});
	// Закатываем значения слайдера в переменную
	var thisrange = $range.data("ionRangeSlider");
	// Передаем в слайдер значения из поля "от"
	$('.gtxfrom').on("input", function () {
		var textfrom = $(this).val();
		thisrange.update({
			from: textfrom
		});
	});
	// Передаем в слайдер значения из поля "до"
	$('.gtxto').on("input", function () {
		var textto = $(this).val();
		thisrange.update({
			to: textto
		});
	});
	
	/* Открывание - закрывание фильтра */
	
	$('.togglefilter').bind('click', function(){
		$(this).parent('.categoryfilterblock').toggleClass('bottomed');
		$(this).parent('.categoryfilterblock').children('.filterwrap').slideToggle();
	});
	
	/* Слайдер на деталке */
	
	$('.catalogitemslider').bxSlider({
		pagerCustom: '#catalogitemslider-pager'
	});
	
	/* Слайдер-пэйджер на деталке */
	
	$('#catalogitemslider-pager').slick({
		dots: false,
		vertical: true,
		verticalSwiping: true,
		infinite: false,
		prevArrow: $('.pagerarrowtop'),
		nextArrow: $('.pagerarrowbot'),
		speed: 300,
		slidesToShow: 4,
		slidesToScroll: 4
	});
	
	
	/* Ввод чисел */
	
	$('.addquantity').bind('click', function(){

		if ( $('.catalogitemquantitywrap').length>0 ) {
			var $input = $(this).parent('.catalogitemquantitywrap').children('input');
			var val = parseInt($input.val())+1;
			var $itemPriceWrapper = $input.closest('.catalogitem');

			$itemPriceWrapper.find('.product_cart').attr('data-amount',val);

		}else {
			var $input = $(this).parent('.cartitemquantitywrap').children('input');
			var val = parseInt($input.val()) + 1;
			var $itemPriceWrapper = $input.closest('.cartitem');
			setAmount($input.data('buy-id'),val);
		}
		$input.val(val);
		updateItemPrice( $itemPriceWrapper );

	});
	
	$('.minquantity').bind('click', function(){
		if ( $('.catalogitemquantitywrap').length>0 ) {
			var $input = $(this).parent('.catalogitemquantitywrap').children('input');
			var val = parseInt($input.val());
			var $itemPriceWrapper = $input.closest('.catalogitem');
			if (val > 1) {
				$itemPriceWrapper.find('.product_cart').attr('data-amount', val - 1);
			}

		}else {
			var $input = $(this).parent('.cartitemquantitywrap').children('input');
			var val = parseInt($input.val());
			var $itemPriceWrapper = $input.closest('.cartitem');
			if (val > 1) {
				setAmount($input.data('buy-id'),val-1);
			}
		}
		if (val > 1) {
			$input.val(val - 1);
		}
		updateItemPrice( $itemPriceWrapper );


	});

	$('.cartitemdelete').click(function(e){
		var $row = $(this).closest('.cartitem');
		var id = $row.find('input.amount-input').data('buy-id');
		e.preventDefault();
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
			updateTotalPrice();
		})
	}).css('cursor','pointer');


	function setAmount(id,val)
	{
		$.post('/ajax.php', {
			action : 'basket-set-amount',
			id : id,
			amount : val
		});
		updateTotalPrice();
	}

	function updateItemPrice( $item )
	{
		var $itemPriceWrapper = $item.find('.itemprice');
		var $input = $item.find('.amount-input');
		var itemPrice = parseInt($input.data('price'));
		var itemAmount = parseInt($input.val());
		//console.log('amount='+itemAmount);
		//console.log('itemPrice='+itemPrice);

		$itemPriceWrapper.text( (itemPrice*itemAmount).formatMoney(0, '.', ' ') );
	}

	function updateTotalPrice()
	{
		var $totalPriceWrapper = $('.cartitogprice');
		var resultPrice = 0;
		$('.cartitem').each(function(){
			var $input = $(this).find('.amount-input');
			var itemPrice = parseInt($input.data('price'));
			var itemAmount = parseInt($input.val());
			resultPrice += itemPrice*itemAmount;
		});

		$totalPriceWrapper.text( resultPrice.formatMoney(0, '.', ' ') );

	}
	


	$('#signin-header-link').off('click').on('click',function(){
		
		$('#signin_form').slideToggle();
		$('#signin-header-link').toggleClass('active');
		$('#signup_form').slideUp();
		$('#signup-header-link').removeClass('active');
		return false;
	});

	$('#signup-header-link').off('click').on('click',function(){
		$('#signup_form').slideToggle();
		$('#signup-header-link').toggleClass('active');
		$('#signin_form').slideUp();
		$('#signin-header-link').removeClass('active');
		return false;
	});



	$("#header_region").autocomplete({
		source: mht.regions,
		minLength: 0,
		select: function( event, ui ) {
			location.href = ui.item.value;
			return false;
		}
	}).click(
		function(){
			$(this).autocomplete("search", "");
		}
	);
	
	function callplease(){
		$('.callpleasewrap').fadeIn();
	}

	$('.closethis').bind('click', function(){
		$('.callpleasewrap').fadeOut();
	});



	//Добавление в корзину
	$(".product_cart").click(function(){ //.maket


		var $button = $(this);
		var titleBefore = $(this).text();
		$(this).animate({ opacity: '0.7' }, 300, function(){
			$(this).text('Добавлено  ...');
		});

		var $this = $(this),
			href = $this.attr('href');

		var amount = $(this).data('amount') || 1;

		var options = {
			'action':'ADD2BASKET',
			'ajax_basket':'Y',
			'id':$this.data('id'),
			'quantity':amount
		}


		mht.animateToBasket($this.closest(".product").find(".product_image_original"));
		$.post(href,options, function(){
			mht.updateBasket();
		})

		setTimeout(function(){
			$button.css({opacity:1}).text(titleBefore)
		},1000);
		/*
		 $.post('/ajax.php', {
		 action : 'basket-add',
		 id     : $this.attr('data-id')
		 }, function(){
		 mht.updateBasket();
		 });
		 */
		return false;
	});

	// $('.input_discont').keypress(function(){
	// 	if ( $('.submit-discount-button').length<=0 ) {
	// 	}
	// });

    $('a.submit-discount-button').on('click', function () {

        // var $context = $('.input_discont');
        // var sessid = $context.data('sessid');
        //
        // $.post('/ajax.php', {
        //     action: 'apply_discount',
        //     value: $context.val(),
        //     sessid:sessid
        // }, function (data) {
        //     if (data=='True'){
        //         document.location.reload();
        //     }else if (data=='False' || data=='Attempts'){
        //         $('#coupon-error').slideDown(700).remove();
        //         var sErrorText = "Неверный код дисконтной карты";
        //         if (data=='Attempts') sErrorText = "Превышен лимит попыток.";
        //
        //         var $errorSpan = $("<span>").attr("id","coupon-error").text(sErrorText);
        //         $('a.submit-discount-button').after( $errorSpan );
        //
        //     }
        // });
        return false;
    });

    $('.discount-card-info>a').on('click', function () {

        $('.registerheading').slideToggle();
        $('#signup-header-link').trigger('click');

        return false;

	});

	$('.agreement-wrapper a').on('click', function () {

        window.location.href = 'agreement.php';

        return false;

	});


	
});




	
