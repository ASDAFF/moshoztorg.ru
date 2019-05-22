<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?

?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
   <div class="subscribe_page">
      <div class="subscribe">
        <h1>Рассылки</h1>
        <div class="authorization">
            <div class="row h3">
                <div class="col">Авторизация</div>
            </div>
            <div class="row">
                <div class="col title">Адрес подписки будет принадлежать пользователю:</div>
                <div class="col">Марфа Петровна [marfa].</div>
                <div class="col">Вы можете <a href="#">закончить сеанс</a>, чтобы авторизоваться под другим пользователем.</div>
            </div>
        </div>
        <div class="settings">
            <div class="row h3">
                <div class="col">Настройки подписки</div>
            </div>
            <div class="row">
                <div class="col">
	                	<label>Ваш e-mail</label>
	                    <input type="text" name="sf_EMAIL" size="20" value="<?=$arResult["EMAIL"]?>" title="<?echo GetMessage("SUBSCR_EMAIL_TITLE")?>" />
	                    <div class="h4">Рубрики подписки</div>
                		<? foreach($arResult["RUBRICS"] as $itemID => $itemValue){ ?>
		                    <div>
		                    	<input class="checkbox-input" name="news" type="checkbox" type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?=$itemID?>" value="<?=$itemValue["ID"]?>">
		                    	<label for="sf_RUB_ID_<?=$itemID?>" class="checkbox-label"><?=$itemValue["NAME"]?> <?=$arResult["SHOW_COUNT"] ? $itemValue["SUBSCRIBER_COUNT"] : ''?></label>
		                    </div>
						<? } ?>
						<?/*
	                    <div class="h4">Предпочтительный формат</div>
	                    <div>
	                    	<div class="field">
	                    	<input class="radio-input" name="format" id="format_text" type="radio"><label for="format_text" class="radio-label">Текст</label>
	                        </div><!--
	                        --><div class="field">
	                        <input class="radio-input" name="format" id="format_html" type="radio"><label for="format_html" class="radio-label">HTML</label>
	                        </div>
	                    </div>
	                    */?>
                </div>
                <div class="col"><div class="description">После добавления или изменения адреса подписки вам будет выслан код подтверждения. Подписка будет не активной до ввода кода подтверждения.</div></div>
            </div>
        </div>
        <div class="buttons"><a href="#" onclick="$(this).closest('form').submit(); return false;">Сохранить</a></div>
        <?=MHT::showRecentlyViewed('mht2')?>
      </div>
    </div>
</form>

<? return ?>


<div class="subscribe-index">

<h4><?echo GetMessage("SUBSCR_NEW_TITLE")?></h4>
<p><?echo GetMessage("SUBSCR_NEW_NOTE")?></p>
<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
	<table class="data-table" border="0" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
			<td>&nbsp;</td>
			<td><?echo GetMessage("SUBSCR_NAME")?></td>
			<td><?echo GetMessage("SUBSCR_DESC")?></td>
			<?if($arResult["SHOW_COUNT"]):?>
				<td><?echo GetMessage("SUBSCR_CNT")?></td>
			<?endif;?>
		</tr>
		</thead>
		<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
		<tr>
			<td><input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?=$itemID?>" value="<?=$itemValue["ID"]?>" checked /></td>
			<td><label for="sf_RUB_ID_<?=$itemID?>"><?=$itemValue["NAME"]?></label></td>
			<td><?=$itemValue["DESCRIPTION"]?></td>
			<?if($arResult["SHOW_COUNT"]):?>
				<td align="right"><?=$itemValue["SUBSCRIBER_COUNT"]?></td>
			<?endif?>
		</tr>
		<?endforeach;?>
	</table>
	<p><?echo GetMessage("SUBSCR_ADDR")?>&nbsp;
	<input type="text" name="sf_EMAIL" size="20" value="<?=$arResult["EMAIL"]?>" title="<?echo GetMessage("SUBSCR_EMAIL_TITLE")?>" />
	<input type="submit" value="<?echo GetMessage("SUBSCR_BUTTON")?>" />
	</p>
</form>
<br />

<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
<?echo bitrix_sessid_post();?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
<thead><tr><td colspan="2"><?echo GetMessage("SUBSCR_EDIT_TITLE")?></td></tr></thead>
<tr valign="top">
	<td width="40%">
		<p>e-mail<br />
		<input type="text" name="sf_EMAIL" size="20" value="<?=$arResult["EMAIL"]?>" title="<?echo GetMessage("SUBSCR_EMAIL_TITLE")?>" /></p>
		<?if($arResult["SHOW_PASS"]=="Y"):?>
			<p><?echo GetMessage("SUBSCR_EDIT_PASS")?><span class="starrequired">*</span><br />
			<input type="password" name="AUTH_PASS" size="20" value="" title="<?echo GetMessage("SUBSCR_EDIT_PASS_TITLE")?>" /></p>
		<?else:?>
			<p><span class="green"><?echo GetMessage("SUBSCR_EDIT_PASS_ENTERED")?></span><span class="starrequired">*</span></p>
		<?endif;?>
	<td width="60%">
		<p><?echo GetMessage("SUBSCR_EDIT_NOTE")?></p>
	</td>
</tr>
<tfoot><tr><td colspan="2">
	<input type="submit" value="<?echo GetMessage("SUBSCR_EDIT_BUTTON")?>" />
</td></tr></tfoot>
</table>
<input type="hidden" name="action" value="authorize" />
</form>
<br />

<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
<?echo bitrix_sessid_post();?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
<thead><tr><td colspan="2"><?echo GetMessage("SUBSCR_PASS_TITLE")?></td></tr></thead>
<tr valign="top">
	<td width="40%">
		<p>e-mail<br />
		<input type="text" name="sf_EMAIL" size="20" value="<?=$arResult["EMAIL"]?>" title="<?echo GetMessage("SUBSCR_EMAIL_TITLE")?>" /></p>
	<td width="60%">
		<p><?echo GetMessage("SUBSCR_PASS_NOTE")?></p>
	</td>
</tr>
<tfoot><tr><td colspan="2">
	<input type="submit" value="<?echo GetMessage("SUBSCR_PASS_BUTTON")?>" />
</td></tr></tfoot>
</table>
<input type="hidden" name="action" value="sendpassword" />
</form>
<br />

<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
<?echo bitrix_sessid_post();?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
<thead><tr><td colspan="2"><?echo GetMessage("SUBSCR_UNSUBSCRIBE_TITLE")?></td></tr></thead>
<tr valign="top">
	<td width="40%">
		<p>e-mail<br />
		<input type="text" name="sf_EMAIL" size="20" value="<?=$arResult["EMAIL"]?>" title="<?echo GetMessage("SUBSCR_EMAIL_TITLE")?>" /></p>
		<?if($arResult["SHOW_PASS"]=="Y"):?>
			<p><?echo GetMessage("SUBSCR_EDIT_PASS")?><span class="starrequired">*</span><br />
			<input type="password" name="AUTH_PASS" size="20" value="" title="<?echo GetMessage("SUBSCR_EDIT_PASS_TITLE")?>" /></p>
		<?else:?>
			<p><span class="green"><?echo GetMessage("SUBSCR_EDIT_PASS_ENTERED")?></span><span class="starrequired">*</span></p>
		<?endif;?>
	<td width="60%">
		<p><?echo GetMessage("SUBSCR_UNSUBSCRIBE_NOTE")?></p>
	</td>
</tr>
<tfoot><tr><td colspan="2">
	<input type="submit" value="<?echo GetMessage("SUBSCR_EDIT_BUTTON")?>" />
</td></tr></tfoot>
</table>
<input type="hidden" name="action" value="authorize" />
</form>
<br />

<p><span class="starrequired">*&nbsp;</span><?echo GetMessage("SUBSCR_NOTE")?></p>

</div>