<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true){die();}
$arResult = array_filter($arResult ,function($item){
	return $item['TEXT'] == 'Каталог';
});
$arResult = current($arResult);

?><ul><?

foreach($arResult['CHILDREN'] as $arItem):
	?><li>
	<div class="menuicon" style="background-image:url(<?php echo $arItem['ADDITIONAL_LINKS']?>)"></div> 
	<p><?php echo $arItem['TEXT'];?></p><i class="flaticon-right"></i>
		<?if (isset($arItem['CHILDREN'])):?>
			<ul>
			<?foreach($arItem['CHILDREN'] as $k=>$arSubItem):?>
				<li>
					<?/*<div class="gtx_imgholder"><img src="<?php echo $arSubItem['ADDITIONAL_LINKS']?>" alt="<?php echo $arSubItem['NAME']?>"></div>*/?>
			   		<a href="<?php echo $arSubItem['LINK']?>">
						<p><?php echo $arSubItem['TEXT']?></p>
					</a>
				</li>
				<?if ($k>3) break;?>
			<?endforeach;?>
			</ul>
		<?endif?>
	</li><?
endforeach?></ul>
