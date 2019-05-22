$(document).ready(function() {
$('.gtread').click(function(){
var el = $(this).attr('href');
$('body').animate({
scrollTop: $(el).offset().top}, 500);
return false;
});
});


$(document).ready(function() {

		
	if (getCookie('hidefloatmht') != 1) {
		$('.float-banner').addClass('active');
	}
	
	$('.closefloat').click(function(){
		setCookie ('hidefloatmht', 1, 86400, "/");
		$('.float-banner').fadeOut();
	});
	
	var productTabsBlock = $(".about .product .product_tabs");

	var tabLinks = productTabsBlock.find("ul.product_tabs_link li a");
	var tabBlocks = productTabsBlock.find(".product_tab");
	var sliderNav = $(".about .product .slider_nav, .about .description .slider_nav");

	sliderNav.each(function() {
		
		var readyToChange = true;

		var prev = $(this).find(".prev"); 
		var next = $(this).find(".next");
		var itemIndexCont = $(this).find(".current_index");
		var totalCountCont = $(this).find(".total");
		var tabBlock = $(this).parent();
		var itemsContainer = tabBlock.find(".slider_items_container");
		var items = itemsContainer.children();
		var itemsCount = items.length;
		var maxHeight = getMaxHeight(items);
		var totalWidth = getTotalWidth(items);
		var maxWidth = getMaxWidth(items);

		itemsContainer
			.css("position", "absolute")
			.css("height", maxHeight)
			.css("width", totalWidth)
			.css("left", 0)
			.css("bottom", 0);

		tabBlock
			.css("height", $(this).outerHeight(true) + maxHeight)
			.css("overflow", "hidden")
			.css("position", "relative");

		totalCountCont.text(itemsCount);
		itemIndexCont.text(1);

		prev.on("click", function() {
			if(!readyToChange) {
				return;
			}

			next.removeClass("unactive");
			var curLeftPosition = itemsContainer.position().left;
			if(curLeftPosition < 0) {
				itemsContainer.animate({
					left: curLeftPosition + maxWidth
				}, {
					start: function() {readyToChange = false;},
					done: function() {
						readyToChange = true;
						curItemIndex = itemsContainer.position().left*(-1)/maxWidth + 1
						itemIndexCont.text(curItemIndex);
					}
				});
			} else {
				prev.addClass("unactive");
			}
		});

		next.on("click", function() {
			if(!readyToChange) {
				return;
			}

			prev.removeClass("unactive");
			var curLeftPosition = itemsContainer.position().left;
			var tailWidth = totalWidth - curLeftPosition*(-1)
			if(tailWidth > tabBlock.innerWidth()) {
				itemsContainer.animate({
					left: curLeftPosition - maxWidth
				}, {
					start: function() {readyToChange = false;},
					done: function() {
						readyToChange = true;
						curItemIndex = itemsContainer.position().left*(-1)/maxWidth + 1
						itemIndexCont.text(curItemIndex);
					}
				});
			} else {
				next.addClass("unactive");
			}
		});

	});

	tabBlocks
		.hide()
		.eq(0)
		.show();

	tabLinks.on("click", function() {
		var index = $(this).parent().index();
		echo(index);
		tabBlocks
			.hide()
			.eq(index)
			.show();
		
		tabLinks
			.parent()
			.removeClass("active");
		$(this)
			.parent()
			.addClass("active");

		return false;
	});


	function getMaxHeight(jObjects) {
		var max = 0;
		jObjects.each(function() {
			max = $(this).outerHeight(true) > max ? $(this).outerHeight(true) : max;
		});
		return max;
	}
	
	function getMaxWidth(jObjects) {
		var max = 0;
		jObjects.each(function() {
			max = $(this).outerWidth(true) > max ? $(this).outerWidth(true) : max;
		});
		return max;
	}

	function getTotalWidth(jObjects) {
		var width = 0;
		jObjects.each(function() {
			echo($(this));
			echo($(this).outerWidth(true));
			width = $(this).outerWidth(true) + width;
		});
		return width;
	}

	function echo(item) {
		console.log(item);
	}

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



function setCookie (name, value, seconds, path, domain, secure) {

    var expires;
    if (seconds) {
        var date = new Date();
        date.setTime(date.getTime() + (seconds * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }

    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}


function getCookie(name) {
    var cookie = " " + document.cookie;
    var search = " " + name + "=";
    var setStr = null;
    var offset = 0;
    var end = 0;
    if (cookie.length > 0) {
        offset = cookie.indexOf(search);
        if (offset != -1) {
            offset += search.length;
            end = cookie.indexOf(";", offset)
            if (end == -1) {
                end = cookie.length;
            }
            setStr = unescape(cookie.substring(offset, end));
        }
    }
    return(setStr);
}


//для акций с таймером
$(document).ready(function() {
	// From http://learn.shayhowe.com/advanced-html-css/jquery

	// Change tab class and display content
	$('.stock-nav a').click(function(event) {
		event.preventDefault();
		$('.stock-active').removeClass('stock-active');
		$(this).parent().addClass('stock-active');
		$('.stock-stage>div').hide();
		$($(this).attr('href')).show();
	});

})