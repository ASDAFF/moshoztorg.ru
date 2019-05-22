<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["CATEGORIES"])):?>
	<table class="title-search-result">
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
			<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<tr>
				<?if($category_id === "all"):?>
					<td class="title-search-all"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></td>
				<?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
					$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];
				?>
					<td class="title-search-item"><a href="<?echo $arItem["URL"]?>"><?
						/* if (is_array($arElement["PICTURE"])):?>
							<img align="left" src="<?echo $arElement["PICTURE"]["src"]?>" width="<?echo $arElement["PICTURE"]["width"]?>" height="<?echo $arElement["PICTURE"]["height"]?>">
						<?endif; */?>
						<?echo $arItem["NAME"]?>
						
						<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
							<?if($arPrice["CAN_ACCESS"]):?>
								<span class="title-search-price"><span class="catalog-price"><?=number_format(ceil($arPrice["DISCOUNT_VALUE"]), 0, '.', ' ');?> руб.</span></span>
							<?endif;?>
						<?endforeach;?>

						</a>
						<? /* <p class="title-search-preview"><?echo $arElement["PREVIEW_TEXT"];?></p> */ ?>
					</td>
				<?elseif(isset($arItem["ICON"])):?>
					<td class="title-search-item"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></td>
				<?/*else:?>
					<td class="title-search-more"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></td> */?>
				<?endif;?>
			</tr>
			<?endforeach;?>
		<?endforeach;?>
	</table>
<?endif;
?>