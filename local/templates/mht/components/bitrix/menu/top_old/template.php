<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}

?><nav class="top_menu" xmlns="http://www.w3.org/1999/html"><!--
        --><ul><?
foreach($arResult as $arItem):
	?><li><a href="<?php echo $arItem['LINK'];?>"><?php echo $arItem['TEXT'];?></a>
		<?if (isset($arItem['CHILDREN'])):?>
			<ul class="gtx_secondlevel">
			<?foreach($arItem['CHILDREN'] as $arSubItem):?>
				<li>
			   		<a href="<?php echo $arSubItem['LINK']?>"><div class="gtx_imgholder"><img src="<?php echo $arSubItem['ADDITIONAL_LINKS']?>" alt="<?php echo $arSubItem['NAME']?>"></div>
						<p><?php echo $arSubItem['TEXT']?></p>
					</a>
					<?if (isset($arSubItem['CHILDREN'])):?>
					<div class="gtx_thirdlevel">
						<ul>
						<?foreach($arSubItem['CHILDREN'] as $k=>$arSubSubItem):?>
						<li><a href="<?php echo $arSubSubItem['LINK']?>"><?php echo $arSubSubItem['TEXT']?></a></li>
						<?if ($k+1>=$arParams['MAX_ITEMS_IN_COL'] && isset($arSubItem['CHILDREN'][$k+1])):?> 
								<li class="all"><a href="<?php echo $arSubItem['LINK']?>">Все</a></li>
							<?
							break;
						endif?>
						<?if($k && ($k+1)%9==0):?>
							</ul><ul>				
						<?endif?>
						<?endforeach?>
						</ul>
					</div>
					<?endif?>
				</li>
			<?endforeach;?>
			</ul>
		<?endif?>
	</li><?
endforeach?></ul></nav>