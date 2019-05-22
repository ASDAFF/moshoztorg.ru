<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    use Bitrix\Sale\DiscountCouponsManager;

	WP::loadScript('/js_/cart.js?v=1');
?>
<div class="cart_page">
<div class="cart">
    <h1>Корзина</h1>
	<? //dm($arResult); ?>
    <?
        //ставим сумму рассчитанную компонентом
        $total = $arResult['allSum'];
        $products = array();
		$arProducts = array();
        foreach($arResult['GRID']['ROWS'] as $element){
			if($element["DELAY"] != 'Y'){
				$product = MHT\Product::byID($element['PRODUCT_ID']);
				$arProducts[] = $element['PRODUCT_ID'];
				if(!$product){
					continue;
				}
				$product->moreFields($element);
				// хер знает зачем пересчитывается вся корзина, отключаю
				//$total += $product->get('price-num') * $product->get('buy-amount');
				$valenki = $product->get('brand');
				$products[] = $product;
			}
        }

    ?>

	<? if(count($products)){ ?>
		<form action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form" method="post">
			<input type="hidden" name="BasketOrder" value="BasketOrder" />
			<input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode($arHeaders, ","))?>" />
			<input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ","))?>" />
			<input type="hidden" id="action_var" value="<?=CUtil::JSEscape($arParams["ACTION_VARIABLE"])?>" />
			<input type="hidden" id="quantity_float" value="<?=$arParams["QUANTITY_FLOAT"]?>" />
			<input type="hidden" id="count_discount_4_all_quantity" value="<?=($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N"?>" />
			<input type="hidden" id="price_vat_show_value" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>" />
			<input type="hidden" id="hide_coupon" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>" />

			<input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />

		    <div class="cart_list">


			    <?
			    	foreach($products as $product){

			    		echo $product->html('basket');
			    	}
			    ?>
		        <div class="row total_price">


					<div class="col discount_number_block">
                        <?$APPLICATION->IncludeComponent(
                            "itsfera:discounts.cards",
                            "mini",
                            Array()
                        );?>
					</div>

                    <?
                    $allDiscount = 0;
                    foreach ( $arResult['GRID']['ROWS']  as $row) {

                             $allDiscount = $allDiscount + $row['SUM_DISCOUNT_PRICE'];
                    }
                    ?>

                    <div class="col total_price_block"
                         data-discount="<?=$arResult['DISCOUNT_PRICE_ALL']?>"
                        data-discountAll="<?=$allDiscount?>">

                        <? if (   $arResult['allSum_FORMATED'] != $arResult['PRICE_WITHOUT_DISCOUNT'] ) {
                            ?>
		            	<div class="discount-value">
                           <span class="product_price_value"><?=MHT\Product::formatPrice($arResult['PRICE_WITHOUT_DISCOUNT'])?> </span> <span class="rub"><span>рублей</span></span>
                        </div>
                            <?
                        }?>

		            	<div class="product_price">
                            <span class="product_price_value js-global-total-price"></span> <span class="rub"><span>рублей</span></span>
                        </div>
		            </div>

                    <div class="col next_block">
                        <a href="<?=$arParams['PATH_TO_ORDER']?>" class="next">Оформить заказ</a>
		            </div>

		        </div>
		    </div>
		    <div class="clear"></div>

                <div class="bx_ordercart_order_pay_left" id="coupons_block">
                    <?
                    if ($arParams["HIDE_COUPON"] != "Y")
                    {
                        ?>
                        <div class="bx_ordercart_coupon">
                            <span>Введите код купона для скидки:</span>
                            <input type="text" id="coupon" name="COUPON" value="" onchange="enterCoupon();">&nbsp;
                            <a class="bx_bt_button" href="javascript:void(0)" onclick="enterCoupon();" title="Применить">Применить</a>
                        </div>
                        <?
                        if (!empty($arResult['COUPON_LIST']))
                        {
                            foreach ($arResult['COUPON_LIST'] as $oneCoupon)
                            {
                                $couponClass = 'disabled';
                                switch ($oneCoupon['STATUS'])
                                {
                                    case DiscountCouponsManager::STATUS_NOT_FOUND:
                                    case DiscountCouponsManager::STATUS_FREEZE:
                                        $couponClass = 'bad';
                                        break;
                                    case DiscountCouponsManager::STATUS_APPLYED:
                                        $couponClass = 'good';
                                        break;
                                }
                                ?><div class="bx_ordercart_coupon"><input disabled readonly type="text" name="OLD_COUPON[]" value="<?=htmlspecialcharsbx($oneCoupon['COUPON']);?>" class="<? echo $couponClass; ?>" title="<?
                                    if (isset($oneCoupon['CHECK_CODE_TEXT']))
                                    {
                                        echo (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br>', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);
                                    }
                                ?>"><span class="<? echo $couponClass; ?>" data-coupon="<? echo htmlspecialcharsbx($oneCoupon['COUPON']); ?>"></span>
                                <span class="description">
                                    <?=($oneCoupon['DISCOUNT_NAME'] ? $oneCoupon['DISCOUNT_NAME'] : '')?>
                                    <?

    if (  $couponClass == 'disabled' && $arMessage = CouponConditions::toString( $oneCoupon ) )      {
        echo '<span class="conditions"> Не выполнено условие: '.implode(';',$arMessage).'</span>';
    }
                                    ?>
                                </span></div><?
                            }
                            unset($couponClass, $oneCoupon);


                        }
                    }
                    else
                    {
                        ?>&nbsp;<?
                    }
                    ?>
                </div>


		</form>

		
	<? } else { ?>
		<div class="nothing-in-basket">
			В корзине нет товаров.
		</div>
	<? } ?>


<?
		/********************
		**black friday 2016**
		*******************
		
		<p>
		<a href="/catalog/blackfridaysale2015/"><img src="/images/bf2016.jpg"></a>
		</p>
		
		
		\Bitrix\Main\Page\Asset::getInstance()->addJs("/banners/countdown.js");
		\Bitrix\Main\Page\Asset::getInstance()->addCss("/banners/countdown.css");
		
		$seconds = strtotime("2016-11-24 21:00:00") - time();
		$days = str_pad(floor($seconds / 86400), 2, '0', STR_PAD_LEFT);
		$seconds %= 86400;
		$hours = str_pad(floor($seconds / 3600), 2, '0', STR_PAD_LEFT);
		$seconds %= 3600;
		$minutes = str_pad(floor($seconds / 60), 2, '0', STR_PAD_LEFT);
		$seconds %= 60;			
		$seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);
		//echo $days.':'.$hours.':'.$minutes.':'.$seconds;
		?>
		
		<script>
		  $(document).ready(function(){
			$(".digits").countdown({
			  image: "/banners/digits.png",
			  format: "dd:hh:mm:ss",
			  startTime: "<?=$days.':'.$hours.':'.$minutes.':'.$seconds?>"
			});
		  });
		</script>
		
		<a href="/o_kompanii/novosti/664791/">
		<div class="banner_friday">
			<div class="wrapper_friday">
			  <div class="cell_friday">
				<div id="holder_friday">
				  <div class="digits"></div>
				</div>
			  </div>
			</div>
		</div>		
		</a>
		
		<?
		*******************
		**black friday 2016**
		********************/
		?>
	
	
		<div data-retailrocket-markup-block="58886fde5a658842d81a0402" data-products="<?=implode(',', $arProducts);?>"></div>

	
	<?/*$APPLICATION->IncludeComponent('mht:offers', 'basket', array(
			'NAME' => 'Распродажа',
			'TYPE' => 'SAYT_AKTSIONNYY_TOVAR',
			'SORT_BY' => 'NAME',
			'SORT_ORDER' => 'ASC',
			'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
			'HIDE_ACTION_LABEL' => 'Y'
))*/?>
		
		
		<?$APPLICATION->IncludeComponent('mht:offers', 'basket', array(
			'NAME' => 'Новинки',
			'TYPE' => 'SAYT_NOVINKA',
			'SORT_BY' => 'NAME',
			'SORT_ORDER' => 'ASC',
			'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
			'HIDE_NEW_LABEL' => 'Y'
		))?>

	<?MHT::showRecentlyViewed()?>

</div>
</div>
<?



$arUrls = Array(
	"delete" => $APPLICATION->GetCurPage()."?".$arParams["ACTION_VARIABLE"]."=delete&id=#ID#",
	"delay" => $APPLICATION->GetCurPage()."?".$arParams["ACTION_VARIABLE"]."=delay&id=#ID#",
	"add" => $APPLICATION->GetCurPage()."?".$arParams["ACTION_VARIABLE"]."=add&id=#ID#",
);

$arBasketJSParams = array(
	'SALE_DELETE' => GetMessage("SALE_DELETE"),
	'SALE_DELAY' => GetMessage("SALE_DELAY"),
	'SALE_TYPE' => GetMessage("SALE_TYPE"),
	'TEMPLATE_FOLDER' => $templateFolder,
	'DELETE_URL' => $arUrls["delete"],
	'DELAY_URL' => $arUrls["delay"],
	'ADD_URL' => $arUrls["add"]
);

?>
	<script type="text/javascript">
		var basketJSParams = <?=CUtil::PhpToJSObject($arBasketJSParams);?>
	</script>
<?

$APPLICATION->AddHeadScript($templateFolder."/script.js");

return; 


include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/functions.php");

if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
{
	?>
	<div id="warning_message">
		<?
		if (is_array($arResult["WARNING_MESSAGE"]) && !empty($arResult["WARNING_MESSAGE"]))
		{
			foreach ($arResult["WARNING_MESSAGE"] as $v)
				echo ShowError($v);
		}
		?>
	</div>
	<?

	$normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);
	$normalHidden = ($normalCount == 0) ? "style=\"display:none\"" : "";

	$delayCount = count($arResult["ITEMS"]["DelDelCanBuy"]);
	$delayHidden = ($delayCount == 0) ? "style=\"display:none\"" : "";

	$subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
	$subscribeHidden = ($subscribeCount == 0) ? "style=\"display:none\"" : "";

	$naCount = count($arResult["ITEMS"]["nAnCanBuy"]);
	$naHidden = ($naCount == 0) ? "style=\"display:none\"" : "";

	?>
		<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form">
			<div id="basket_form_container">
				<div class="bx_ordercart">
					<div class="bx_sort_container">
						<span><?=GetMessage("SALE_ITEMS")?></span>
						<a href="javascript:void(0)" id="basket_toolbar_button" class="current" onclick="showBasketItemsList()"><?=GetMessage("SALE_BASKET_ITEMS")?><div id="normal_count" class="flat" style="display:none">&nbsp;(<?=$normalCount?>)</div></a>
						<a href="javascript:void(0)" id="basket_toolbar_button_delayed" onclick="showBasketItemsList(2)" <?=$delayHidden?>><?=GetMessage("SALE_BASKET_ITEMS_DELAYED")?><div id="delay_count" class="flat">&nbsp;(<?=$delayCount?>)</div></a>
						<a href="javascript:void(0)" id="basket_toolbar_button_subscribed" onclick="showBasketItemsList(3)" <?=$subscribeHidden?>><?=GetMessage("SALE_BASKET_ITEMS_SUBSCRIBED")?><div id="subscribe_count" class="flat">&nbsp;(<?=$subscribeCount?>)</div></a>
						<a href="javascript:void(0)" id="basket_toolbar_button_not_available" onclick="showBasketItemsList(4)" <?=$naHidden?>><?=GetMessage("SALE_BASKET_ITEMS_NOT_AVAILABLE")?><div id="not_available_count" class="flat">&nbsp;(<?=$naCount?>)</div></a>
					</div>
					<?
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delayed.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribed.php");
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_not_available.php");
					?>
				</div>
			</div>
			<input type="hidden" name="BasketOrder" value="BasketOrder" />
			<!-- <input type="hidden" name="ajax_post" id="ajax_post" value="Y"> -->
		</form>
	<?
}
else
{
	ShowError($arResult["ERROR_MESSAGE"]);
}
?>