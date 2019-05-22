<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div id="bx_cart_block_inline" class="bx_cart_block bx_cart_top_inline">
	<?$frame = $this->createFrame("bx_cart_block_inline", false)->begin()?>
		<?require(realpath(dirname(__FILE__)).'/ajax_template.php')?>
	<?$frame->beginStub()?>
		<div class="bx_small_cart empty">
			<a class="bx_cart_top_inline_link" href="<?=$arParams['PATH_TO_BASKET']?>">
				<?=GetMessage('TSB1_CART')?>
			</a>
		</div>
	<?$frame->end()?>
</div>
<script>
	BX.addCustomEvent(window, "OnBasketChange", function()
	{
		BX.ajax({
			url: '<?=$componentPath?>/ajax.php',
			method: 'POST',
			dataType: 'html',
			data: {
				sessid: BX.bitrix_sessid(),
				siteId: '<?=SITE_ID?>',
				templateName: '<?=$templateName?>',
				arParams: <?=CUtil::PhpToJSObject(array(
					'SHOW_NUM_PRODUCTS' => $arParams['SHOW_NUM_PRODUCTS'],
					'SHOW_TOTAL_PRICE' => $arParams['SHOW_TOTAL_PRICE'],
					'PATH_TO_BASKET' => $arParams['PATH_TO_BASKET']
				))?>
			},
			onsuccess: function(result)
			{
				var elemBlock = BX("bx_cart_block_inline");
				if (elemBlock)
					elemBlock.innerHTML = result;
			}
		});
	});
</script>