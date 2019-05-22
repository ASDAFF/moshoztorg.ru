<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="cart_success_page">
	<div class="cart_success">
	    <div class="order_info">
			<?
				if (!empty($arResult["ORDER"]))
				{
					$arBasket = array();
					$ob = CSaleBasket::GetList(Array(),Array("ORDER_ID"=>$arResult["ORDER"]["ID"]));
					while($ar = $ob->GetNext()){
						//var_dump($ar);
						$arProduct = CCatalogProduct::GetByIDEx($ar['PRODUCT_ID']);
						$arBasket[] = array(
							'ID' => $ar['PRODUCT_ID'],
							'NAME' => $ar['NAME'],
							'TARIFF' => $arProduct['PROPERTIES']['ADMITAD_TARIFF']['VALUE'], // тариф
							'SKU' => $arProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'], // Артикул.
							'CATEGORY' => $arProduct['IBLOCK_NAME'], // Возьмём по имени инфоблока
							'IBLOCK_ID' => $arProduct['IBLOCK_ID'], // Возьмём по имени инфоблока
							'PRICE' => $ar['PRICE'],
							'QUANTITY' => intval($ar['QUANTITY']),
							'URL' => $arProduct["DETAIL_PAGE_URL"]
						);
					}
					?>
						<h3>Ваш заказ оформлен</h1>
						<div class="detail">
							<p>Спасибо за заказ! В ближайшее время наш менеджер свяжется с вами и уточнит детали.</p>
							<p>Обращаем ваше внимание, что обработка заказов осуществляется ежедневно с 9:00 до 21:00.</p>
							<p class="separator-top"><span class="b">Номер заказа:</span> <?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></p>
							<p class="separator-top"><span class="b smallheading">Товары в заказе:</span></p>
							<ul>
								<?foreach($arBasket as $product){?>
									<li><a href="<?=$product["URL"]?>"><?=$product["NAME"]?></a></li>
								<?}?>
							</ul>
							<p class="separator-top"><span class="b smallheading">Итого:  <?=number_format($arResult["ORDER"]["PRICE"],0,'.',' ')?> руб.</span></p>
							<p class="note">По любым вопросам вы можете связаться с нами по телефону:</p>
							<a class="phone" href="tel:+78005504747"><span class="phone_code">8 (800) </span>550-47-47 </a>
						</div>
						<!--
				        <h1>Оформление заказа</h1>
				        <p class="h2">Ваш заказ сформирован</p>
				        <p>Ваш заказ №<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?> от <?=$arResult["ORDER"]["DATE_INSERT"]?> успешно создан.</p>
				        <p>Вы можете следить за выполением своего заказа в <a href="/personal/order/">персональном разделе</a> сайта.</p>
				        <p>Обратите внимание, что для просмотра этого раздела вам необходимо <a href="/personal/auth/">авторизоваться</a>.</p>
				        <?
				        	if(strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0){
				        		if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y"){
									?>
										<script language="JavaScript">
											window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
										</script>
										<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
									<?
									if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
									{
										?><br />
										<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
										<?
									}
				        		}
								else
								{
									if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
									{
										include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
									}
								}
				        	}
				        ?>
						-->
					<script>
						ga('require', 'ecommerce', 'ecommerce.js');
						ga('set', 'dimension1', 'basket');

						ga('ecommerce:addTransaction', {
						  'id': '<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>', 
						  'revenue': '<?=$arResult["ORDER"]["PRICE"]?>', 
						  'shipping': '<?=$arResult["ORDER"]["PRICE_DELIVERY"]?>', 
						  'tax': '<?=$arResult["ORDER"]["TAX_VALUE"]?>' 
						});

						<? foreach ($arBasket as $key => $arItem) {
							$addItem = array(
								"id" => $arResult["ORDER"]["ACCOUNT_NUMBER"] ,
								"name" => $arItem["NAME"],
								"sku" => $arItem["ID"],
								"category" => $arItem["CATEGORY"],
								"price" => $arItem["PRICE"],
								"quantity" => $arItem["QUANTITY"]
							);
							?>
							ga('ecommerce:addItem', <?= json_encode($addItem) ?>);
						<? } ?>

						ga('ecommerce:send');
						ga('ecommerce:clear');
						ga('send', 'event', 'ecommerce', 'buy', 'basket');
					</script>
					
					

<script type="text/javascript">

ADMITAD = window.ADMITAD || {};

ADMITAD.Invoice = ADMITAD.Invoice || {};

ADMITAD.Invoice.broker = "adm";     // параметр дедупликации (по умолчанию для admitad)

ADMITAD.Invoice.category = "1";     // код целевого действия (определяется при интеграции)

 
var orderedItem = [];               // временный массив для товарных позиций

 
// повторить для каждой товарной позиции в корзине

<?
		foreach ($arBasket as $key => $arItem) {

			if ($arItem["TARIFF"]) {
				$tariff = getPropertyEnumValueById("ADMITAD_TARIFF", $arItem["TARIFF"], $arItem["IBLOCK_ID"]);				
			} else {				
				$tariff = 1;				
			}
			
		?>

orderedItem.push({

  Product: {

      productID: '<?=$arItem["ID"]?>', // внутренний код продукта (не более 100 символов, соответствует ID из товарного фида).

      category: '<?=$tariff?>',               // код тарифа (определяется при интеграции)

      price: '<?=$arItem["PRICE"]?>',          // цена товара

      priceCurrency: "RUB",        // код валюты ISO-4217 alfa-3

  },

  orderQuantity: '<?=$arItem["QUANTITY"]?>',   // количество товара

  additionalType: "sale"           // всегда sale

});
 
<? } ?>


ADMITAD.Invoice.referencesOrder = ADMITAD.Invoice.referencesOrder || [];
// добавление товарных позиций к заказу

ADMITAD.Invoice.referencesOrder.push({

  orderNumber: "<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>", // внутренний номер заказа (не более 100 символов)

  orderedItem: orderedItem

});

 
// Важно! Если данные по заказу admitad подгружаются через AJAX раскомментируйте следующую строку.

// ADMITAD.Tracking.processPositions();

</script>






					<script type="text/javascript">/*
						(function (d, w) {
							w._admitadPixel = {
								response_type: 'img',     // 'script' or 'img'. Default: 'img'
								action_code: '1',
								campaign_code: 'b4b564561f'
							};
							w._admitadPositions = w._admitadPositions || [];
							<?
							foreach ($arBasket as $key => $arItem) {
								
								if ($arItem["TARIFF"]) {
									$tariff = getPropertyEnumValueById("ADMITAD_TARIFF", $arItem["TARIFF"], $arItem["IBLOCK_ID"]);				
								} else {				
									$tariff = 1;				
								}
							?>
							w._admitadPositions.push({
								uid: '<?=get_admitad_uid()?>',
								tariff_code: '<?=$tariff?>',
								order_id: '<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>',
								position_id: '<?=$key+1?>',
								currency_code: 'RUB',
								position_count: '<?=count($arBasket)?>',
								price: '<?=$arItem["PRICE"]?>',
								quantity: '<?=$arItem["QUANTITY"]?>',
								product_id: '<?=$arItem["ID"]?>',
								payment_type: 'sale'

							});
							<? } ?>
							var id = '_admitad-pixel';
							if (d.getElementById(id)) { return; }
							var s = d.createElement('script');
							s.id = id;
							var r = (new Date).getTime();
							var protocol = (d.location.protocol === 'https:' ? 'https:' : 'http:');
							s.src = protocol + '//cdn.asbmit.com/static/js/npixel.js?r=' + r;
							var head = d.getElementsByTagName('head')[0];
							head.appendChild(s);
						})(document, window)*/
					</script>

					<noscript>
						<img src="//ad.admitad.com/r?campaign_code=b4b564561f&action_code=1&payment_type=sale&response_type=img&uid=&tariff_code=&order_id=&position_id=&currency_code=&position_count=&price=&quantity=&product_id=" width="1" height="1" alt="">
					</noscript>



					<? // Для Метрики

						$arYaParams = array(
							'order_id' => $arResult["ORDER"]["ACCOUNT_NUMBER"], //номер заказа
							'order_price' => floatval($arResult["ORDER"]["PRICE"]), //сумма заказа
							'goods' => array()
						);
						foreach ($arBasket as $key => $arItem) {
							$arYaParams['goods'][] = array(
								'id' => $arItem["ID"],
								'name' => $arItem["NAME"],
								'price' => floatval($arItem['PRICE']),
								'quantity' => $arItem['QUANTITY'] 
							);
						}
					?>

					<script>
						// Сама метрика загружается позже, поэтому ждём, пока она не появится
						var metrikaIntervalId = 0;
						var sentEcomMetrika = false;
						var metrikaID = '26064957';
						var counterMetrika = 0;
						function sendEcomMetrika(){ 
							if (window['yaCounter'+metrikaID]){
								var yaParams = <?= json_encode($arYaParams)?>;
								window['yaCounter'+metrikaID].reachGoal('order_done_mht', yaParams);
								sentEcomMetrika = true;
								clearInterval(metrikaIntervalId);
							}
							counterMetrika++;
							if (counterMetrika>=10){
							clearInterval(metrikaIntervalId);	
							}
							return true;
						}
						metrikaIntervalId = setInterval(sendEcomMetrika, 300);
					</script>



				<?}else{?>
				        <h1><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></h1>
				        <?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
						<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
				<?}?>
		</div>
	</div>
</div>