$(function(){

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
					total = 0;
					$.each(this.dataGetters, function(i, dataGetter){
						var data = dataGetter(),
							price = data.price;
						total += price;
					});
					$total.html(formatPrice(total));
				}
			};


		function formatPrice(value){
			return value;
		}

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

			function updateTotalPrice(){
				totalPrice = price * parseInt($amount.val());
				$total.html(formatPrice(totalPrice));
				list.setTotalPrice();
			}

			$amount.spinner({
				min:1,
				step:1,
				icons: {
					down: "ui-icon-minus",
					up: "ui-icon-plus"
				},
				stop : function(){
					$.post('/ajax.php', {
						action : 'basket-set-amount',
						id : id,
						amount : $amount.val()
					});
					updateTotalPrice();
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
				})
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
});