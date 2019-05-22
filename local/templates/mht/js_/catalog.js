$(function(){

	var timeoutSlider;


	// $('.filter_block select').selectmenu();
	$('.breadcrumbs.catalog').each(function(){
		var $h1 = $('h1');
		$h1.css({
			marginTop : parseFloat($h1.css('marginTop')) + $(this).height() - 20
		});
	});

	$('#change_per_page').selectmenu({
		change : function(){
			$.post('/ajax.php', {
				action : 'change-per-page',
				value : $(this).val()
			}, function(){
				location.reload();
			});
		}
	});
	$('#change_sort').selectmenu({
		change : function(){
			$.post('/ajax.php', {
				action : 'change-sort',
				value : $(this).val(),
				list: $(this).data('list-id')
			}, function(){
				location.reload();
			});
		}
	});
/*
	$(".catalog_menu li:has(ul)>a").click(function(e){
		var $li = $(this).closest("li"),
			active = $li.is('.active');
		e.preventDefault();
		$(this).closest("ul").find("li").removeClass("active");
		$li.toggleClass("active", !active);
	});
*/
	$('.filter-prices').each(function(){
		var $holder = $(this),
			$range = $holder.find('.cost_range'),
			$min = $holder.find('.cost-min'),
			$max = $holder.find('.cost-max'),
			disable = function(v){
				v = v ? 'disabled' : false
				$min.attr('disabled', v);
				$max.attr('disabled', v);
			};

		disable(true);
		$holder.click(function(){
			disable(false);
		});

		$range.slider({
            change: function( event, ui ) {

                clearTimeout(timeoutSlider);
                timeoutSlider = setTimeout(function(){

                	window.makeFilter();

				},3000);

			},
			range: true,
			min: parseInt($min.attr('data-sel-value')),
			max: parseInt($max.attr('data-sel-value')),
			values: [
				parseInt($min.val()),
				parseInt($max.val()),
			],
			slide: function( event, ui ) {
				$min.val(ui.values[0]).attr('value',ui.values[0]);
				$max.val(ui.values[1]).attr('value',ui.values[1]);
				//console.log(ui.values[0]);
				//console.log( ui.values[1] );
			}
		});
	});
    $('.filter-numbers').each(function(){
        var $holder = $(this),
            $range = $holder.find('.cost_range'),
            $min = $holder.find('.cost-min'),
            $max = $holder.find('.cost-max'),
            disable = function(v){
                v = v ? 'disabled' : false
                $min.attr('disabled', v);
                $max.attr('disabled', v);
            };

        disable(true);
        $holder.click(function(){
            disable(false);
        });

        $range.slider({
            range: true,
            step: 1,
            min: $min.attr('data-sel-value'),
            max: $max.attr('data-sel-value'),
            values: [
                $min.val(),
                $max.val(),
            ],
            slide: function( event, ui ) {
                $min.val(ui.values[0]);
                $max.val(ui.values[1]);
            }
        });
    });
	
	$(".filter_block .filter_block_middle").click(
		function(){
			$(".filter_block").toggleClass("full");
		}
	);

	(function($triggers){
		var $products = $(".products_block"),
			listId = $products.data('list-id') ? $products.data('list-id') : '',
			prev = {
				_index : 'catalogListTriggerValue' + listId,
				def : false,
				get : function(){
					/*if(localStorage && localStorage[this._index]){
						return (localStorage[this._index] === 'true' || localStorage[this._index] === true);
					}*/
					return this.def;
				},
				set : function(value){
					/*if(!localStorage){
						return;
					}
					localStorage[this._index] = value;*/
				},
				resize : function(){
					var callback = function(){
						var w = window.innerWidth;
						if(w < 400){
							prev.set(false);
							$products.toggleClass('block', false);
						}
						else if(w < 640){
							prev.set(true);
							$products.toggleClass('block', true);
						}

					};
					callback();
					$(window).resize(callback);
				}
			};

		prev.resize();

		$triggers.click(function(e){
			e.preventDefault();
			var $trigger = $(this),
				isBlock = $trigger.is('.block_group');
			$triggers.removeClass('active');
			$trigger.addClass('active');
			$products.toggleClass('block', isBlock);
			prev.set(isBlock);
			
			$.ajax({
				"url":"/ajax/setCatalogView.php",
				"data":{"VIEW":(isBlock?"block":"list")}
			});
			
			setTimeout(function(){
				mht.fit();
				mht.fitImages();
				mht.fit();
				mht.fitImages();
			}, 10);
		});

		if(prev.get()){
			$triggers.filter('.block_group').click();
		}
	})($('.js-change-catalog-view .js-trigger'));
});
/*
var catalogUpdater;

function filterCatalog(){
	clearTimeout(catalogUpdater);
	catalogUpdater = setTimeout(
		function(){
			$(".products_block .product").show();
			$target = $(".products_block");
			$elements = $(".products_block .product");
			switch($(".sort_block_list select").val()){
				case "reduced price":
					$elements.sort(function (a, b) {
						var contentA =parseInt( $(a).attr('data-price'));
						var contentB =parseInt( $(b).attr('data-price'));
						if(contentA == contentB) return 0;
						return (contentA > contentB) ? -1 : 1;
				   })
				break;
				case "price increase":
					$elements.sort(function (a, b) {
						var contentA =parseInt( $(a).attr('data-price'));
						var contentB =parseInt( $(b).attr('data-price'));
						if(contentA == contentB) return 0;
						return (contentA < contentB) ? -1 : 1;
				   })
				break;
				case "name":
					$elements.sort(function (a, b) {
						var contentA = $(a).attr('data-name').toUpperCase();
						var contentB = $(b).attr('data-name').toUpperCase();
						if(contentA == contentB) return 0;
						return (contentA < contentB) ? -1 : 1;
				   })
				break;
				case "popularity":
					$elements.sort(function (a, b) {
						var contentA =parseInt( $(a).attr('data-assessment'));
						var contentB =parseInt( $(b).attr('data-assessment'));
						if(contentA == contentB) return 0;
						return (contentA > contentB) ? -1 : 1;
				   })
				break;
			}
			$elements.detach().appendTo($target);
			
			brend = $(".filter_block_bottom select[name='brend']").val();
			if(brend != "all"){
				$(".products_block .product:not([data-brend='"+brend+"'])").hide();	
			}
			
			shop = $(".filter_block_bottom select[name='shop']").val();
			if(shop != "all"){
				$(".products_block .product:not([data-shop='"+shop+"'],[data-shop='all'])").hide();	
			}
			
			type = $(".filter_block_bottom select[name='type']").val();
			if(type != "all"){
				$(".products_block .product:not([data-type='"+type+"'])").hide();	
			}
			
			features = $(".filter_block_bottom select[name='features']").val();
			if(features != "all"){
				$(".products_block .product:not([data-features='"+features+"'])").hide();	
			}
			
			min_price = parseInt($("input[name='cost_min']").val());
			max_price = parseInt($("input[name='cost_max']").val());
			$(".products_block .product:visible").each(
				function(){
					val = parseInt($(this).attr("data-price"));	
					if(val<min_price || val>max_price){
						$(this).hide();	
					}
				}
			);
			
			$(".products_block .product:visible").removeClass("even four");
			i = 1;
			$(".products_block .product:visible").each(
				function(){
					if(i % 2 == 0){
						$(this).addClass("even");	
					}
					if(i % 4 == 0){
						$(this).addClass("four");	
					}
					i++;	
				}
			);
			$(".products_block .product:visible:gt("+($(".product_count_list select").val()-1)+")").hide();
		},
		100
	);
}

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
	$(".catalog_menu li:has(ul)>a").click(
		function(){
			$(this).closest("ul").find("li").removeClass("active");
			$(this).closest("li").addClass("active");
			return false;	
		}
	);
	$(".product_count_list select").selectmenu({
		change:function( event, ui ){
			filterCatalog();
		}
	});
	$(".sort_block_list select").selectmenu({
		change:function( event, ui ){
			filterCatalog();
		}
	});
	
	max_price = 0;
	min_price = parseInt($(".products_block .product:eq(0)").attr("data-price"));
	$(".products_block .product").each(
		function(){
			val = parseInt($(this).attr("data-price"));	
			if(max_price<val){
				max_price = val;	
			}
			if(min_price>val){
				min_price = val;	
			}
		}
	);
	
	$("input[name='cost_min']").val(min_price);
	$("input[name='cost_max']").val(max_price);
	$(".filter_block_bottom .cost_range").slider({
		range: true,
		min: min_price,
		max: max_price,
		values: [min_price,max_price],
		slide: function( event, ui ) {
			$("input[name='cost_min']").val(ui.values[ 0 ]);
			$("input[name='cost_max']").val(ui.values[ 1 ]);
			filterCatalog();
		}
	});	
	
	$(".filter_block_top .group_block .block_group").click(
		function(){
			$(".filter_block_top .group_block .col_group").removeClass("active");
			$(this).addClass("active");
			$(".products_block").addClass("block");
		}
	);
	
	$(".filter_block_top .group_block .col_group").click(
		function(){
			$(".filter_block_top .group_block .block_group").removeClass("active");
			$(this).addClass("active");
			$(".products_block").removeClass("block");
		}
	);
	
	$(".filter_block .filter_block_middle").click(
		function(){
			$(".filter_block").toggleClass("full");
		}
	);
	
	$(".filter_block_bottom select[name='features']").selectmenu({
		change:function( event, ui ){
			filterCatalog();
		}
	});
	$(".filter_block_bottom select[name='shop']").selectmenu({
		change:function( event, ui ){
			filterCatalog();
		}
	});
	$(".filter_block_bottom select[name='brend']").selectmenu({
		change:function( event, ui ){
			filterCatalog();
		}
	});
	$(".filter_block_bottom select[name='type']").selectmenu({
		change:function( event, ui ){
			filterCatalog();
		}
	});
	filterCatalog();
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
});*/



    function gtresize (){
    setTimeout(function(){
        $('.product_brand').each(function(){
            $('.product_brand').css('height','auto');
        });
        $('.product_description').each(function(){
            $('.product_description').css('height','auto');
        });
        var prodtitle = 0;
        var proddesc = 0;
        $(".gtwrap").replaceWith(function () { return $(this).html(); });
        var blockwidth = $('.products_block.js-fit').innerWidth();
		var itemwidth = $('.products_block.js-fit .product:first-child')[0].getBoundingClientRect().width + parseFloat($('.products_block.js-fit .product:first-child').css('margin-left')) + parseFloat($('.products_block.js-fit .product:first-child').css('margin-right'));
		
        var rowitemcountval = blockwidth / itemwidth;
        var rowitemcount = parseInt(rowitemcountval);   
        /*if (rowitemcount == 7) {
            rowitemcount = 5;
        } else if (rowitemcount == 5){
            rowitemcount = 4;
        }*/
        while($('.products_block.js-fit').children('div:not(.gtwrap):not(.bTileSeparator)').length){
            $('.products_block.js-fit').children('div:not(.gtwrap):not(.bTileSeparator):lt('+rowitemcount+')').wrapAll('<div class="gtwrap">');
        }
        $('.products_block.js-fit .gtwrap').each(function(){
            prodtitle = 0;
            $(this).find('.product_brand').each(function(){
                if ($(this).height() > prodtitle){
                    prodtitle = $(this).height();
                };
            });
            $(this).find('.product_brand').each(function(){
                $(this).css('height', prodtitle);
            });
            proddesc = 0;
            $(this).find('.product_description').each(function(){
                if ($(this).height() > proddesc){
                    proddesc = $(this).height();
                };
            });
            $(this).find('.product_description').each(function(){
                $(this).css('height', proddesc);
            });
        });
    }, 0);
};

$(document).ready(function(){
    gtresize();
});
$(window).resize(function(){
    gtresize();
});
$(document).ready(function(){
    $('.js-trigger a').bind('click',function(){
        gtresize();
    });
})

  