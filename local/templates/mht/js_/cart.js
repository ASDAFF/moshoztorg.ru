$(function(){

	function updateAllPrices(data){
		$('span.js-global-total-price').html( formatPrice(data.arBasket.TOTAL_PRICE) );
		$('div.discount-value>span.product_price_value').html( formatPrice(data.fullPrice) );

        data.arBasket.CATEGORIES.READY.forEach(function (item, i) {

        	var $row = $("div.row[data-id='"+ item.ID +"']");

        	$("span.js-price",$row).html(formatPrice(item.PRICE_FORMATED));
        	$("span.js-price-total",$row).html( formatPrice(item.SUM) );
        });

	}


    function formatPrice(value){

        value = ''+value;

        value = value.replace(/ руб./g,"");

        return value;
    }




	$('.discount_number_field').change(function(){
		$.post('/ajax.php', {
			action : 'apply_discount',
			value : $(this).val()
		}, function(result){
		});
	});

	$('.cart_list').each(function(){
		var $list = $(this),
			$rows = $list.children('.row'),
			$total = $list.find('.js-global-total-price'),

			list = {
				dataGetters : [],
				addDataGetter : function(dataGetter){
					this.dataGetters.push(dataGetter);
				},
				setTotalPrice : function(){
				}
			};




		$rows.each(function(){
			var $row = $(this);
			if($row.is('.total_price')){
				return;
			}
			var $amount = $row.find('input.count_selector'),
				$remove = $row.find('.remove_block'),
				$total = $row.find('.js-price-total'),
				price = Math.ceil($row.find('.js-price').attr('data-value')),
				totalPrice = price * parseInt($amount.val()),
				id = parseInt($row.attr('data-id')),
				$restore = $row.find('.restore_block');

			$amount.spinner({
				min:1,
				step:1,
				icons: {
					down: "ui-icon-minus",
					up: "ui-icon-plus"
				},
				start: function() {
					$amount.attr('oldValue', this.value );
				},
				stop: function() {
				   if (this.value != $amount.attr('oldValue') == '1') {
					$.post('/ajax.php', {
						action : 'basket-set-amount',
						id : id,
						amount : $amount.val()
					}).done(function(data) {
						updateAllPrices(data);
					});
				  }
				}
			});

			$remove.click(function(e){
				e.preventDefault();
				$row
					.find("div:not(.restore_block)")
					.animate({
						opacity : 0.3
					});
				$.post('/ajax.php', {
					action : 'basket-remove',
					id : id
				}, function(){
					/*$row.slideUp(function(){
						$row.detach();
					});*/
					totalPrice = 0;
					list.setTotalPrice();
					$remove.hide();
					$restore.show();
					$amount.prop("disabled", true);
					$amount.spinner("disable");
				}).done(function(data) {
					updateAllPrices(data);
				});
			});

			$restore.click(function(e) {
				e.preventDefault();
				
				var href = $restore.find("a.restore").attr('href');
				$.post(href, function(){
					mht.updateBasket();
					$row
						.find("div")
						.animate({
							opacity : 1
						});
					$amount.prop("disabled", false);
					$amount.val(1);
					$remove.show();
					$restore.hide();
					list.setTotalPrice();
					$amount.spinner("enable");
				})			
			});

			list.addDataGetter(function(){
				return {
					price : totalPrice
				};
			})
		});
	})


    $.post('/ajax.php', {
        action: 'basket-get',
    }, function () {
    }).done(function (data) {
        updateAllPrices(data);
    });

});