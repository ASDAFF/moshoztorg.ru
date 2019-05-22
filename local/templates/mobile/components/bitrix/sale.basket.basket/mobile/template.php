<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	//WP::loadScript('/js_/cart.js?v=1');
use Bitrix\Main\Page\Asset,
    Bitrix\Sale\DiscountCouponsManager;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/cart.js");
?>
<section class="padding">
		<div class="headingline inner">
			<p class="heading">Корзина</p>
		</div>
		<div class="cart">
  <?
        //ставим сумму рассчитанную компонентом
        $total = $arResult['allSum'];
        $products = array();
        foreach($arResult['GRID']['ROWS'] as $element){
			if($element["DELAY"] != 'Y'){
				$product = MobileCatalog::byID($element['PRODUCT_ID']);
				if(!$product){
					continue;
				}
				$product->moreFields($element);
				//$total += $product->get('price-num') * $product->get('buy-amount');
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
			<input type="hidden" id="coupon_approved" value="N" />
			<input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />


			    <?
			    	foreach($products as $product){
			    		echo $product->html('basket');
			    	}
			    ?>

			<div class="cartitogblock">

                <?$APPLICATION->IncludeComponent(
                    "itsfera:discounts.cards",
                    "mini",
                    Array()
                );?>

                <div class="col discount_number_block" id="coupons_block">
                    <?
                    if ($arParams["HIDE_COUPON"] == "Y")
                    {
                        ?>

                        <input type="text" name="COUPON" id="coupon" data-sessid="<?php echo bitrix_sessid();?>" value=""  placeholder="Введите купон для скидки" class="input_discont" title="Введите купон для скидки"  onchange="enterCoupon();"><a class="submit-discount-button" href="javascript:void(0)" onclick="enterCoupon();">Применить</a>

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
                                ?>"><span class="<? echo $couponClass; ?>" data-coupon="<? echo htmlspecialcharsbx($oneCoupon['COUPON']); ?>"></span> <span class="description"><?=($oneCoupon['DISCOUNT_NAME'] ? $oneCoupon['DISCOUNT_NAME'] : '')?></span></div><?
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


				<p class="cartitogprice"><?=MHT\Product::formatPrice($total)?></p>
			</div>
			<a href="#" onclick="$('#basket_form').submit();" class="greenbutton full">Оформить заказ</a>

		</form>

	<? } else { ?>
		<div class="nothing-in-basket">
			В корзине нет товаров.
		</div>
	<? } ?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.viewed.product",
			'mobile',
			Array(
				"VIEWED_COUNT" => "6",
				"VIEWED_NAME" => "Y",
				"VIEWED_IMAGE" => "Y",
				"VIEWED_PRICE" => "Y",
				"VIEWED_CURRENCY" => "default",
				"VIEWED_CANBUY" => "Y",
				"VIEWED_CANBASKET" => "Y",
				"VIEWED_IMG_HEIGHT" => "150",
				"VIEWED_IMG_WIDTH" => "150",
				"BASKET_URL" => "/personal/basket.php",
				"ACTION_VARIABLE" => "action",
				"PRODUCT_ID_VARIABLE" => "id",
				"SET_TITLE" => "N"
			)
		);
		?>
		</div>
</section>

<?
/*


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
}*/
?>