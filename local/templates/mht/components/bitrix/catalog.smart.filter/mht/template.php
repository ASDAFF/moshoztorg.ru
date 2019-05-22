<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	
	$this->setFrameMode(true);
/*
	if(!count($arResult['ITEMS'])){
		return;
	}

	$bad = true;
	foreach($arResult['ITEMS'] as $item){
		if($item['PRICE']){
			continue;
		}

		if(count($item['VALUES']) > 1){
			$bad = false;
			break;
		}
	}

	if($bad){
		return;
	}

*/
?>

<div class="filter_block_middle">
	Расширенный поиск
</div>
<div class="filter_block_bottom">
	<form <?=WP::attr(array(
		'name' => $arResult["FILTER_NAME"]."_form",
		'action' => $arResult["FORM_ACTION"],
		'method' => 'get',
		'class' => 'filter-form',
		'id' => 'filter_form'
	))?>>
		<div class="filter_block_bottom_line1">
			<?/*
			<div class="total-found">
				<a href="#">
					Найдено: <div class="number"></div>
				</a>
			</div>
			*/?>
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
						ob_start();

						$values = array(
							'min' => intval($arItem['VALUES']['MIN']['VALUE']),
							'max' => intval($arItem['VALUES']['MAX']['VALUE']),
						);

						if($isPrice){
							?><div class="filter_block_bottom_col1 filter-prices"><?
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
									?><div class="range_col1">
			                            <div class="filter_title">стоимость от</div>
			                            <input <?=$attr?>>
			                        </div><div class="range_col2">
			                            <div class="filter_title">&nbsp;</div>
			                            <div class="cost_range"></div>
			                        </div><?
								}
								elseif($typeL == 'max'){
									?><div class="range_col3">
			                            <div class="filter_title">до</div>
			                            <input <?=$attr?>>
			                        </div><?
								}
							}
							?></div><?
			            }

						

						if($isPrice){
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
						}
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

					ob_start();
                    /*
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
					<?*/

                        //echo'<pre>';print_r($arItem["NAME"].' - '.$arItem["DISPLAY_TYPE"]);echo"</pre>";

                        $arCur = current($arItem["VALUES"]);
                        switch ($arItem["DISPLAY_TYPE"])
                        {
                        case "A"://NUMBERS_WITH_SLIDER

                            $values = array(
                                'min' => intval($arItem['VALUES']['MIN']['VALUE']),
                                'max' => round($arItem['VALUES']['MAX']['VALUE']),
                            );

                            ?><div class="filter_block_bottom_col1 filter-numbers"><?
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

                                if($typeL == 'min'){?>
                                    <div><?=$arItem["NAME"]?></div>
                                    <div class="range_col1">
                                    <div class="filter_title"> от</div>
                                    <input <?=$attr?>>
                                    </div><div class="range_col2">
                                        <div class="filter_title">&nbsp;</div>
                                        <div class="cost_range"></div>
                                    </div><?
                                }
                                elseif($typeL == 'max'){
                                    ?><div class="range_col3">
                                    <div class="filter_title">до</div>
                                    <input <?=$attr?>>
                                    </div><?
                                }
                            }
                            ?></div><?

                        break;
                        case "B"://NUMBERS
                        ?>
                            <div class="col-xs-6 bx-filter-parameters-box-container-block bx-left">
                                <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_FROM")?></i>
                                <div class="bx-filter-input-container">
                                    <input
                                        class="min-price"
                                        type="text"
                                        name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                        id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
                                        value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
                                        size="5"
                                        onkeyup="smartFilter.keyup(this)"
                                    />
                                </div>
                            </div>
                            <div class="col-xs-6 bx-filter-parameters-box-container-block bx-right">
                                <i class="bx-ft-sub"><?=GetMessage("CT_BCSF_FILTER_TO")?></i>
                                <div class="bx-filter-input-container">
                                    <input
                                        class="max-price"
                                        type="text"
                                        name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                        id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
                                        value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
                                        size="5"
                                        onkeyup="smartFilter.keyup(this)"
                                    />
                                </div>
                            </div>
                        <?
                        break;
                        case "G"://CHECKBOXES_WITH_PICTURES
                        ?>
                            <div class="bx-filter-param-btn-inline">
                                <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                    <input
                                        style="display: none"
                                        type="checkbox"
                                        name="<?=$ar["CONTROL_NAME"]?>"
                                        id="<?=$ar["CONTROL_ID"]?>"
                                        value="<?=$ar["HTML_VALUE"]?>"
                                        <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                    />
                                    <?
                                    $class = "";
                                    if ($ar["CHECKED"])
                                        $class.= " bx-active";
                                    if ($ar["DISABLED"])
                                        $class.= " disabled";
                                    ?>
                                    <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
                                                <span class="bx-filter-param-btn bx-color-sl">
                                                    <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                        <span class="bx-filter-btn-color-icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                    <?endif?>
                                                </span>
                                    </label>
                                <?endforeach?>
                            </div>
                        <?
                        break;
                        case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
                        ?>
                            <div class="bx-filter-param-btn-block">
                                <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                    <input
                                        style="display: none"
                                        type="checkbox"
                                        name="<?=$ar["CONTROL_NAME"]?>"
                                        id="<?=$ar["CONTROL_ID"]?>"
                                        value="<?=$ar["HTML_VALUE"]?>"
                                        <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                    />
                                    <?
                                    $class = "";
                                    if ($ar["CHECKED"])
                                        $class.= " bx-active";
                                    if ($ar["DISABLED"])
                                        $class.= " disabled";
                                    ?>
                                    <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
                                                <span class="bx-filter-param-btn bx-color-sl">
                                                    <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                        <span class="bx-filter-btn-color-icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                    <?endif?>
                                                </span>
                                                <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                        ?> (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                    endif;?></span>
                                    </label>
                                <?endforeach?>
                            </div>
                        <?
                        break;
                        case "P"://DROPDOWN
                        $checkedItemExist = false;
                        ?>
                            <div class="bx-filter-select-container">
                                <div class="bx-filter-select-block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
                                    <div class="bx-filter-select-text" data-role="currentOption">
                                        <?
                                        foreach ($arItem["VALUES"] as $val => $ar)
                                        {
                                            if ($ar["CHECKED"])
                                            {
                                                echo $ar["VALUE"];
                                                $checkedItemExist = true;
                                            }
                                        }
                                        if (!$checkedItemExist)
                                        {
                                            echo GetMessage("CT_BCSF_FILTER_ALL");
                                        }
                                        ?>
                                    </div>
                                    <div class="bx-filter-select-arrow"></div>
                                    <input
                                        style="display: none"
                                        type="radio"
                                        name="<?=$arCur["CONTROL_NAME_ALT"]?>"
                                        id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
                                        value=""
                                    />
                                    <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                        <input
                                            style="display: none"
                                            type="radio"
                                            name="<?=$ar["CONTROL_NAME_ALT"]?>"
                                            id="<?=$ar["CONTROL_ID"]?>"
                                            value="<? echo $ar["HTML_VALUE_ALT"] ?>"
                                            <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                        />
                                    <?endforeach?>
                                    <div class="bx-filter-select-popup" data-role="dropdownContent" style="display: none;">
                                        <ul>
                                            <li>
                                                <label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
                                                    <? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
                                                </label>
                                            </li>
                                            <?
                                            foreach ($arItem["VALUES"] as $val => $ar):
                                                $class = "";
                                                if ($ar["CHECKED"])
                                                    $class.= " selected";
                                                if ($ar["DISABLED"])
                                                    $class.= " disabled";
                                                ?>
                                                <li>
                                                    <label for="<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" data-role="label_<?=$ar["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')"><?=$ar["VALUE"]?></label>
                                                </li>
                                            <?endforeach?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?
                        break;
                        case "R"://DROPDOWN_WITH_PICTURES_AND_LABELS
                        ?>
                            <div class="bx-filter-select-container">
                                <div class="bx-filter-select-block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
                                    <div class="bx-filter-select-text fix" data-role="currentOption">
                                        <?
                                        $checkedItemExist = false;
                                        foreach ($arItem["VALUES"] as $val => $ar):
                                            if ($ar["CHECKED"])
                                            {
                                                ?>
                                                <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                <span class="bx-filter-btn-color-icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                            <?endif?>
                                                <span class="bx-filter-param-text">
                                                                <?=$ar["VALUE"]?>
                                                            </span>
                                                <?
                                                $checkedItemExist = true;
                                            }
                                        endforeach;
                                        if (!$checkedItemExist)
                                        {
                                            ?><span class="bx-filter-btn-color-icon all"></span> <?
                                            echo GetMessage("CT_BCSF_FILTER_ALL");
                                        }
                                        ?>
                                    </div>
                                    <div class="bx-filter-select-arrow"></div>
                                    <input
                                        style="display: none"
                                        type="radio"
                                        name="<?=$arCur["CONTROL_NAME_ALT"]?>"
                                        id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
                                        value=""
                                    />
                                    <?foreach ($arItem["VALUES"] as $val => $ar):?>
                                        <input
                                            style="display: none"
                                            type="radio"
                                            name="<?=$ar["CONTROL_NAME_ALT"]?>"
                                            id="<?=$ar["CONTROL_ID"]?>"
                                            value="<?=$ar["HTML_VALUE_ALT"]?>"
                                            <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                        />
                                    <?endforeach?>
                                    <div class="bx-filter-select-popup" data-role="dropdownContent" style="display: none">
                                        <ul>
                                            <li style="border-bottom: 1px solid #e5e5e5;padding-bottom: 5px;margin-bottom: 5px;">
                                                <label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
                                                    <span class="bx-filter-btn-color-icon all"></span>
                                                    <? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
                                                </label>
                                            </li>
                                            <?
                                            foreach ($arItem["VALUES"] as $val => $ar):
                                                $class = "";
                                                if ($ar["CHECKED"])
                                                    $class.= " selected";
                                                if ($ar["DISABLED"])
                                                    $class.= " disabled";
                                                ?>
                                                <li>
                                                    <label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')">
                                                        <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                            <span class="bx-filter-btn-color-icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
                                                        <?endif?>
                                                        <span class="bx-filter-param-text">
                                                                    <?=$ar["VALUE"]?>
                                                                </span>
                                                    </label>
                                                </li>
                                            <?endforeach?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?
                        break;
                        case "K"://RADIO_BUTTONS

                            /*?>
                            <div class="radio">
                                <label class="bx-filter-param-label" for="<? echo "all_".$arCur["CONTROL_ID"] ?>">
                                                <span class="bx-filter-input-checkbox">
                                                    <input
                                                        type="radio"
                                                        value=""
                                                        name="<? echo $arCur["CONTROL_NAME_ALT"] ?>"
                                                        id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
                                                        onclick="smartFilter.click(this)"
                                                    />
                                                    <span class="bx-filter-param-text"><? echo GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
                                                </span>
                                </label>
                            </div>
                            */?>
                            <div class="filter_block_bottom_col1">
                                <div><?=$arItem["NAME"]?></div>
                                <?foreach($arItem["VALUES"] as $val => $ar):?>
                                    <div class="radio">
                                        <label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label" for="<? echo $ar["CONTROL_ID"] ?>">
                                            <span class="bx-filter-input-checkbox <? echo $ar["DISABLED"] ? 'disabled': '' ?>">
                                                <input
                                                    class="filter-cleared"
                                                    type="radio"
                                                    value="<? echo $ar["HTML_VALUE_ALT"] ?>"
                                                    name="<? echo $ar["CONTROL_NAME_ALT"] ?>"
                                                    id="<? echo $ar["CONTROL_ID"] ?>"
                                                    <? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
                                                    onclick="smartFilter.click(this)"
                                                />
                                                <span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
                                                    if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                        ?> (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
                                                    endif;?></span>
                                            </span>
                                        </label>
                                    </div>
                                <?endforeach;?>
                            </div>
                        <?
                        break;
                        case "U"://CALENDAR
                        ?>
                            <div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
                                    <?$APPLICATION->IncludeComponent(
                                        'bitrix:main.calendar',
                                        '',
                                        array(
                                            'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
                                            'SHOW_INPUT' => 'Y',
                                            'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
                                            'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
                                            'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                            'SHOW_TIME' => 'N',
                                            'HIDE_TIMEBAR' => 'Y',
                                        ),
                                        null,
                                        array('HIDE_ICONS' => 'Y')
                                    );?>
                                </div></div>
                            <div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
                                    <?$APPLICATION->IncludeComponent(
                                        'bitrix:main.calendar',
                                        '',
                                        array(
                                            'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
                                            'SHOW_INPUT' => 'Y',
                                            'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
                                            'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
                                            'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                                            'SHOW_TIME' => 'N',
                                            'HIDE_TIMEBAR' => 'Y',
                                        ),
                                        null,
                                        array('HIDE_ICONS' => 'Y')
                                    );?>
                                </div></div>
                        <?
                        break;
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




	/*
					?>
						<div class="holder last" id="ul_<?=$arItem["ID"]?>">
							<?/*
								<h3>
									<span>
										<?=$arItem["NAME"]?>
										<?
											if($arItem['HINT']){
												?>
													<span class="inform-on-hover-holder">
														<span class="inform-on-hover">
															<span class="inform-text"><?=$arItem['HINT']?></span>
														</span>
													</span>
												<?
											}
										?>
									</span>
								</h3>
							?>
							<div class="columns filter-check-values">
								<?
									foreach($arItem['VALUES'] as $val => $ar){
										$v = trim($ar["VALUE"], '- ');
										if($v == 'N'){
											$v = 'Нет';
										}
										elseif($v == 'Y'){
											$v = 'Да';
										}
										?>
											<div class="column">
												<div class="row">
													<input <?=WP::attr(array(
														'type' => 'checkbox',
														'value' => $ar["HTML_VALUE"],
														'name' => $ar["CONTROL_NAME"],
														'id' => $ar["CONTROL_ID"],
														// 'onchange' => 'smartFilter.click(this)',
														'checked' => $ar["CHECKED"] ? 'checked' : null,
														'disabled' => (!$ar['CHECKED'] && $ar['DISABLED']) ? 'disabled' : null
													))?>>
													<label for="<?=$ar["CONTROL_ID"]?>" class="<?=$ar['DISABLED'] ? 'disabled' : ''?>"><?=$v?></label>
												</div>
											</div>
										<?
									}
								?>
							</div>
						</div>
					<?
	*/
					if($anyChecked || in_array($arItem['CODE'], $itemsToShow)){
						$htmls['show'] .= ob_get_clean();
					}
					else{
						$htmls['hide'] .= ob_get_clean();
						$showMore = true;
					}
				}
			?>

			<?=$htmls['show']?>

			<?
				if($showMore){
					?>
						<?=$htmls['hide']?>
					<?
				}
			?>

			<input type="submit" name="set_filter" value="Подобрать" id="set_filter_btn" style="display: none">
            <input type="submit" name="del_filter" value="Сбросить"  id="del_filter_btn" style="display: none"/>
			<?/*
				if($showMore){ 
					?>
						<div class="link-holder" data-ng-hide="tog.get('filter-ext')">
							<a href="#" class="link-more" data-ng-click="tog.sw('filter-ext'); $event.preventDefault()">Расширенный подбор</a>
						</div>
					<?
				}
			*/?>

			<div class="modef" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?>>
				<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
				<a href="<?echo $arResult["FILTER_URL"]?>" class="showchild"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
				<!--<span class="ecke"></span>-->
			</div>
		</div>
		<div class="reset-wrap">
            <a class="reset" href="javascript:;" onclick="$('#del_filter_btn').click();">
            Сбросить
            </a>
        </div>
		<div class="search" onclick="$('#set_filter_btn').click();">Найти</div>
	</form>
	<script>
		var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>');
	</script>
</div>