<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

?>

<div class="bx-auth-profile personal personal-edit">
	<h1>Профиль пользователя</h1>

	<?ShowError($arResult["strProfileError"]);?>
	<?
	if ($arResult['DATA_SAVED'] == 'Y')
		ShowNote(GetMessage('PROFILE_DATA_SAVED'));
	?>
	<script type="text/javascript">
		<!--
		var opened_sections = [<?
			$arResult["opened"] = $_COOKIE[$arResult["COOKIE_PREFIX"]."_user_profile_open"];
			$arResult["opened"] = preg_replace("/[^a-z0-9_,]/i", "", $arResult["opened"]);
			if (strlen($arResult["opened"]) > 0)
			{
				echo "'".implode("', '", explode(",", $arResult["opened"]))."'";
			}
			else
			{
				$arResult["opened"] = "reg";
				echo "'reg'";
			}
		?>];
		//-->

		var cookie_prefix = '<?=$arResult["COOKIE_PREFIX"]?>';
	</script>
	<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
		<?=$arResult["BX_SESSION_CHECK"]?>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
		<h2>Личные данные</h2>
		<table class="profile-table data-table">
			<tbody>
				<tr>
					<td>
						<label><?=GetMessage('NAME')?></label><br/>
						<input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" />
					</td>
				</tr>
				<tr>
					<td>
						<label><?=GetMessage('LAST_NAME')?></label><br/>
						<input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" />
					</td>
				</tr>
				<tr>
					<td>
						<label><?=GetMessage('SECOND_NAME')?></label><br/>
						<input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" />
					</td>
				</tr>
				<tr>
					<td>
						<label><?=GetMessage('EMAIL')?><span class="starrequired">*</span></label><br/>
						<input type="text" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" />
					</td>
				</tr>
				<tr>
					<td>
						<label><?=GetMessage('USER_PHONE')?><span class="starrequired">*</span></label><br/>
						<input 
							type="tel" 
							name="PERSONAL_PHONE" 
							maxlength="50"
							<?if($arResult["arUser"]["PERSONAL_PHONE_FORMATED"]):?> 
							value="<?=$arResult["arUser"]["PERSONAL_PHONE_FORMATED"]?>"
							placeholder="<?=$arResult["arUser"]["PERSONAL_PHONE_FORMATED"]?>" 
							<?endif;?>
						/>	
					</td>
				</tr>
				<tr>
					<td>
						<label><?=GetMessage('LOGIN')?><span class="starrequired">*</span></label><br/>
						<input type="text" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<h2>пароль</h2>
		<table class="profile-table data-table">
			<tbody>
				<tr>
					<td>
						<label><?=GetMessage('NEW_PASSWORD_REQ')?></label><br/>
						<input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="bx-auth-input" />
					</td>
				</tr>
				<tr>
					<td>
						<label><?=GetMessage('NEW_PASSWORD_CONFIRM')?></label><br/>
						<input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" />
					</td>
				</tr>
			</tbody>
		</table>
		<input type="submit" name="save" value="сохранить настройки профиля">
	</form>
	</div>
