<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	
	$this->setFrameMode(true);

?>

<?/*
<div class="filter-prices">
    <label>Цена:</label>
    <div class="range_col1">

        <div class="filter_title_block">
            <div class="filter_title">от</div>
            <input class="cost-min" value="20" data-sel-value="20" name="arrFilter_P1_MIN" type="text" id="minCost" disabled="disabled">
        </div>
        <div class="filter_title_block">
            <div class="filter_title">до</div>
            <input class="cost-max" value="13283" data-sel-value="13283" name="arrFilter_P1_MAX" type="text" id="maxCost" disabled="disabled">
        </div>

    </div>
    <div class="range_col2">
        <div class="cost_range ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 0%;"></span><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 100%;"></span></div>
    </div>
</div>*/?>

<div class="filter-prices">
    <form <?=WP::attr(array(
		'name' => $arResult["FILTER_NAME"]."_form",
		'action' => $arResult["FORM_ACTION"],
		'method' => 'get',
		'class' => 'filter-form',
		'id' => 'filter_form'
	))?>>
			<?
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

				// __($arResult['ITEMS']);

				foreach($arResult["ITEMS"] as $arItem){
					$anyChecked = false;
					if(empty($arItem["VALUES"])){
						continue;
					}

					if(
						//$arItem["PROPERTY_TYPE"] == "N" ||
						($isPrice = isset($arItem["PRICE"]))
					){
						if(!isset($arItem['VALUES']['MIN']['VALUE'])){
							continue;
						}
						//ob_start();

						$values = array(
							'min' => intval($arItem['VALUES']['MIN']['VALUE']),
							'max' => intval($arItem['VALUES']['MAX']['VALUE']),
						);

						if($isPrice){
							?><div class="range_col1"><?
							foreach(array(
								array('min', 'От'),
								array('max', 'До')
							) as $a){
								list($typeL, $typeName) = $a;
								$typeU = strtoupper($typeL);
								$id = $typeL.($isPrice ? 'Cost' : $arItem['CODE']);
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
									// 'onkeyup' => 'smartFilter.keyup(this)',
									// 'onchange' => 'smartFilter.keyup(this)'
								));

								if($typeL == 'min'){
									?>
                                        <div class="filter_title_block">
			                                <div class="filter_title">от</div>
			                                <input <?=$attr?>>
                                        </div>
			                        <?
								}
								elseif($typeL == 'max'){
									?>
                                        <div class="filter_title_block">
			                                <div class="filter_title">до</div>
			                                <input <?=$attr?>>
                                        </div>
			                        <?
								}
							}
							?></div><div class="range_col2">
                                <div class="cost_range ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
                                    <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div>
                                    <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 0%;"></span>
                                    <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 100%;"></span></div>
                            </div><?
			            }

						

						/*if($isPrice){
							$htmls['show'] = ob_get_clean().$htmls['show'];
						}
						else{
							if($anyChecked || in_array($arItem['CODE'], $itemsToShow)){
								$htmls['show'] .= ob_get_clean();
							}
							else{
								$htmls['hide'] .= ob_get_clean();
								$showMore = true;
							}
						}*/
						continue;
					}

					if(count($arItem['VALUES']) < 2 && !$arItem['VALUES']['MIN']){
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


					/*

					ob_start();
                        $arCur = current($arItem["VALUES"]);
                        switch ($arItem["DISPLAY_TYPE"])
                        {
                        default://CHECKBOXES

                            $vals = array('true','false');
                            foreach ($arItem['VALUES'] as $value)
                                if(in_array($value['URL_ID'], $vals))
                                    $yesno = true;
                                else
                                    $yesno = false;

                            if(count($arItem['VALUES']) == 2 && $yesno) {
                                foreach ($arItem['VALUES'] as $value) {
                                    if ($value['URL_ID'] == 'true') {
                                        ?>
                                        <div class="filter_block_bottom_col2">
                                            <div class="js-select-filter-helper">
                                                <label>
                                                <input type="checkbox" class="filter-cleared" name="<?= $value["CONTROL_NAME"] ?>"
                                                       value="<?= $value["HTML_VALUE"] ?>" <?=$value["CHECKED"]?"checked":""?> <?=$value["DISABLED"]?"disabled":""?>>
                                                    <?= $arItem['NAME'] ?>
                                                </label>
                                            </div>
                                        </div>
                                        <?
                                    }
                                }
                            }else{

                                ?>
                                <div class="filter_block_bottom_col2">
                                    <div class="js-select-filter-helper">
                                        <input type="hidden"/>
                                        <div class="filter_title"><?=$arItem['NAME']?></div>
                                        <select>
                                            <option value="">все</option>
                                            <?foreach($arItem['VALUES'] as $val => $ar){
                                                $v = trim($ar["VALUE"], '- ');
                                                if($v == 'N'){
                                                    $v = 'Нет';
                                                }
                                                elseif($v == 'Y'){
                                                    $v = 'Да';
                                                }

                                                ?>
                                                <option <?=WP::attr(array(
                                                    'type' => 'checkbox',
                                                    'value' => $ar["HTML_VALUE"],
                                                    'data-name' => $ar["CONTROL_NAME"],
                                                    // 'id' => $ar["CONTROL_ID"],
                                                    // 'onchange' => 'smartFilter.click(this)',
                                                    'selected' => $ar["CHECKED"] ? 'selected' : null,
                                                    'disabled' => (!$ar['CHECKED'] && $ar['DISABLED']) ? 'disabled' : null
                                                ))?>><?=$v?></option>
                                                input
                                                <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?
                            }
                        }




					if($anyChecked || in_array($arItem['CODE'], $itemsToShow)){
						$htmls['show'] .= ob_get_clean();
					}
					else{
						$htmls['hide'] .= ob_get_clean();
						$showMore = true;
					}*/


                $arCur = current($arItem["VALUES"]);
                switch ($arItem["DISPLAY_TYPE"])
                {
                    default://CHECKBOXES

                        if ($arItem['CODE'] == 'CML2_MANUFACTURER'){
                            foreach ($arItem['VALUES'] as $val => $ar){

                            if ($ar['CHECKED']){
                                ?><input type="hidden" name="<?= $ar["CONTROL_NAME"] ?>" value="Y"><?
                            }


                            }
                        }

                }


            }
			?>

			<input type="submit" name="set_filter" value="Подобрать" id="set_filter_btn" style="display: none">
            <input type="submit" name="del_filter" value="Сбросить"  id="del_filter_btn" style="display: none"/>

        <?/*if (isset($_GET['arrFilter_P1_MIN']) && intval($_GET['arrFilter_P1_MIN'])>0){
            $sDopGetParams.= '&arrFilter_P1_MIN='.intval($_GET['arrFilter_P1_MIN']);
            ?><input type="hidden" name="arrFilter_P1_MIN" value="<?=intval($_GET['arrFilter_P1_MIN'])?>"><?
        }

        if (isset($_GET['arrFilter_P1_MAX']) && intval($_GET['arrFilter_P1_MAX'])>0){
            $sDopGetParams.= '&arrFilter_P1_MAX='.intval($_GET['arrFilter_P1_MAX']);
            ?><input type="hidden" name="arrFilter_P1_MAX" value="<?=intval($_GET['arrFilter_P1_MAX'])?>" ><?
        }*/
        ?>


        <?/*

			<div class="modef" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?>>
				<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
				<a href="<?echo $arResult["FILTER_URL"]?>" class="showchild"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
				<!--<span class="ecke"></span>-->
			</div>

        <div class="reset-wrap">
            <a class="reset" href="javascript:;" onclick="$('#del_filter_btn').click();">
            Сбросить
            </a>
        </div>
		<div class="search" onclick="$('#set_filter_btn').click();">Найти</div>*/?>
	</form></div>