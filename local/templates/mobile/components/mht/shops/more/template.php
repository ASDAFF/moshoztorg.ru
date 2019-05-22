<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//myPrintR( $arResult , __FILE__, __LINE__ );
?>
<section class="padding">
    <div class="mainaccordwrap">
        <div class="accorditem accord_shops">
            <a href="javascript:void(0)" class="accorditemheading">Магазины <i class="flaticon-right"></i></a>
            <ul>
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
                <li>
                    <ul>
                        <?
                        foreach($arResult['SHOPS'] as $k=>$shop){
                            ?><li class="shopitem <?php echo $shop['region'][0]?'region_'.$shop['region'][0]:'';?> <?=$shop['isComingSoon'] ? 'coming-soon' : ''?>">
                            <div class="pinblock"></div>
                            <p class="shoptitle"><?=$shop['street'].' '.$shop['house_html']?></p>
                            <?if(implode('/',$shop['phones'])!=""){?><p class="shopphone"><?php echo implode('/',$shop['phones']);?></p><?}?>
                            <?if($shop['time']!=""){?><p class="shoptimetitle">время работы</p>
                            <p class="shoptime"><?=$shop['time']?></p><?}?>
                            <a href="#"  data-shop-id="<?php echo $shop['id'];?>"  data-shop-number="<?php echo $k;?>"  class="shopviewmore">Подробнее</a>
                            
                            <div class="hidden" data-id="<?=$shop['id']?>">                
                                <div class="ya-taxi-widget"
                                    data-size="s"
                                    data-theme="action"
                                    data-title="Вызвать такси"
                                    data-CLID="moshoztorg"
                                    data-APIKEY="4b6e4fcc5e99490eb6a152fe02dd1927"
                                    data-description=""
                                    data-use-location="true"
                                    data-point-b="<?=round($shop['coords'][1], 6)?>,<?=round($shop['coords'][0], 6)?>">
                                </div>
                            </div>
                        </li><?
                        }
                        ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</section>
<section>
    <div class="map" id="shops_map" style="height: 400px"></div>

    <div class="shopslist">
        <div class="headingline">
            <div class="shoparrowleft">
                <i class="flaticon-left"></i>
            </div>
            <div class="shoparrowright">
                <i class="flaticon-right"></i>
            </div>
        </div>

        <div class="itemscarousel">
            <?foreach($arResult['SHOPS'] as $k=>$shop){?>
            <div class="shop-item" id="shop-item-<?php echo $k;?>" data-shop-id="<?=$shop['id']?>">
                <p class="shopadress"><?=$shop['street'].'<br>'.$shop['house_html']?></p>
                <p class="shopphone"><?php echo implode('/',$shop['phones']);?></p>
                <p class="shoptime">время работы: <span><?=$shop['time']?></span></p>
                <?/*
                <p class="shopabout">О магазине</p>
                <p>Как добраться:</p>
                <p class="shopfrom"><span>От метро Автозаводская:</span> Первый вагон из центра, из стеклянных дверей направо, на углу здания направо и прямо до наземного перехода. Затем через переход и прямо до ул.Сайкина направо;</p>
                <p class="shopfrom"><span>От метро Кожуховская:</span> Остановка "метро Кожуховская", автобус 44 до остановки "Проспект Андропова";</p>
                <p class="shopfrom"><span>От метро Пролетарская:</span> Автобусы 229, 608 до остановки "5-я Кожуховская"</p>
                <p>Возле магазина имеется удобная бесплатная парковка</p>*/?>
                <?if (isset($shop['images'][0])):?>
                    <img src="<?php echo $shop['images'][0];?>" alt="" class="shopfoto">
                <?endif?>
            </div>
            <?}?>
        </div>
    </div>
</section>

<?/*
    <div class="contacts_page">
      <div class="contacts">
        <h1>адреса магазинов</h1>
        </div>
        <div class="map" id="shops_map"></div>
        <div class="contacts">
        <div class="dealers">

     <script>
     	mht.shops = <?
     		echo WP::js($arResult['SHOPS']);
     	?>;
     	mht.shopRegion = "<?=$arResult['ACTIVE_REGION']?>";
     </script>

<div class="area_block">
	<div class="area_block_title">Город</div>
    <div class="select"><div class="select_arrow"></div><input name="area" class="ui-autocomplete-input"></div>
</div><div class="dealers_block <?=$n==6 ? 'nomarg' : ''?>" id="shops">
	<?
		foreach($arResult['SHOPS'] as $shop){
			?><div class="dealer <?=$shop['isComingSoon'] ? 'coming-soon' : ''?>"  <?=WP::getEditElementID(74, $shop['id'], $this, true)?>>
		    	<a href="<?=$shop['link']?>">
		            <div class="dealer_street"><?=$shop['street']?></div>
		            <div class="dealer_build"><?=$shop['house_html']?></div>
                    <span class="dealer_phones">
                        <?
                            foreach($shop['phones'] as $phone){
                                ?>
                                    <div class="dealer_phone">
                                        <?=$phone?>
                                    </div>
                                <?
                            }
                        ?>
                    </span>
		            <div class="dealer_time_title">время работы</div>
		            <div class="dealer_time"><?=$shop['time']?></div>
		            <div class="dealer_map"><span>на карте</span></div>
		        </a>
		    </div><?
		}
	?>
</div>
      </div>
    </div>
	</div>*/?>