<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

    //путь для подключения файлов include-ом
    $CurPath = $_SERVER['DOCUMENT_ROOT'].'/'.SITE_TEMPLATE_PATH;

/**
 * @var array $arParams
 */

CUtil::InitJSCore(array("popup"));

$arAuthServices = $arPost = array();
if(is_array($arParams["~AUTH_SERVICES"]))
{
	$arAuthServices = $arParams["~AUTH_SERVICES"];
}
if(is_array($arParams["~POST"]))
{
	$arPost = $arParams["~POST"];
}

$hiddens = "";
foreach($arPost as $key => $value)
{
	if(!preg_match("|OPENID_IDENTITY|", $key))
	{
		$hiddens .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
	}
}
?>
<script type="text/javascript">
function BxSocServPopup(id)
{
	var content = BX("bx_socserv_form_"+id);
	if(content)
	{
		var popup = BX.PopupWindowManager.create("socServPopup"+id, BX("bx_socserv_icon_"+id), {
			autoHide: true,
			closeByEsc: true,
			angle: {offset: 24},
			content: content,
			offsetTop: 3
		});

		popup.show();

		var input = BX.findChild(content, {'tag':'input', 'attribute':{'type':'text'}}, true);
		if(input)
		{
			input.focus();
		}

		var button = BX.findChild(content, {'tag':'input', 'attribute':{'type':'submit'}}, true);
		if(button)
		{
			button.className = 'btn btn-primary';
		}
	}
}
</script>

<?
foreach($arAuthServices as $service):
	$onclick = ($service["ONCLICK"] <> ''? $service["ONCLICK"] : "BxSocServPopup('".$service["ID"]."')");

?>

			<a id="bx_socserv_icon_<?=$service["ID"]?>" class="<?=$service["ICON"]?> bx-authform-social-icon dark" href="javascript:void(0)" onclick="<?=htmlspecialcharsbx($onclick)?>" title="<?=htmlspecialcharsbx($service["NAME"])?>">

                <?php echo file_get_contents($CurPath.'/svg/'.$service["ICON"].'.svg'); ?>

            </a>
	<?if($service["ONCLICK"] == '' && $service["FORM_HTML"] <> ''):?>
			<div id="bx_socserv_form_<?=$service["ID"]?>" class="bx-authform-social-popup">
				<form action="<?=$arParams["AUTH_URL"]?>" method="post">
					<?=$service["FORM_HTML"]?>
					<?=$hiddens?>
					<input type="hidden" name="auth_service_id" value="<?=$service["ID"]?>" />
				</form>
			</div>
	<?endif?>

<?
endforeach;
?>

