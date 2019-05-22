<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	
	$this->setFrameMode(true);

    foreach($arResult["HIDDEN"] as $arItem){
        echo '<input'.WP::attr(array(
            'type' => 'hidden',
            'name' => $arItem["CONTROL_NAME"],
            'value' => $arItem["HTML_VALUE"],
            'id' => $arItem["CONTROL_ID"]
        )).'/>';
    }

    $htmls = array(
        'show' => '',
        'hide' => ''
    );

    $itemsToShow = array(
        'PROP_TYPE',
        'BRAND',
        'PROP_GLOB_TYPE'
    );

    $showMore = false;


    foreach($arResult["ITEMS"] as $arItem){
        if($arItem['PRICE']) continue;
        //if ($arItem['CODE']!='CML2_MANUFACTURER') continue;

        $anyChecked = false;
        if(empty($arItem["VALUES"])){
            continue;
        }



        $allDisabled = true;
        foreach($arItem['VALUES'] as $val => $ar){
            if(!$ar['DISABLED']){
                $allDisabled = false;
            }
            if($ar["CHECKED"]){
                $anyChecked = true;
            }
        }

        if($allDisabled){
            continue;
        }

        //ob_start();

            //echo'<pre>';print_r($arItem["NAME"].' - '.$arItem["DISPLAY_TYPE"]);echo"</pre>";

            $arCur = current($arItem["VALUES"]);
            switch ($arItem["DISPLAY_TYPE"])
            {
                case 'A':
                case 'B':

                    ?><p class="heading"><?=$arItem['NAME']?></p><?

                    $values = array(
                        'min' => intval($arItem['VALUES']['MIN']['VALUE']),
                        'max' => round($arItem['VALUES']['MAX']['VALUE']),
                    );

                    ?><div class="filter_block_bottom_col1 filter-numbers-fields"><?
                    foreach(array(
                                array('min', 'От'),
                                array('max', 'До')
                            ) as $a){
                        list($typeL, $typeName) = $a;
                        $typeU = strtoupper($typeL);
                        $id = $typeL.$arItem['CODE'];
                        $selValue = intval($arItem['VALUES'][$typeU]['HTML_VALUE']);
                        if($selValue){
                            $anyChecked = true;
                        }

                        $attr = WP::attr(array(
                            'class' => 'cost-'.$typeL,
                            'value' => $selValue ? $selValue : $values[$typeL],
                            'data-sel-value' => $values[$typeL],
                            'name' => $arItem["VALUES"][$typeU]["CONTROL_NAME"],
                            'type' => 'text',
                            'id' => $id,
                        ));

                        if($typeL == 'min'){
                            ?>
                            <div class="range_col1">
                                <div class="filter_title"> от</div>
                                <input <?=$attr?>>
                            </div>
                            <?
                        }
                        elseif($typeL == 'max'){
                            ?>
                            <div class="range_col3">
                                <div class="filter_title">до</div>
                                <input <?=$attr?>>
                            </div>
                           <?
                        }

                    }
                    ?>
                    <div class="clearfix"></div>
                        <div class="range_col2">
                            <div class="cost_range"></div>
                        </div>

                    <?

                    ?></div><?

                break;

            default://CHECKBOXES

                $vals = array('true','false');
                foreach ($arItem['VALUES'] as $value)
                    if(in_array($value['URL_ID'], $vals))
                        $yesno = true;
                    else
                        $yesno = false;


                if(count($arItem['VALUES']) < 2 && !$arItem['VALUES']['MIN']) {

                    $ar = array_shift($arItem['VALUES']);

                    ?>

                    <ul class="heading">
                        <li <?= $ar['CHECKED'] ? 'class="active"' : '' ?>><a
                                    href="?<?= $ar["CONTROL_NAME"] ?>=Y<?= $sDopGetParams ?>&set_filter=Подобрать"
                                    data-filter="<?= $ar["CONTROL_NAME"] ?>"
                                    class="filter-page">
                                <label><?= $arItem['NAME'] ?> <?= ($ar['ELEMENT_COUNT'] ? "[" . $ar['ELEMENT_COUNT'] . "]" : '') ?></label>
                            </a></li>
                    </ul>

                    <?

                }  else {

                    $bHiddenBlockShown = false;
                    $iItemsCount = count($arItem['VALUES']);
                    ?>
                    <p class="heading"><?=$arItem['NAME']?></p>
                    <ul>
                        <?
                        $k=0;
                        foreach($arItem['VALUES'] as $val => $ar){

                        if ( isset($ar['ELEMENT_COUNT']) && $ar['ELEMENT_COUNT']<=0 ) continue;


                        $sDopGetParams = "";
                        if (isset($_GET['arrFilter_P1_MIN']) && intval($_GET['arrFilter_P1_MIN'])>0){
                            $sDopGetParams.= '&arrFilter_P1_MIN='.intval($_GET['arrFilter_P1_MIN']);
                        }

                        if (isset($_GET['arrFilter_P1_MAX']) && intval($_GET['arrFilter_P1_MAX'])>0){
                            $sDopGetParams.= '&arrFilter_P1_MAX='.intval($_GET['arrFilter_P1_MAX']);
                        }
                        //arrFilter_P1_MIN=80&arrFilter_P1_MAX=400

                        ?>
                    <?if ($k++>=$arParams['ITEMS_IN_BLOCK']  && !$bHiddenBlockShown){
                    $bHiddenBlockShown = true;
                    ?><div class="hidden_list"><?} //$GLOBALS['APPLICATION']->GetCurPage().?>



                            <li <?=$ar['CHECKED']?'class="active"':''?>><a href="?<?=$ar["CONTROL_NAME"]?>=Y<?=$sDopGetParams?>&set_filter=Подобрать" class="filter-page"
                                data-filter="<?= $ar["CONTROL_NAME"] ?>"
                                >
                                    <label><?=$ar["VALUE"]?> <?=($ar['ELEMENT_COUNT']?"[".$ar['ELEMENT_COUNT']."]":'')?></label></a></li>
                            <?
                            }
                            ?><?if ( $bHiddenBlockShown ){?>
                        </div>
                        <li class="showhidden"><a href="javascript:void(0)">Показать скрытые (<?=$iItemsCount-$arParams['ITEMS_IN_BLOCK']?>)</a></li>
                        <li class="hidehidden"><a href="javascript:void(0)">Минимизировать список</a></li>

                    <?}?>
                    </ul>

             <?   } ?>



            <?}



        /*if($anyChecked || in_array($arItem['CODE'], $itemsToShow)){
            $htmls['show'] .= ob_get_clean();
        }
        else{
            $htmls['hide'] .= ob_get_clean();
            $showMore = true;
        }*/
    }
?>

<?//=$htmls['show']?>

<?/*
    if($showMore){
        ?>
            <?=$htmls['hide']?>
        <?
    }*/
?>

<?/*

<input type="submit" name="set_filter" value="Подобрать" id="set_filter_btn" style="display: none">
<input type="submit" name="del_filter" value="Сбросить"  id="del_filter_btn" style="display: none"/>
    if($showMore){
        ?>
            <div class="link-holder" data-ng-hide="tog.get('filter-ext')">
                <a href="#" class="link-more" data-ng-click="tog.sw('filter-ext'); $event.preventDefault()">Расширенный подбор</a>
            </div>
        <?
    }
*/?>