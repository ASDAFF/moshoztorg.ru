<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$templateFolder = $this->__component->__template->__folder;
?>
<script>
    var one_click_order_ajax_path = '<?=$templateFolder?>/ajax.php';
</script>
<div class="one-click-wrapper">
<span class="result_message">
    <?if (!empty($arResult["ERRORS"])):?>
        <?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
    <?endif;
    if (strlen($arResult["MESSAGE"]) > 0):?>
        <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>
</span>
<form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
    <?=$arResult["FIELDS"]['USER_NAME']?>
    <?=$arResult["FIELDS"]['PHONE']?>

    <?foreach($arParams['HIDDEN_PROPERTIES'] as $key=>$val):
        $sCode = array_key_exists($key,$arResult['PROPERTY_IDS'])?'ONE_CLICK_ORDER_'.$arResult['PROPERTY_IDS'][$key]:'ONE_CLICK_ORDER_'.$key;
        ?>
        <input type="hidden" id="<?php echo $sCode;?>" value="<?php echo $val;?>" name="PROPERTY[<?php echo $key;?>][0]">
    <?endforeach?>
     <?=bitrix_sessid_post()?>
    <input type="hidden" name="iblock_submit" value="Y">
    <input type="hidden" name="ajax_type" value="fast_order">
    <button id="submit_one_click_order">Отправить</button>
</form>
</div>