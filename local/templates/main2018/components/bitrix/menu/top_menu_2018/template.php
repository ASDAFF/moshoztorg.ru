<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
 global $USER;
if ($USER->IsAdmin()){
?>
<?if (!empty($arResult)):?>
<div class="top-menu-2018-block">
	<div class="top-menu-2018">

		<?
		$pos=count($arResult);
		foreach($arResult as $key => $arItem):
			if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
				continue;
			?>
			<?if($pos==($key+1)):?>
			<div class="phone"><a href="tel:<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div>
			<?else:?>
			<div><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div>
			<?endif?>

			<?endforeach?>

		</div>
		<?endif?>
		<div class="top-menu-2018-clear">
		</div>
	</div>

<?
}
?>