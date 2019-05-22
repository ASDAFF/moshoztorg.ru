<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
	<h1><?=isset($arParams['TITLE']) && !empty($arParams['TITLE'])?$arParams['TITLE']:$arResult["NAME"]?></h1>
	<div class="clear"></div>
<?endif;?>

<div class="brand-container">
	<?if(is_array($arResult["PREVIEW_PICTURE"])):?>
	<div class="leftblock">
		<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
	</div>
	<?endif?>

	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
	<div class="rightblock">
	
		<div class="lead-box" id="brand-leadbox">
			
			<p><?=$arResult["PREVIEW_TEXT"];?></p>		
			
			<?if ( isset( $arResult["DETAIL_TEXT"] ) && !empty( $arResult["DETAIL_TEXT"] )):?>
				<p class="brand-detail">
					<?php echo $arResult["DETAIL_TEXT"];?>
				</p>
			<?endif?>
			
			<?
			/*
			if ($USER->IsAdmin()) {
				echo ('<pre>');
				print_r ($arResult);
			}*/
			
			if (isset($arResult['PROPERTIES']['SEO_TEXT']['VALUE']['TEXT']) && !empty($arResult['PROPERTIES']['SEO_TEXT']['VALUE']['TEXT'])):?>
				<p><?=$arResult['PROPERTIES']['SEO_TEXT']['VALUE']['TEXT']?></p>
			<?endif?>
		</div>	
		<a class="gtread" href="#" onclick="$('#brand-leadbox').animate({height: $('#brand-leadbox').get(0).scrollHeight}, 1000 ).removeClass('lead-box');$('.gtread').hide();$('.gtexpand').show();">Читать далее</a>
		<a class="gtexpand" style="display:none;" href="#" onclick="$('#brand-leadbox').animate({height: 116}, 1000 ).addClass('lead-box');$('.gtread').show();$('.gtexpand').hide()">Свернуть</a>

	</div>
	<?endif;?>
</div>
