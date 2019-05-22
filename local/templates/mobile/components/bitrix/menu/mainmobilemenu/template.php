<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}

?><!--
        --><ul><?
foreach($arResult as $arItem):
	?><li><a href="<?php echo isset($arItem['CHILDREN'])?'javascript:void(0)':$arItem['LINK'];?>"><?php echo $arItem['TEXT'];?></a>
		<?if (isset($arItem['CHILDREN'])):
		?><ul><?
				foreach($arItem['CHILDREN'] as $arSubItem):
					?><li><p><?php echo $arSubItem['TEXT']?></p><i class="flaticon-right"></i>
					<?if (isset($arSubItem['CHILDREN'])):?>
					<ul>
						<?foreach($arSubItem['CHILDREN'] as $k=>$arSubSubItem):?>
							<li><a href="<?php echo $arSubSubItem['LINK']?>"><?php echo $arSubSubItem['TEXT']?></a></li>
							<?if ($k+1>=$arParams['MAX_ITEMS_IN_COL'] && isset($arSubItem['CHILDREN'][$k+1])):?>
								<li><a href="<?php echo $arSubItem['LINK']?>">Все</a></li>
								<?
								break;
							endif?>
						<?endforeach?>
					</ul>
					<?endif?>
				</li><?
				endforeach;
				?></ul><?
	endif?>
	</li><?
endforeach?></ul>