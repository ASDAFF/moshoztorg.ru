<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//myPrintR( $arResult , __FILE__, __LINE__ );
?>
<a href="javascript:void(0)" class="accorditemheading">Магазины <i class="flaticon-right"></i></a>
<ul>
    <?if (isset($arResult['REGIONS'][1])):?>
    <li>
        <div class="selectwrap">
            <select id="region-select">
                <option value=""></option>
                <?foreach($arResult['REGIONS'] as $k=>$arRegion):?>
                <option value="<?php echo $arRegion['code'];?>"><?php echo $arRegion['label'];?></option>
                <?endforeach?>
            </select>
        </div>
    </li>
    <?endif?>
    <li>
        <ul>
            <?
            foreach($arResult['SHOPS'] as $k=>$shop){
                ?><li class="shopitem <?php echo $shop['region'][0]?'region_'.$shop['region'][0]:'';?>">
                <div class="pinblock"></div>
                <p class="shoptitle"><?=$shop['street'].' '.$shop['house_html']?></p>
                <p class="shopphone"><?php echo implode('/',$shop['phones']);?></p>
                <p class="shoptimetitle">время работы</p>
                <p class="shoptime"><?=$shop['time']?></p>
                <a href="/magaziny/#shop<?php echo $shop['id'];?>"  data-shop-id="<?php echo $shop['id'];?>"  data-shop-number="<?php echo $k;?>"  class="shopviewmore">Подробнее</a>
            </li><?
            }
            ?>
        </ul>
    </li>
</ul>