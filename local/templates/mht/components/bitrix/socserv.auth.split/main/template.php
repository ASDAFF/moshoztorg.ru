<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?
if($arResult['ERROR_MESSAGE'])
	ShowMessage($arResult['ERROR_MESSAGE']);
?>
<script type="text/javascript">
	function ShowTwitDiv(){
		var obTwitterRecipients = document.getElementById('soc-serv-recipients');
		if(obTwitterRecipients.style.display == 'block')
			obTwitterRecipients.style.display = 'none';
		else
			obTwitterRecipients.style.display = 'block'
	}
</script>
<?
$arServices = $arResult["AUTH_SERVICES_ICONS"];
$userIdTwitter = array();
$userIdOther = array();
$showDivTwitter = false;
$arPerm = array();
?>
<div class="soc-serv-main">
<?
if(!empty($arResult["AUTH_SERVICES"]))
{
	?>
		<div class="soc-serv-title-grey">
			<?=GetMessage("SS_GET_COMPONENT_INFO")?>
			<br><br>
		</div>
	<?
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "split",
		array(
			"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
			"CURRENT_SERVICE"=>$arResult["CURRENT_SERVICE"],
			"AUTH_URL"=>$arResult['CURRENTURL'],
			"POST"=>$arResult["POST"],
			"SHOW_TITLES"=>'N',
			"FOR_SPLIT"=>'Y',
			"AUTH_LINE"=>'N',
		),
		$component,
		array("HIDE_ICONS"=>"Y")
	);
	?>
	<?
}

if(isset($arResult["DB_SOCSERV_USER"]) && $arParams["SHOW_PROFILES"] != 'N')
{
	?>
	<div class="soc-serv-my-actives">
		<input type="hidden" name="bEdit" value="N" />
	</div>
	<div class="soc-serv-accounts">
		<table cellspacing="0" cellpadding="8">
			<?
			foreach($arResult["DB_SOCSERV_USER"] as $key => $arUser)
			{
				if(!$icon = htmlspecialcharsbx($arResult["AUTH_SERVICES_ICONS"][$arUser["EXTERNAL_AUTH_ID"]]["ICON"]))
					$icon = 'openid';
				$authID = ($arServices[$arUser["EXTERNAL_AUTH_ID"]]["NAME"]) ? $arServices[$arUser["EXTERNAL_AUTH_ID"]]["NAME"] : $arUser["EXTERNAL_AUTH_ID"];
				if($arUser["EXTERNAL_AUTH_ID"] == "Twitter")
				{
					$showDivTwitter = true;
					$userIdTwitter[] = $arUser["ID"];
					$userPerm = $arUser["PERMISSIONS"];
				}
				else
					$userIdOther[] = $arUser["ID"];
				?>
				<tr class="soc-serv-personal">
					<td>
						<i class="bx-ss-icon <?=$icon?>">&nbsp;</i>
					</td>
					<td>
						<?if ($arUser["PERSONAL_LINK"] != ''):?>
							<a class="soc-serv-link" target="_blank" href="<?=$arUser["PERSONAL_LINK"]?>">
						<?endif;?>
						<?if(intval($arUser["PERSONAL_PHOTO"]) > 0):?>
							<?$file = CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"], array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_EXACT, true);?>
							<?if($file){?>
								<img src="<?=$file["src"]?>">
							<?}?>
						<?endif;?>
						<i class="soc-serv-text"><?=$arUser["VIEW_NAME"]?></i>
						<?if ($arUser["PERSONAL_LINK"] != ''):?>
							</a>
						<?endif;?>
					</td>
					<td>
						<?if (in_array($arUser["ID"], $arResult["ALLOW_DELETE_ID"])):?>
							<a class="split-delete-item" href="?action=delete&user_id=<?=$arUser["ID"]."&".bitrix_sessid_get()?>" onclick="return confirm('<?=GetMessage("SS_PROFILE_DELETE_CONFIRM")?>')" title=<?=GetMessage("SS_DELETE")?>>&times;</a>
						<?endif;?>
					</td>
				</tr>
				<?
			}
			?>

		</table>
	</div>
	<?if($showDivTwitter):?>
	<div class="soc-serv-title-grey">
		<?if(COption::GetOptionString("socialservices", "get_message_from_twitter", "N") == 'Y'):?>
		<br><?=str_replace("#hash#", $arResult["TWIT_HASH"], GetMessage("SS_SEND_MESSAGE_TO"))."  "?><a href="javascript:void(0)" onclick="ShowTwitDiv()"><?=GetMessage("SS_TO_RECIPIENTS")?></a>
			<div id="soc-serv-recipients">
		<?
		$APPLICATION->IncludeComponent(
			"bitrix:main.post.form",
			"",
			$formParams = Array(
				"FORM_ID" => "bx_user_profile_form",
				"SHOW_MORE" => "Y",
				"PARSER" => Array("Bold", "Italic", "Underline", "Strike", "ForeColor",
					"FontList", "FontSizeList", "RemoveFormat", "Quote", "Code",
					"MentionUser",
				),
				"BUTTONS" => Array(
					"MentionUser",
				),
				"DESTINATION" => array(
					"VALUE" => $arResult["PostToShow"]["FEED_DESTINATION"],
					"SHOW" => "Y"
				),
			),
			false,
			Array("HIDE_ICONS" => "Y")
		);
		?>
			</div>
		<?endif;?>
	</div>
	<?
endif;
	?>
	<?
}
if(!empty($arResult["AUTH_SERVICES"]))
{
?>
	</div>
<?
}
if(!empty($userIdTwitter))
{
	foreach($userIdTwitter as $value)
	{
	?>
		<input type="hidden" name="USER_ID_TWITTER[<?=$value?>]" value="<?=$value?>" />
	<?
	}
}
if(!empty($userIdOther))
{
	foreach($userIdOther as $value)
	{
	?>
		<input type="hidden" name="USER_ID_OTHER[<?=$value?>]" value="<?=$value?>" />
	<?
	}
}