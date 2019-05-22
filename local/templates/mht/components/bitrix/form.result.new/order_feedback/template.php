<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<div class="<?= $arParams['FORM_CONTAINER_CLASS'] ?>" id="<?= $arParams['FORM_CONTAINER_ID'] ?>">

<? if($arResult["FORM_NOTE"] != ""): ?>
	<div class="form-note"><?=$arResult["FORM_NOTE"]?></div>
<? endif; ?>

<?if ($arResult["isFormNote"] != "Y") : ?>
	<?=$arResult["FORM_HEADER"]?>
	<input type="hidden" name="ajax" value="1" />
	<input type="hidden" name="action" value="formsubmit" />
	<input type="hidden" name="confirm" value="1" />
	<?if ($arResult["isFormErrors"] == "Y"):?>
		<?=$arResult["FORM_ERRORS_TEXT"];?>
	<?endif;?>


	<? if ($arResult["isFormTitle"] && $arParams["SHOW_TITLE"] != "N"): ?>
		<div class="form-title"><?=$arResult["FORM_TITLE"]?></div>
	<? endif; ?>

	<? if($arResult["isFormImage"] == "Y"): ?>
		<div class="form-image"><img src="<?=$arResult["FORM_IMAGE"]["URL"]?>" /></div>
	<? endif; ?>

	<? if($arResult["FORM_DESCRIPTION"] != ""): ?>
		<div class="form-description"><p><?=$arResult["FORM_DESCRIPTION"]?></p></div>
	<? endif; ?>

	<div class="form-questions">

		<? foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion): ?>
		
			<? if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden'): ?>
				<?= $arQuestion["HTML_CODE"]; ?>
			<? else: ?>
			<div class="field">
				<div class="left"><?=$arQuestion["CAPTION"]?><?= ($arQuestion["REQUIRED"] == "Y" ? $arResult["REQUIRED_SIGN"] : "" ) ?></div>
				<div class="right"><?=$arQuestion["HTML_CODE"]?></div>
			</div>
			<? endif; ?>

		<? endforeach; ?>

		<?if($arResult["isUseCaptcha"] == "Y"): ?>
			<div class="captcha">
				<div class="captcha-title"><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></div>
				<div class="captcha-image">
					<input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" />
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" />
				</div>
				<div class="captcha-field-title"><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?><?=$arResult["REQUIRED_SIGN"];?></div>
				<div class="captcha-field"><input type="text" name="captcha_word" size="30" maxlength="50" value="" /></div>
			</div>
		<? endif; ?>

		<div class="field buttons">
			<div class="left">
				<input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" class="button-primary" />
			</div>
			<div class="right">&nbsp;</div>
			
				<? /* if ($arResult["F_RIGHT"] >= 15):?>
				&nbsp;<input type="hidden" name="web_form_apply" value="Y" /><input type="submit" name="web_form_apply" value="<?=GetMessage("FORM_APPLY")?>" />
				<?endif;?>
				&nbsp;<input type="reset" value="<?=GetMessage("FORM_RESET");?>" /> */?>
		</div>
	</div>

	<? /* <p><?=$arResult["REQUIRED_SIGN"];?> - <?=GetMessage("FORM_REQUIRED_FIELDS")?></p> */?>

	<?=$arResult["FORM_FOOTER"]?>
<? endif; //isFormNote ?>

<script>
	$(function(){
		$('form[name="<?= $arResult['arForm']['SID'] ?>"]').initWebForm();
	});
</script>

</div>