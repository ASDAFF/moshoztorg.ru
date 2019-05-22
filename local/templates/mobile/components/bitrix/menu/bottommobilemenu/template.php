<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}
?><!--
        --><ul class="bottommenu"><?
foreach($arResult as $arItem):
	?><li><a href="<?php echo $arItem['LINK'];?>"><?php echo $arItem['TEXT'];?></a></li><?
endforeach?></ul>