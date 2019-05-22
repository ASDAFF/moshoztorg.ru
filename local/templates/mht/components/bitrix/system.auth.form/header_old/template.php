<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arResult["FORM_TYPE"] == "login"){?>
	<a class="unlogged-user" href="/personal/auth/" data-hayhop="#auth_holder" data-title="Войти на сайт">вход</a>
	<div id="auth_holder" style="display:none"> 
		<?
		if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
			ShowMessage($arResult['ERROR_MESSAGE']);
		?>
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
                            console.log('response:',response);
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
            function afterSuccessCall () {
                $.post('/ajax.php', {
                    'action' : 'RRsentemail'
                }, function(id){
                    location.reload();
                });
            }
		</script>
		<?
		$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "header", 
			array(
				"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
				"AUTH_URL"=>$arResult["AUTH_URL"],
				"POST"=>$arResult["POST"],
				"POPUP"=>"Y",
				"SUFFIX"=>"form",
			), 
			$component, 
			array()
		);
		?>
		<hr>
		<form action="/ajax.php" method="POST" enctype="multipart/form-data" class="js-form login" data-onsuccess="afterSuccessCall();">
			<?if($arResult["BACKURL"] <> ''){?>
				<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
			<?}?>
			<?foreach ($arResult["POST"] as $key => $value){?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?}?>
			<input type="hidden" name="AUTH_FORM" value="Y" />
			<input type="hidden" name="TYPE" value="AUTH" />
			<input type="hidden" name="act" value="login-form">
			<input type="hidden" name="confirm" value="1">
			<table>
				<tbody>
					<tr>
						<td>Логин/email/телефон:</td>
					</tr>
					<tr>
						<td><input type="text" name="login" value=""></td>
					</tr>
					<tr>
						<td>Пароль:</td>
					</tr>
					<tr>
						<td><input type="password" name="password" value=""></td>
					</tr>
					<tr>
						<br>
						<td><input id="Webprofy_Ajax_Field_File_0" type="checkbox" name="remember">
						<label for="Webprofy_Ajax_Field_File_0">Запомнить меня</label>
						<div class="righter"> <a href="/personal/forgot/">Забыли пароль?</a><br><br>
						<a href="/personal/register/">Регистрация</a> </div></td>
					</tr>
				</tbody>
			</table>
			<input type="submit" value="Войти">
		</form>
	</div>
<?}else{?>
	<div class="logged-user">
		<a href="/personal/" class="user">
		<?=$USER->GetFirstName().' '.substr($USER->GetLastName(),0,1).'.'?></a>
		<span>|</span>
		<a href="#" class="exit js-unlog-button">Выход</a>
	</div>
<?}?>