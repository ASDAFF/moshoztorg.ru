<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="cart_success_page">
	<div class="cart_success">
	    <div class="order_info">
			<?
				if (!empty($arResult["ORDER"]))
				{
					?>
				        <h1>Оформление заказа</h1>
				        <p class="h2">Ваш заказ сформирован</p>
				        <p>Ваш заказ №<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?> от <?=$arResult["ORDER"]["DATE_INSERT"]?> успешно создан.</p>
				        <p>Вы можете следить за выполением своего заказа в <a href="<?=$arParams["PATH_TO_PERSONAL"]?>">персональном разделе</a> сайта.</p>
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
										WP::log(CSalePaySystemAction::GetParamValue("SHOULD_PAY"));
										include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
									}
								}
				        	}
				        ?>
					<?
				}
				else
				{
					?>
				        <h1><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></h1>
				        <?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
						<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
					<?
				}
			?>
		</div>
	</div>
</div>