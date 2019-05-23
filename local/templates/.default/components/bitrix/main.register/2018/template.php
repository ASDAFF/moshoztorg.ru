<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */
 
 /*IT Sphere 2018 Radchenko*/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();


if($USER->IsAuthorized()):

	LocalRedirect('/');
	
else:

	if (count($arResult["ERRORS"]) > 0):
		foreach ($arResult["ERRORS"] as $key => $error)
			if (intval($key) == 0 && $key !== 0) 
				$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

		ShowError(implode("<br /><br />", $arResult["ERRORS"]));

		
		/*
		//добавляем купон за регистрацию
            $coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);

            $addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add(array(
                'DISCOUNT_ID' => 4,
                'ACTIVE' => 'Y',
                'COUPON' => $coupon,
                'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
                'MAX_USE' => 1,
                'USER_ID' => $USER->GetID(),
                'DESCRIPTION' => 'Скидка за регистрацию на сайте',
            ));
            if($addDb->isSuccess()) {
                $arEventFields = array( "COUPON"=>$coupon, "EMAIL"=>$USER->GetEmail(), "USER_ID"=>$USER->GetID() );
                if ( ! \CEvent::Send("ITSFERA_COUPON_FOR_REGISTRATION", 'el', $arEventFields, 'Y', 170)) {
                    \CEventLog::Add(array(
                        "SEVERITY"      => "INFO",
                        "AUDIT_TYPE_ID" => "DEBUG",
                        "MODULE_ID"     => "main",
                        "ITEM_ID"       => 123,
                        "DESCRIPTION"   => "Ошибка отправки сообщения. Скидка за регистрацию на сайте, шаблон 170",
                    ));
                }
            }
			
		*/
		
		
	endif;
	?>

	<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
	<?
	if($arResult["BACKURL"] <> ''):
	?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?
	endif;
	?>

	<div class="registration_page">
		<table>
			<thead>
				<tr>
					<td width="350"></td>
					<td></td>
				</tr>
			</thead>
			        
	<?foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
		
		<tr>
			<td><label><?=GetMessage("REGISTER_FIELD_".$FIELD)?> <?if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"):?><span>*</span><?endif?></label></td>
			<td></td>
		</tr>
					

		<tr>					
			<td>
			
		<?
		switch ($FIELD)
		{
			
			case "PASSWORD":
			?>
			<input type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" />
			<?
			break;
			
			case "CONFIRM_PASSWORD":
			?>
			<input type="password" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" />
			<?
			break;

			default:
			?>
			<input type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" />
			<?
				
		}?></td>
		
			<td>
			
			<?
		switch ($FIELD)
		{
			
			case "PASSWORD":
			?>
			<span class="notis">минимум<br/>6 символов</span>
			<?
			break;
			
			case "CONFIRM_PASSWORD":
			?>
			<span class="notis">минимум<br/>6 символов</span>
			<?
			break;
			
			case "LOGIN":
			?>
			<span class="notis">минимум<br/>3 символа</span>
			<?
			break;

			default:
			?>
			
			<?
				
		}?>
		
			</td>
							
		</tr>
		
	<?endforeach?>
	
	<?
	/* CAPTCHA */
	if ($arResult["USE_CAPTCHA"] == "Y")
	{
		?>
			
		<tr>
			<td colspan="2">
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="text" name="captcha_word" maxlength="50" value="" /></td>
		</tr>
		<?
	}
	/* !CAPTCHA */
	?>
	
		<tr>
			<td class="main2018" colspan="2">
				<? GLOBAL $APPLICATION;
				$APPLICATION->IncludeComponent(
					"itsfera:agreement",
					"2018",
					Array()
				); ?>
			</td>
			
		</tr>
							
			
		<tr>
			<td><a class="button checkagreement" href="#" onclick="if( !$(this).hasClass('disabled') ) { $(this).closest('form').submit(); (window['rrApiOnReady'] = window['rrApiOnReady'] || []).push(function() { rrApi.setEmail($('#email_input_container input').val()); }); } return false;">регистрация</a></td>
			
			
			<td><input type="hidden" value="1" name="register_submit_button" /></td>			
		</tr>
		<tr>
			<td><span class="notis"><span>*</span> Обязательные поля</span></td>
			<td></td>
		</tr>

							
			
	</table>
	
</form>
<?endif?>