<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']) {
	ShowMessage($arResult['ERROR_MESSAGE']);
	?><script>$(function(){
			$('.registerheading').slideToggle();
			$('#signin-header-link').trigger('click');
		});</script><?
}

/*?>
<script>
	$(function(){
		$('.js-form').each(function(){
			var $form = $(this).ajaxForm({
				beforeSubmit : function(fields){
					$.each(fields, function(i, field){
						if(field.name == 'confirm'){
							field.value = 0;
						}
					});
					$form.addClass('loading');
				},
				success : function(response){
					$form.removeClass('loading');
					eval('response = ' + response + ';');
					if(response.ok == '1'){
						$(document).trigger('formajax.success', [ $form.attr('id') , $form ]);
						$form.addClass('form-complete');
						var onsuccess = $form.attr('data-onsuccess');
						if(onsuccess && onsuccess.length){
							eval(onsuccess);
						}
						$form.find('input[type=text], textarea').val('');
					}
					else{
						$form.addClass('error');
						if(response.fields && response.fields.length){
							$.each(response.fields, function(i, field){
								$form.find('[name=' + field.name + ']').addClass('error');
							});
						}
					}
					captcha.update();
				}
			}),
			captcha = {
				ok : false,
				_init : function(){
					var self = this;
					this.e = {
						image : $form.find('.js-captcha-image'),
						text : $form.find('.js-captcha-text'),
						code : $form.find('.js-captcha-code')
					};
					if(!this.e.text.length || !this.e.image.length || !this.e.code.length){
						return;
					}
					this.ok = true;
					this.e.image.click(function(){
						self.update();
					})
				},
				update : function(){
					if(!this.ok){
						return;
					}
					var self = this;
					$.post('/ajax.php', {
						'get_captcha' : '1'
					}, function(id){
						self.e.text.val('');
						self.e.image.attr('src', '/bitrix/tools/captcha.php?captcha_sid=' + id);
						self.e.code.val(id);
					});
				}
			},
			onchange = function(){
				$(this).removeClass('error');
			};
			captcha._init();
			$form.find('input, textarea').change(onchange).keyup(onchange);
		});
	})
</script>
<?*/
/*$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "header",
	array(
		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
		"AUTH_URL"=>$arResult["AUTH_URL"],
		"POST"=>$arResult["POST"],
		"POPUP"=>"Y",
		"SUFFIX"=>"form",
	),
	$component,
	array()
);*/
?>


<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<?if($arResult["BACKURL"] <> ''):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?endif?>
	<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?endforeach?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
	<div class="gt_registerform">
		<div class="gt_registerline">
			Логин:
			<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" size="17" />
		</div>
		<div class="gt_registerline">
			Пароль:
			<input type="password" name="USER_PASSWORD" maxlength="50" size="17" />
			<?if($arResult["SECURE_AUTH"]):?>
				<span class="bx-auth-secure" id="bx_auth_secure<?=$arResult["RND"]?>" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
				<div class="bx-auth-secure-icon"></div>
			</span>
				<noscript>
			<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
				<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
			</span>
				</noscript>
				<script type="text/javascript">
					document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
				</script>
			<?endif?>
		</div>
		<?/*if ($arResult["STORE_PASSWORD"] == "Y"):?>

				<td valign="top"><input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER_frm" title="<?=GetMessage("AUTH_REMEMBER_ME")?>"><?echo GetMessage("AUTH_REMEMBER_SHORT")?></label></td>
		<?endif*/?>
		<?if ($arResult["CAPTCHA_CODE"]):?>

		<div class="gt_registerline">
					Защита от роботов:
					<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
					<input type="text" name="captcha_word" maxlength="50" value="" />
		</div>
		<?endif?>

		<div class="gt_registerline">
			<input type="submit" name="Login" value="Войти" />
		</div>
	</div>
</form>