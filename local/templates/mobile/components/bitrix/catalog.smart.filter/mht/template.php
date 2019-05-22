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
	Найти
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
			?>

			<?
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
						$arItem["PROPERTY_TYPE"] == "N" ||
						($isPrice = isset($arItem["PRICE"]))
					){
						if(!isset($arItem['VALUES']['MIN']['VALUE'])){
							continue;
						}
						ob_start();

						$values = array(
							'min' => intval($arItem['VALUES']['MIN']['VALUE']),
							'max' => intval($arItem['VALUES']['MAX']['VALUE']),
							'selected_min' => isset($arItem['VALUES']['MIN']['HTML_VALUE'])?intval($arItem['VALUES']['MIN']['HTML_VALUE']):intval($arItem['VALUES']['MIN']['VALUE']),
							'selected_max' => isset($arItem['VALUES']['MAX']['HTML_VALUE'])?intval($arItem['VALUES']['MAX']['HTML_VALUE']):intval($arItem['VALUES']['MAX']['VALUE']),
						);

						if($isPrice){
							?>
							<div class="filtervalue">
								<div class="filtertype"><p>Стоимость, руб</p></div>
								<div class="filterinput"><div class="fromtowrap">
							<?
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
									'data-from'=>$values['selected_min'],
									'data-to'=>$values['selected_max'],
									'data-min'=>$values['min'],
									'data-max'=>$values['max'],
									// 'onkeyup' => 'smartFilter.keyup(this)',
									// 'onchange' => 'smartFilter.keyup(this)'
								));

								if($typeL == 'min'){
									?><div class="fromwrap">
			                            <label>от</label>
			                            <input class="gtxfrom" <?=$attr?>>
			                        </div><?
								}elseif($typeL == 'max'){
									?><div class="towrap">
			                            <label>до</label>
			                            <input class="gtxto" <?=$attr?>>
			                        </div><?
								}
							}
							?></div><div class="rangewrap">
										<input type="text">
									</div></div></div><?
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

					if(count($arItem['VALUES']) < 2){
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
					?><div class="filtervalue">
						<div class="filtertype"><p><?=$arItem['NAME']?></p></div>
						<div class="filterinput">
							<div class="selectwrap">
								<?
								/*echo '<pre>';
								print_r($arItem);
								echo '</pre>';*/
								?>
								<select class="select-box">
									<option value="">все</option>
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
								<input type="hidden">
							</div>
						</div>
					</div><?
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
							*//*?>
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

			<input type="hidden" id="set_filter" name="set_filter" value="Подобрать" >

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
		<?/*<div class="search" onclick="$('#filter_form').submit();">Найти</div>*/?>
		<a href="#" onclick="$('#filter_form').submit();" class="searchthis">Найти</a>
	</form>
	<script>
		var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>');
	</script>
</div>