<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div style="display: none;">
    <div class="form-default form-placeholder" id="call-back-form">
        <h3>Заказ обратного звонка</h3>

        <span class="result_message">
            <?if (!empty($arResult["ERRORS"])):?>
                <?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
            <?endif;
            if (strlen($arResult["MESSAGE"]) > 0):?>
                <?=ShowNote($arResult["MESSAGE"])?>
            <?endif?>
        </span>
        <form class="date_form" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
            <?=bitrix_sessid_post()?>
            <?if ( $arParams['USER_ID']>0 ):?>
                <input type="hidden" value="<?=intval($arParams['USER_ID'])?>" name="PROPERTY[<?=getPropertyIdByCode("USER_ID",$arParams['IBLOCK_ID'])?>][0]">
            <?endif?>

            <?=$arResult["FIELDS"]['NAME']?>
            <?=$arResult["FIELDS"]['EMAIL']?>
            <?=$arResult["FIELDS"]['PHONE']?>
            <?=$arResult["FIELDS"]['PREVIEW_TEXT']?>
			
			<?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>

				<div><?=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")?></div>
				<div>
					<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
				</div>

				<div class="control-group">
					<label style="display: block;" class="control-label"><?=GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT")?>
						<font color="red"><span class="form-required starrequired">*</span></font>
					</label>
					<div class="controls ORDER_PROP_1">
						<div class="input input-text">
							<input type="text" class="inputtext" name="captcha_word" maxlength="50" value="">
						</div>
					</div>
				</div>

			<?endif?>
			
            <div class="form-actions" style="margin-top:10px">
                <table width="100%"><tr>
                        <td style="text-align: left; line-height:10px;" valign="top"></td>
                        <td align="right">
                            <input type="hidden" name="iblock_submit" value="Y">
                            <input class="button" type="submit" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" />
                        </td>
                    </tr></table>
            </div>

        </form>
    </div>
</div>