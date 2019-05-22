<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->IncludeLangFile("template.php");
?>
<div class="bx_small_cart<?=$arResult['NUM_PRODUCTS'] > 0 && $arParams['SHOW_TOTAL_PRICE'] == 'Y' ? '' : ' empty_cart'?>">
	<a class="bx_cart_top_inline_link" href="<?=$arParams['PATH_TO_BASKET']?>">
		<?=GetMessage('TSB1_CART')?>
		<?if($arParams['SHOW_NUM_PRODUCTS'] == 'Y'):?>
			<strong>(<?=$arResult['NUM_PRODUCTS']?>)</strong>
		<?endif?>
	</a>
	<?if($arParams['SHOW_TOTAL_PRICE'] == 'Y' && $arResult['NUM_PRODUCTS'] > 0):?>
		<br><?=GetMessage('TSB1_TOTAL_PRICE')?> <?=$arResult['TOTAL_PRICE']?>
	<?endif?>
</div>