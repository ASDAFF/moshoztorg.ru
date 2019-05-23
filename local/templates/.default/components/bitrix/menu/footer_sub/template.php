<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}

foreach($arResult as $arItem):
	?><li><a href="<?php echo $arItem['LINK'];?>" class="light <?=$arItem['SELECTED']?' selected':''?>"><?php echo $arItem['TEXT'];?></a>
	</li><?
endforeach?>