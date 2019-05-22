<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="mainpagesliderwrap">
	<ul class="mainpageslider"><?
        		foreach($arResult['ITEMS'] as $arItem){
        			?>
						<li><a href="<?=$arItem["PROPERTIES"]["URL"]["VALUE"]?>"><img src="<?php echo $arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" width="100%"></a></li>
        			<?
        		}
        	?></ul>
        </div><?
	return;
?>