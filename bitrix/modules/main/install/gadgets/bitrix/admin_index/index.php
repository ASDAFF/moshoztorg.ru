<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (strlen($arGadgetParams["MODE"]) <= 0 || !in_array($arGadgetParams["MODE"], array("LIST", "ICON")))
	$arGadgetParams["MODE"] = "ICON";

$APPLICATION->SetAdditionalCSS("/bitrix/themes/".ADMIN_THEME_ID."/index.css");

?>
<style type="text/css">
td.gd-index-section-container {width:110px; padding:0px 10px 0px 0px; background-position:right top; background-repeat:repeat-y; background-image:url(/bitrix/themes/.default/images/dots_index.gif);}
div.gd-index-section-icon {width:44px; height:44px; background-repeat:no-repeat;}
div.gd-index-section-text {font-size:70%; font-weight:bold; margin-top:4px;}
td.gd-index-section-container a, td.gd-index-section-container a:visited {color:black!important; text-decoration:none!important;font-size:100%!important;}
td.gd-index-section-container a:hover {color:black; text-decoration:underline;}
td.gd-index-items-container {padding-left:12px;}
div.gd-index-section-line {height:30px; overflow:hidden;}

/*list mode*/
div.gd-index-item-container {float:left; width:180px; margin:0px 0px 4px 0px;}
div.gd-index-item-icon {float:left; margin:0px 2px 0px 0px; height:17px; width:17px; background-repeat:no-repeat; background-position:left center; box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box;}
div.gd-index-item-block {height:17px; float:left; font-size:70%; padding:2px 0px 0px 0px; white-space:nowrap; box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box;}
div.gd-index-item-container a, div.gd-index-item-container a:visited {color:black!important; text-decoration:none!important;font-size:100%!important;}
div.gd-index-item-container a:hover {color:black!important; text-decoration:underline!important;}

/*icons mode*/
div.gd-index-icon-container {float:left; height:60px; margin:0px 10px 10px 0px; overflow:hidden;}
div.gd-index-icon-icon {width:34px; height:34px; background-repeat:no-repeat;}
div.gd-index-icon-text {width:100px; font-family:Tahoma,Verdana,Arial,helvetica,sans-serif; font-size:70%; overflow: hidden; -o-text-overflow: ellipsis; text-overflow: ellipsis;}
div.gd-index-icon-container a, div.gd-index-icon-container a:visited {color:black!important; text-decoration:none!important;font-size:100%!important;}
div.gd-index-icon-container a:hover {color:black!important; text-decoration:underline!important;}
</style>
<?
global $adminMenu;

if($arGadgetParams["MODE"] <> "LIST"):
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?><div id="index_page_result_div"><?
endif;
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?
$i=0;
foreach($adminMenu->aGlobalMenu as $menu):
	?>
	<?if($i>0):?>
		<tr>
			<td><div class="gd-index-section-line">&nbsp;</div></td>
			<td></td>
		</tr>
	<?endif;?>
	<tr valign="top">
		<td align="center" class="gd-index-section-container">
			<a href="<?echo $menu["url"]?>" title="<?echo $menu["title"]?>">
				<div class="gd-index-section-icon" id="<?echo $menu["index_icon"]?>"></div>
				<div class="gd-index-section-text"><?echo $menu["text"]?></div>
			</a>
		</td>
		<td class="gd-index-items-container">
		<?
		foreach($menu["items"] as $submenu):
			if($arGadgetParams["MODE"] == "LIST"):
				?>
				<div class="gd-index-item-container">
				<?if($submenu["url"] <> ""):?>
					<a href="<?echo $submenu["url"]?>" title="<?echo $submenu["title"]?>"><div class="gd-index-item-icon" id="<?echo $submenu["icon"]?>"></div></a>
					<div class="gd-index-item-block"><a href="<?echo $submenu["url"]?>" title="<?echo $submenu["title"]?>"><?echo $submenu["text"]?></a></div>
				<?else:?>
					<div class="gd-index-item-icon" id="<?echo $submenu["icon"]?>"></div>
					<div class="gd-index-item-block"><?echo $submenu["text"]?></div>
				<?endif?>
				</div>
				<?
			else: //icon
				?>
				<div class="gd-index-icon-container" align="center">
				<?if($submenu["url"] <> ""):?>
					<a href="<?echo $submenu["url"]?>" title="<?echo $submenu["title"]?>">
						<div class="gd-index-icon-icon" id="<?echo $submenu["page_icon"]?>"></div>
						<div class="gd-index-icon-text"><?echo $submenu["text"]?></div>
					</a>
				<?else:?>
						<div class="gd-index-icon-icon" id="<?echo $submenu["page_icon"]?>"></div>
						<div class="gd-index-icon-text"><?echo $submenu["text"]?></div>
				<?endif;?>
				</div>
				<?
			endif;
		endforeach;
		?>
		</td>
	</tr>
	<?
	$i++;
endforeach;
?>
</table>
<?
if($arGadgetParams["MODE"] <> "LIST")
	echo '</div>';
?>