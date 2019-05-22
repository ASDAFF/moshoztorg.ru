<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="my_orders_page">
	<div class="my_orders">
		<h1>Отменить заказ</h1>
		<div class="bx_my_order_cancel cancel-order">
			<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
				<form method="post" action="<?=POST_FORM_ACTION_URI?>">
					<input type="hidden" name="CANCEL" value="Y">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
					
					<p>
						<?=GetMessage("SALE_CANCEL_ORDER1") ?>
					
						<a href="<?=$arResult["URL_TO_DETAIL"]?>"><?=GetMessage("SALE_CANCEL_ORDER2")?> #<?=$arResult["ACCOUNT_NUMBER"]?></a>?
						<b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b><br /><br />
						<?= GetMessage("SALE_CANCEL_ORDER4") ?>:<br />
						
						<textarea name="REASON_CANCELED"></textarea><br /><br />
						<input type="submit" name="action" value="<?=GetMessage("SALE_CANCEL_ORDER_BTN") ?>">
					</p>
				</form>
			<?else:?>
				<?=ShowError($arResult["ERROR_MESSAGE"]);?>
			<?endif;?>

		</div>
	</div>
</div>
