<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	// Если мы зарегестрированы, заказ подтверждён и нужен ридирект - редиректим.
	if(
		(
			$USER->IsAuthorized() ||
			$arParams["ALLOW_AUTO_REGISTER"] == "Y"
		) && (
			$arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" ||
			$arResult["NEED_REDIRECT"] == "Y"
		) && (
			strlen($arResult["REDIRECT_URL"]) > 0
		)
	){
		$APPLICATION->RestartBuffer();
		?>
			<script type="text/javascript">
				window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
		<?
		die();
	}

	CJSCore::Init(array(
		'fx',
		'popup',
		'window',
		'ajax'
	));

	?>
		<noscript>
			Для работы формы заказа требуется <a href="http://www.enable-javascript.com/ru/">включить JavaScript</a>.
		</noscript>
	<?

	if(!function_exists("getColumnName")){
		function getColumnName($arHeader){
			return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_".$arHeader["id"]);
		}
	}

	if(!function_exists("cmpBySort")){
		function cmpBySort($array1, $array2){
			if (!isset($array1["SORT"]) || !isset($array2["SORT"])){
				return -1;
			}
			if ($array1["SORT"] > $array2["SORT"]){
				return 1;
			}
			if ($array1["SORT"] < $array2["SORT"]){
				return -1;
			}
			if ($array1["SORT"] == $array2["SORT"]){
				return 0;
			}
		}
	}

	if(
		!$USER->IsAuthorized() &&
		$arParams["ALLOW_AUTO_REGISTER"] == "N"
	){
		if(!empty($arResult["ERROR"])){
			foreach($arResult["ERROR"] as $v){
				echo ShowError($v);
			}
		}
		elseif(!empty($arResult["OK_MESSAGE"])){
			foreach($arResult["OK_MESSAGE"] as $v){
				echo ShowNote($v);
			}
		}

		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php"); // хз что это такое блять
		return;
	}

	if(
		$arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" ||
		$arResult["NEED_REDIRECT"] == "Y"
	){
		if(strlen($arResult["REDIRECT_URL"]) == 0){
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
		}
		return;
	}

	?>
		<script type="text/javascript">
			function submitForm(val){
				if(val != 'Y'){
					BX('confirmorder').value = 'N';
				}

				var orderForm = BX('ORDER_FORM');
				BX.showWait();
				BX.ajax.submit(orderForm, ajaxResult);

				return true;
			}

			function ajaxResult(res){
				try{
					var json = JSON.parse(res);
					BX.closeWait();

					if(json.error){
						return;
					}

					if(json.redirect){
						window.top.location.href = json.redirect;
					}
				}
				catch(e){
					BX('order_form_content').innerHTML = res;
				}

				BX.closeWait();
			}

			function SetContact(profileId){
				BX("profile_change").value = "Y";
				submitForm();
			}
		</script>



	


<div class="checkout_page">
	<div class="checkout">
	    <h1>Оформление заказа</h1>
	    <div class="checkout_list">

			<?

			if($_POST["is_ajax_post"] == "Y"){
				$APPLICATION->RestartBuffer();
			}
			else{
				?>
					<form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
						<?=bitrix_sessid_post()?>
						<div id="order_form_content">
				<?
			}

			if(
				!empty($arResult["ERROR"]) &&
				$arResult["USER_VALS"]["FINAL_STEP"] == "Y"
			){
				foreach($arResult["ERROR"] as $v){
					echo ShowError($v);
				}

				?>
					<script type="text/javascript">
						top.BX.scrollToNode(top.BX('ORDER_FORM'));
					</script>
				<?
			}

			?>
				<?/*
			        <div class="row">
			        	<div class="col buyer_info_block">
							<div class="row">
			                    <div class="col">
			                    	<label for="ORDER_PROP_1">Название компании <span>*</span></label>
			                    	<input type="text" id="ORDER_PROP_1" name="ORDER_PROP_1" value="" size="40" maxlength="250">
			                    </div>
			                    <div class="col">
			                    	<label for="ORDER_PROP_2">Юридический адрес</label>
			                    	<input type="text" id="ORDER_PROP_2" name="ORDER_PROP_2" value="" size="40" maxlength="250">
			                    </div>
			                </div>
			                <div class="row">
			                    <div class="col">
			                    	<label for="ORDER_PROP_3">ИНН</label>
			                    	<input type="tel" id="ORDER_PROP_3" name="ORDER_PROP_3" value="" size="8" maxlength="250">
			                    </div>
			                    <div class="col">
			                    	<label for="ORDER_PROP_4">КПП</label>
			                    	<input type="text" id="ORDER_PROP_4" name="ORDER_PROP_4" value="" size="8" maxlength="250">
			                    </div>
			                </div>
			                <div class="row">
			                    <div class="col">
			                    	<label for="ORDER_PROP_5_val">Контактное лицо <span>*</span></label>
			                    	<input type="text" id="ORDER_PROP_5_val" name="ORDER_PROP_5_val" value="" size="40">
			                    </div>
			                    <div class="col">
			                    	<label for="ORDER_PROP_6">E-Mail <span>*</span></label>
			                    	<input type="email" id="ORDER_PROP_6" name="ORDER_PROP_6" value="" size="40">
			                    </div>
			                </div>
			                <div class="row">
			                    <div class="col">
			                    	<label for="ORDER_PROP_7_val">Телефон</label>
			                    	<input type="tel" id="ORDER_PROP_7_val" name="ORDER_PROP_7_val" value="" size="40">
			                    </div>
			                    <div class="col">
			                    	<label for="ORDER_PROP_8">Факс</label>
			                    	<input type="text" id="ORDER_PROP_8" name="ORDER_PROP_8" value="" size="40">
			                    </div>
			                </div>
			                <div class="row">
			                    <div class="col">
			                    	<label for="ORDER_PROP_9_val">Индекс</label>
			                    	<input type="text" id="ORDER_PROP_9_val" name="ORDER_PROP_9_val" value="" size="40">
			                    </div>
			                    <div class="col">
			                    	<label for="ORDER_PROP_10">Местоположение</label>
			                    	<input type="text" id="ORDER_PROP_10" name="ORDER_PROP_10" value="" size="40">
			                    </div>
			                </div>
			                <div class="row">
			                    <div class="col">
			                    	<label for="ORDER_PROP_11_val">Адрес доставки</label>
			                    	<input type="text" id="ORDER_PROP_11_val" name="ORDER_PROP_11_val" value="" size="40">
			                    </div>
			                </div>
			            </div>
			        </div>
			        */?>

			        <div class="row">
			        	<div class="col delivery_block">
			               <div class="radio_blocks">
			               	<div class="radio_block active">
			                	<div class="radio"><input type="radio" checked="checked" value="1" name="DELIVERY_ID" id="ID_DELIVERY_ID_1"><label for="ID_DELIVERY_ID_1">Доставка курьером</label></div>
			                    <div class="description">Доставка осуществляется в течение дня в удобное для вас время.</div>
			                    <div class="price_block"><span class="price_value">500</span> <span class="rub"><span>рублей</span></span></div>
			                </div><!--
			                --><div class="radio_block">
			                	<div class="radio"><input type="radio" value="2" name="DELIVERY_ID" id="ID_DELIVERY_ID_2"><label for="ID_DELIVERY_ID_2">Самовывоз</label></div>
			                    <div class="description">Вы можете самостоятельно забрать заказ из нашего магазина.</div>
			                    <div class="price_block">Бесплатно</div>
			                </div>
			               </div>
			            </div>
			        </div>
			        <div class="row title">
			        	<div class="col">
			               <h4>Платежная система</h4>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="col pay_block">
			               <div class="radio_blocks">
			               	<div class="radio_block active">
			                	<div class="radio"><input type="radio" checked="checked" value="1" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID__1"><label for="ID_PAY_SYSTEM_ID__1">Банковский перевод</label></div>
			                    <div class="description">Будет распечатан счет на оплату</div>
			                </div><!--
			                --><div class="radio_block">
			                	<div class="radio"><input type="radio" value="4" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID_4"><label for="ID_PAY_SYSTEM_ID_4">Наложенный платеж</label></div>
			                    <div class="description">Оплата с помощью наложенного платежа</div>
			                </div>
			               </div>
			            </div>
			        </div>
			        <div class="row title">
			        	<div class="col">
			               <h4>Платежная система</h4>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="col pay_block">
			               <div class="radio_blocks">
			               	<div class="radio_block active">
			                	<div class="radio"><input type="radio" checked="checked" value="1" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID_1"><label for="ID_PAY_SYSTEM_ID_1">Наличными курьеру</label></div>
			                    <div class="description">Оплата наличными при получении заказа курьеру.</div>
			                </div><!--
			                --><div class="radio_block">
			                	<div class="radio"><input type="radio" value="4" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID__4"><label for="ID_PAY_SYSTEM_ID__4">Банковские карты</label></div>
			                    <div class="description">Оплата с помощью наложенного платежа</div>
			                </div><!--
			                --><div class="radio_block">
			                	<div class="radio"><input type="radio" value="2" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID_2"><label for="ID_PAY_SYSTEM_ID_2">Наложенный платеж</label></div>
			                    <div class="description">Оплата с помощью наложенного платежа</div>
			                </div><!--
			                --><div class="radio_block">
			                	<div class="radio"><input type="radio" value="3" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID_3"><label for="ID_PAY_SYSTEM_ID_3">Яндекс.Деньги</label></div>
			                    <div class="description">Оплата с помощью наложенного платежа</div>
			                </div><!--
			                --><div class="radio_block">
			                	<div class="radio"><input type="radio" value="5" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID_5"><label for="ID_PAY_SYSTEM_ID_5">Терминалы</label></div>
			                    <div class="description">Оплата с помощью наложенного платежа</div>
			                </div><!--
			                --><div class="radio_block">
			                	<div class="radio"><input type="radio" value="7" name="PAY_SYSTEM_ID" id="ID_PAY_SYSTEM_ID_7"><label for="ID_PAY_SYSTEM_ID_7">Сбербанк</label></div>
			                    <div class="description">Вы можете оплатить заказ в любом отделении Сбербанка.</div>
			                    <div class="price_block">3-7% / До 10 дней</div>
			                </div>
			               </div>
			            </div>
			        </div>
			        <div class="row title">
			        	<div class="col">
			               <h4>Товары в заказе</h4>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="col product_name_block">
			            	<a class="zoom image_zoom" href="img/checkout/product1.jpg"><img alt="" src="/local/templates/mht/img/checkout/product_mini1.jpg"></a><a href="#" class="product_name">Dyson DC 52 animal complete</a>
			            </div><!--
			            --><div class="col one_price_block">
			            	<div class="product_price"><span class="product_price_value">56 000</span> <span class="rub"><span>рублей</span></span></div>
			            </div><!--
			            --><div class="col count_selector_block">
			            	<span class="unit">3 шт</span>
			            </div><!--
			            --><div class="col all_price_block">
			            	<div class="product_price"><span class="product_price_value">168 000</span> <span class="rub"><span>рублей</span></span></div>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="col product_name_block">
			            	<a class="zoom image_zoom" href="img/checkout/product2.jpg"><img alt="" src="/local/templates/mht/img/checkout/product_mini2.jpg"></a><a href="#" class="product_name">HAMMER RNK1200</a>
			            </div><!--
			            --><div class="col one_price_block">
			            	<div class="product_price"><span class="product_price_value">42 000</span> <span class="rub"><span>рублей</span></span></div>
			            </div><!--
			            --><div class="col count_selector_block">
			            	<span class="unit">4 шт</span>
			            </div><!--
			            --><div class="col all_price_block">
			            	<div class="product_price"><span class="product_price_value">168 000</span> <span class="rub"><span>рублей</span></span></div>
			            </div>
			        </div>
			        <div class="row title comment">
			        	<div class="col">
			               <h4>Комментарии к заказу</h4>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="col comment_block">
			            	<textarea></textarea>
			            </div>
			        </div>
			        <div class="row total_price">
			        	<div class="col total_price_block">
			                <div class="delivery_price">Итого с доставкой (<span class="product_price_value">500</span> <span class="rub"><span>рублей</span></span>)</div>
			                <div class="product_price"><span class="product_price_value">336 000</span> <span class="rub"><span>рублей</span></span></div>
			            </div><!--
			            --><div class="col next_block">
			            	<a class="next" href="#">Оформить заказ</a>
			            </div>
			        </div>
			<?
			$files = array(
				0 => 'person_type',
				1 => 'props',
				2 => 'delivery',
				3 => 'paysystem',
				4 => 'related_props',
				5 => 'summary',
			);

			if($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d"){
				$files[2] = 'paysystem';
				$files[3] = 'delivery';
			}

			foreach($files as $name){
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/$name.php");
			}

			if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0){
				echo $arResult["PREPAY_ADIT_FIELDS"];
			}

			if($_POST["is_ajax_post"] == "Y"){
				?>
					<script type="text/javascript">
						top.BX('confirmorder').value = 'Y';
						top.BX('profile_change').value = 'N';
					</script>
				<?
				die();
			}

			?>
						</div>
					<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
					<input type="hidden" name="profile_change" id="profile_change" value="N">
					<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
					<input type="hidden" name="json" value="Y">
					<div class="bx_ordercart_order_pay_center"><a href="javascript:void();" onclick="submitForm('Y'); return false;" class="checkout"><?=GetMessage("SOA_TEMPL_BUTTON")?></a></div>
				</form>
			<?

			if($arParams["DELIVERY_NO_AJAX"] == "N"){
				?>
					<div style="display:none;">
						<?$APPLICATION->IncludeComponent(
							"bitrix:sale.ajax.delivery.calculator",
							"",
							array(),
							null,
							array(
								'HIDE_ICONS' => 'Y'
							)
						); ?>
					</div>
				<?
			}
		?>
		</div>
	</div>
</div>
