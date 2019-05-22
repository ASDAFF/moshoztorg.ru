<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
	function changePaySystem(param)
	{
		if (BX("account_only") && BX("account_only").value == 'Y') // PAY_CURRENT_ACCOUNT checkbox should act as radio
		{
			if (param == 'account')
			{
				if (BX("PAY_CURRENT_ACCOUNT"))
				{
					BX("PAY_CURRENT_ACCOUNT").checked = true;
					BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
					BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

					// deselect all other
					var el = document.getElementsByName("PAY_SYSTEM_ID");
					for(var i=0; i<el.length; i++)
						el[i].checked = false;
				}
			}
			else
			{
				BX("PAY_CURRENT_ACCOUNT").checked = false;
				BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
				BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
			}
		}
		else if (BX("account_only") && BX("account_only").value == 'N')
		{
			if (param == 'account')
			{
				if (BX("PAY_CURRENT_ACCOUNT"))
				{
					BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

					if (BX("PAY_CURRENT_ACCOUNT").checked)
					{
						BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
						BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
					}
					else
					{
						BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
						BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
					}
				}
			}
		}

		submitForm();
		// mht.fit();
	}
</script>
<div class="formline">
	<p class="formheading"><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></p>
	<?
	if ($arResult["PAY_FROM_ACCOUNT"] == "Y")
	{
		$accountOnly = ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N";
		?>
		<input type="hidden" id="account_only" value="<?=$accountOnly?>" />
		<div class="bx_block w100 vertical">
			<div class="bx_element">
				<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
				<label for="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT_LABEL" onclick="changePaySystem('account');" class="<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo "selected"?>">
					<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?>>
					<div class="bx_logotype">
						<span style="background-image:url(<?=$templateFolder?>/images/logo-default-ps.gif);"></span>
					</div>
					<div class="bx_description">
						<strong><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?></strong>
						<p>
							<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")." <b>".$arResult["CURRENT_BUDGET_FORMATED"]?></b></div>
							<? if ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"):?>
								<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT3")?></div>
							<? else:?>
								<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?></div>
							<? endif;?>
						</p>
					</div>
				</label>
				<div class="clear"></div>
			</div>
		</div>
		<?
	}

	uasort($arResult["PAY_SYSTEM"], "cmpBySort"); // resort arrays according to SORT value

	?><div class="radiorow"><?
	$n = 0;
	foreach($arResult["PAY_SYSTEM"] as $arPaySystem){

		?>
		<label class="radioplate" for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;changePaySystem();">
			<p class="radiowrap">
				<input type="radio"
					   id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
					   name="PAY_SYSTEM_ID"
					   value="<?=$arPaySystem["ID"]?>"
					<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
					   onclick="changePaySystem();" />
			</p>
			<p class="radioinfowrap">
				<b class="radioinfowrapheading"><?=$arPaySystem["PSA_NAME"];?></b>
				<b class="radioinfodescription">
					<?
					if (intval($arPaySystem["PRICE"]) > 0)
						echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), GetMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
					else
						echo $arPaySystem["DESCRIPTION"];
					?>
				</b>
			</p>
		</label><?
		$n++;
	}
	?>
	</div>
</div>