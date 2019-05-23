<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}

foreach($arResult as $arItem):
?><li class="hidden-xs"><a href="<?php echo $arItem['LINK'];?>" class="<?=$arItem['TEXT']=='Спецпредложение'?' sale ':' dark '?><?=$arItem['SELECTED']?' selected':''?>"><?php echo $arItem['TEXT'];?></a>
	</li><?
endforeach?>
