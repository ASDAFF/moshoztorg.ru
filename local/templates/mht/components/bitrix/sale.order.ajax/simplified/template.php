<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	class OrderAjaxFormHTML{
		private $f;
		function __construct($f){
			$this->f = $f;
		}

		function before(){
			$APPLICATION = $this->f->APPLICATION;

			?>
				<form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data">
					<?=bitrix_sessid_post()?>
					<div id="order_form_content">
			<?
		}

		function after(){
			$APPLICATION = $this->f->APPLICATION;
			$arParams = $this->f->arParams;

			?>
					</div>
					<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
					<input type="hidden" name="profile_change" id="profile_change" value="N">
					<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
					<input type="hidden" name="json" value="Y">
					<div class="bx_ordercart_order_pay_center"><a href="javascript:void();" onclick="submitForm('Y'); return false;" class="checkout"><?=GetMessage("SOA_TEMPL_BUTTON")?></a></div>
				</form>
			<?

			if($arParams["DELIVERY_NO_AJAX"] == "N"){
				?>
					<div style="display:none;">
						<?$APPLICATION->IncludeComponent(
							"bitrix:sale.ajax.delivery.calculator",
							"",
							array(),
							null,
							array(
								'HIDE_ICONS' => 'Y'
							)
						); ?>
					</div>
				<?
			}
		}

		function properties(){
			// Информация для оплаты и доставки заказа
			$arResult = $this->f->arResult;
			$hide = true;

			if(
				!is_array($arResult["ORDER_PROP"]["USER_PROFILES"]) ||
				empty($arResult["ORDER_PROP"]["USER_PROFILES"])
			){
				$hide = false;
			}
			else{
				if($arParams["ALLOW_NEW_PROFILE"] == "Y"){
					// Выберите профиль
					$this->profileIDSelect(
						$arResult["ORDER_PROP"]["USER_PROFILES"],
						'Новый профиль'
					);
				}
				else{
					// Профиль покупателя
					if(count($arResult["ORDER_PROP"]["USER_PROFILES"]) == 1){
						foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $profile){
							?>
								<b><?=$profile["NAME"]?></b>
								<?=WP::el('input', array(
									'type' => 'hidden',
									'name' => 'PROFILE_ID',
									'id' => 'ID_PROFILE_ID',
									'value' => $profile["ID"]
								))?>
							<?
						}
					}
					else{
						$this->profileIDSelect($arResult["ORDER_PROP"]["USER_PROFILES"]);
					}
				}
			}

			// Информация о покупателе
			if(
				array_key_exists('ERROR', $arResult) &&
				is_array($arResult['ERROR']) &&
				!empty($arResult['ERROR'])
			){
				$hide = false;
			}

			$name = null;

			if($hide && in_array(
				$_POST['showProps'],
				array(
					'Y',
					'N'
				)
			)){
				$name = ($_POST["showProps"] == "Y") ? 'Свернуть' : 'Развернуть';
			}

			if(!empty($name)){
				?>
					<a href="#" class="slide" onclick="fGetBuyerProps(this); return false;">
						<?=$name?>
					</a>
				<?
			}

			?>
				<input type="hidden" name="showProps" id="showProps" value="N" />
				<div id="sale_order_props" <?=($hide && $_POST["showProps"] != "Y") ? "style='display:none;'" : ''?>>
					<?
						$this->showProperties(
							$arResult["ORDER_PROP"]["USER_PROPS_N"],
							$arParams["TEMPLATE_LOCATION"]
						);
						$this->showProperties(
							$arResult["ORDER_PROP"]["USER_PROPS_Y"],
							$arParams["TEMPLATE_LOCATION"]
						);
					?>
				</div>

				<?
					$this->f->js->fGetBuyerProps();
				?>

				<div style="display:none;">
					<?
						$APPLICATION->IncludeComponent(
							"bitrix:sale.ajax.locations",
							$arParams["TEMPLATE_LOCATION"],
							array(
								"AJAX_CALL" => "N",
								"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
								"REGION_INPUT_NAME" => "REGION_tmp",
								"CITY_INPUT_NAME" => "tmp",
								"CITY_OUT_LOCATION" => "Y",
								"LOCATION_VALUE" => "",
								"ONCITYCHANGE" => "submitForm()",
							),
							null,
							array('HIDE_ICONS' => 'Y')
						);
					?>
				</div>
			<?
		}

		private function showProperties($arSource = array(), $locationTemplate = ".default"){
			if (empty($arSource)){
				return;
			}
			foreach ($arSource as $arProperties){
				switch($arProperties["TYPE"]){
					case "CHECKBOX":
						?>
							<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="">

							<div class="bx_block r1x3 pt8">
								<?=$arProperties["NAME"]?>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="bx_sof_req">*</span>
								<?endif;?>
							</div>

							<div class="bx_block r1x3 pt8">
								<input type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y") echo " checked";?>>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>
							</div>

							<div style="clear: both;"></div>
						<?
						break;
						
					case "TEXT":
						?>
						<div class="bx_block r1x3 pt8">
							<?=$arProperties["NAME"]?>
							<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
								<span class="bx_sof_req">*</span>
							<?endif;?>
						</div>

						<div class="bx_block r3x1">
							<input type="text" maxlength="250" size="<?=$arProperties["SIZE1"]?>" value="<?=$arProperties["VALUE"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>">

							<?
							if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
							?>
							<div class="bx_description">
								<?=$arProperties["DESCRIPTION"]?>
							</div>
							<?
							endif;
							?>
						</div>
						<div style="clear: both;"></div><br/>
						<?
						break;
						
					case "SELECT":
						?>
						<br/>
						<div class="bx_block r1x3 pt8">
							<?=$arProperties["NAME"]?>
							<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
								<span class="bx_sof_req">*</span>
							<?endif;?>
						</div>

						<div class="bx_block r3x1">
							<select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
								<?
								foreach($arProperties["VARIANTS"] as $arVariants):
								?>
									<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
								<?
								endforeach;
								?>
							</select>

							<?
							if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
							?>
							<div class="bx_description">
								<?=$arProperties["DESCRIPTION"]?>
							</div>
							<?
							endif;
							?>
						</div>
						<div style="clear: both;"></div>
						<?
						break;
						
					case "MULTISELECT":
						?>
						<br/>
						<div class="bx_block r1x3 pt8">
							<?=$arProperties["NAME"]?>
							<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
								<span class="bx_sof_req">*</span>
							<?endif;?>
						</div>

						<div class="bx_block r3x1">
							<select multiple name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
								<?
								foreach($arProperties["VARIANTS"] as $arVariants):
								?>
									<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
								<?
								endforeach;
								?>
							</select>

							<?
							if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
							?>
							<div class="bx_description">
								<?=$arProperties["DESCRIPTION"]?>
							</div>
							<?
							endif;
							?>
						</div>
						<div style="clear: both;"></div>
						<?
						break;
						
					case "TEXTAREA":
						$rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];
						?>
						<br/>
						<div class="bx_block r1x3 pt8">
							<?=$arProperties["NAME"]?>
							<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
								<span class="bx_sof_req">*</span>
							<?endif;?>
						</div>

						<div class="bx_block r3x1">
							<textarea rows="<?=$rows?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>

							<?
							if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
							?>
							<div class="bx_description">
								<?=$arProperties["DESCRIPTION"]?>
							</div>
							<?
							endif;
							?>
						</div>
						<div style="clear: both;"></div>
						<?
						break;
						
					case "LOCATION":
						$value = 0;
						if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0)
						{
							foreach ($arProperties["VARIANTS"] as $arVariant)
							{
								if ($arVariant["SELECTED"] == "Y")
								{
									$value = $arVariant["ID"];
									break;
								}
							}
						}
						?>
						<div class="bx_block r1x3 pt8">
							<?=$arProperties["NAME"]?>
							<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
								<span class="bx_sof_req">*</span>
							<?endif;?>
						</div>

						<div class="bx_block r3x1">
							<?
							$GLOBALS["APPLICATION"]->IncludeComponent(
								"bitrix:sale.ajax.locations",
								$locationTemplate,
								array(
									"AJAX_CALL" => "N",
									"COUNTRY_INPUT_NAME" => "COUNTRY",
									"REGION_INPUT_NAME" => "REGION",
									"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
									"CITY_OUT_LOCATION" => "Y",
									"LOCATION_VALUE" => $value,
									"ORDER_PROPS_ID" => $arProperties["ID"],
									"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
									"SIZE1" => $arProperties["SIZE1"],
								),
								null,
								array('HIDE_ICONS' => 'Y')
							);
							?>

							<?
							if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
							?>
							<div class="bx_description">
								<?=$arProperties["DESCRIPTION"]?>
							</div>
							<?
							endif;
							?>
						</div>
						<div style="clear: both;"></div>
						<?
						break;
						
					case "RADIO":
						?>
						<div class="bx_block r1x3 pt8">
							<?=$arProperties["NAME"]?>
							<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
								<span class="bx_sof_req">*</span>
							<?endif;?>
						</div>

						<div class="bx_block r3x1">
							<?
							if (is_array($arProperties["VARIANTS"]))
							{
								foreach($arProperties["VARIANTS"] as $arVariants):
								?>
									<input
										type="radio"
										name="<?=$arProperties["FIELD_NAME"]?>"
										id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"
										value="<?=$arVariants["VALUE"]?>" <?if($arVariants["CHECKED"] == "Y") echo " checked";?> />

									<label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label></br>
								<?
								endforeach;
							}
							?>

							<?
							if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
							?>
							<div class="bx_description">
								<?=$arProperties["DESCRIPTION"]?>
							</div>
							<?
							endif;
							?>
						</div>
						<div style="clear: both;"></div>
						<?
						break;
						
					case "FILE":
						?>
						<br/>
						<div class="bx_block r1x3 pt8">
							<?=$arProperties["NAME"]?>
							<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
								<span class="bx_sof_req">*</span>
							<?endif;?>
						</div>

						<div class="bx_block r3x1">
							<?=showFilePropertyField("ORDER_PROP_".$arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"])?>

							<?
							if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
							?>
							<div class="bx_description">
								<?=$arProperties["DESCRIPTION"]?>
							</div>
							<?
							endif;
							?>
						</div>

						<div style="clear: both;"></div><br/>
						<?
						break;
				}
			}
		}

		function personType(){
			$arResult = $this->f->arResult;

			// Тип плательщика
			if(!empty($arResult["PERSON_TYPE"])){
				foreach($arResult["PERSON_TYPE"] as $v){
					$eid = 'PERSON_TYPE_'.$v["ID"];
					?>
						<input <?=WP::attr(array(
							'type' 		=> 'radio',
							'id' 		=> $eid,
							'name' 		=> 'PERSON_TYPE',
							'value' 	=> $v['ID'],
							'checked' 	=> ($v["CHECKED"]=="Y") ? 'checked' : null,
							'onclick' 	=> 'submitForm()'
						))?>/>
						<label for="<?=$eid?>">
							<?=$v["NAME"]?>
						</label>
					<?
				}
				echo WP::el('input', array(
					'type' => 'hidden',
					'name' => 'PERSON_TYPE_OLD',
					'value' => $arResult["USER_VALS"]["PERSON_TYPE_ID"]
				));
				return;
			}

			if(intval($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0){
				?>
					<span style="display:none;">
						<?
							$ptid = intval($arResult["USER_VALS"]["PERSON_TYPE_ID"]);
							foreach(array(
								'PERSON_TYPE',
								'PERSON_TYPE_OLD'
							) as $name){
								echo WP::el('input', array(
									'type' => 'text',
									'name' => $name,
									'value' => $ptid
								));
							}
						?>
					</span>
				<?
				return;
			}

			foreach($arResult["PERSON_TYPE"] as $v){
				foreach(array(
					'PERSON_TYPE',
					'PERSON_TYPE_OLD'
				) as $name){
					echo WP::el('input', array(
						'type' => 'hidden',
						'name' => $name,
						'value' => $v['ID']
					));
				}
			}
		}

		// private

		private function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000){
			$res = "";

			if (!is_array($values) || empty($values))
				$values = array(
					"n0" => 0,
				);

			if ($property_fields["MULTIPLE"] == "N")
			{
				$res = "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
			}
			else
			{
				$res = '
				<script type="text/javascript">
					function addControl(item)
					{
						var current_name = item.id.split("[")[0],
							current_id = item.id.split("[")[1].replace("[", "").replace("]", ""),
							next_id = parseInt(current_id) + 1;

						var newInput = document.createElement("input");
						newInput.type = "file";
						newInput.name = current_name + "[" + next_id + "]";
						newInput.id = current_name + "[" + next_id + "]";
						newInput.onchange = function() { addControl(this); };

						var br = document.createElement("br");
						var br2 = document.createElement("br");

						BX(item.id).parentNode.appendChild(br);
						BX(item.id).parentNode.appendChild(br2);
						BX(item.id).parentNode.appendChild(newInput);
					}
				</script>
				';

				$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
				$res .= "<br/><br/>";
				$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[1]\" id=\"".$name."[1]\" onChange=\"javascript:addControl(this);\"></label>";
			}
			return $res;
		}

		private function profileIDSelect($profiles, $emptyName = null){
			?>
				<select
					name="PROFILE_ID"
					id="ID_PROFILE_ID"
					onchange="SetContact(this.value)"
				>
					<?
						if(!empty($emptyName)){
							?>
								<option value="0">
									<?=$emptyName?>
								</option>
							<?
						}
						foreach($profiles as $profile){
							?>
								<option <?=WP::attr(array(
									'value' => $profile["ID"],
									'selected' => ($profile["CHECKED"]=="Y") ? 'selected' : null
								))?>>
									<?=$profile["NAME"]?>
								</option>
							<?
						}
					?>
				</select>
			<?
		}
	}

	class OrderAjaxFormJS{
		private $f;
		function __construct($f){
			$this->f = $f;
		}

		function fGetBuyerProps(){
			?>
				<script type="text/javascript">
					function fGetBuyerProps(el){
						var show = 'Развернуть';
						var hide = 'Свернуть';
						var status = BX('sale_order_props').style.display;
						var startVal = 0;
						var startHeight = 0;
						var endVal = 0;
						var endHeight = 0;
						var pFormCont = BX('sale_order_props');
						pFormCont.style.display = "block";
						pFormCont.style.overflow = "hidden";
						pFormCont.style.height = 0;
						var display = "";

						if (status == 'none'){
							el.text = 'Свернуть';

							startVal = 0;
							startHeight = 0;
							endVal = 100;
							endHeight = pFormCont.scrollHeight;
							display = 'block';
							BX('showProps').value = "Y";
							el.innerHTML = hide;
						}
						else{
							el.text = 'Развернуть';

							startVal = 100;
							startHeight = pFormCont.scrollHeight;
							endVal = 0;
							endHeight = 0;
							display = 'none';
							BX('showProps').value = "N";
							pFormCont.style.height = startHeight+'px';
							el.innerHTML = show;
						}

						(new BX.easing({
							duration : 700,
							start : { opacity : startVal, height : startHeight},
							finish : { opacity: endVal, height : endHeight},
							transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),
							step : function(state){
								pFormCont.style.height = state.height + "px";
								pFormCont.style.opacity = state.opacity / 100;
							},
							complete : function(){
									BX('sale_order_props').style.display = display;
									BX('sale_order_props').style.height = '';
							}
						})).animate();
					}
				</script>
			<?
		}

		function scrollUp(){
			?>
				<script type="text/javascript">
					top.BX.scrollToNode(top.BX('ORDER_FORM'));
				</script>
			<?
		}

		function main(){
			CJSCore::Init(array(
				'fx',
				'popup',
				'window',
				'ajax'
			));
			?>
				<noscript>
					Для работы формы заказа требуется <a href="http://www.enable-javascript.com/ru/">включить JavaScript</a>.
				</noscript>
			<?
		}

		function ajaxAfter(){
			?>
				<script type="text/javascript">
					top.BX('confirmorder').value = 'Y';
					top.BX('profile_change').value = 'N';
				</script>
			<?
		}

		function submitForm(){
			?>
				<script type="text/javascript">
					function submitForm(val){
						if(val != 'Y'){
							BX('confirmorder').value = 'N';
						}

						var orderForm = BX('ORDER_FORM');
						BX.showWait();
						BX.ajax.submit(orderForm, ajaxResult);

						return true;
					}

					function ajaxResult(res){
						try{
							var json = JSON.parse(res);
							BX.closeWait();

							if(json.error){
								return;
							}

							if(json.redirect){
								window.top.location.href = json.redirect;
							}
						}
						catch(e){
							BX('order_form_content').innerHTML = res;
						}

						BX.closeWait();
					}

					function SetContact(profileId){
						BX("profile_change").value = "Y";
						submitForm();
					}
				</script>
			<?
		}
	}

	class OrderAjaxForm{
		// public

		public
			$APPLICATION,
			$USER,
			$arParams,
			$arResult,

			$js,
			$html;

		function __construct($APPLICATION, $USER, $arParams, $arResult){
			$this->APPLICATION = $APPLICATION;
			$this->arParams = $arParams;
			$this->arResult = $arResult;
			$this->USER = $USER;

			$this->js = new OrderAjaxFormJS($this);
			$this->html = new OrderAjaxFormHTML($this);
		}

		function isAjax(){
			return $_POST["is_ajax_post"] == "Y";
		}

		function isAutoRegDisabled(){
			$USER = $this->USER;
			$arParams = $this->arParams;

			return
				!$USER->IsAuthorized() &&
				$arParams["ALLOW_AUTO_REGISTER"] == "N";
		}


		function show($type){
			switch($type){
				case 'person_type':
					$this->html->personType();
					break;

				case 'props':
					$this->html->properties();
					break;
			}
		}

		function isConfirmedAndNeedRedirect(){
			return
				$arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" &&
				$arResult["NEED_REDIRECT"] == "Y";
		}

		function showErrors(){
			$arResult = $this->arResult;

			if(!empty($arResult["ERROR"])){
				foreach($arResult["ERROR"] as $v){
					echo ShowError($v);
				}
			}
			elseif(!empty($arResult["OK_MESSAGE"])){
				foreach($arResult["OK_MESSAGE"] as $v){
					echo ShowNote($v);
				}
			}
		}

		function checkRedirect(){
			$APPLICATION = $this->APPLICATION;
			$arResult = $this->arResult;

			if(
				$this->isAutoRegDisabled() ||
				!$this->isConfirmedAndNeedRedirect() ||
				!(strlen($arResult["REDIRECT_URL"]) > 0)
			){
				return;
			}

			$APPLICATION->RestartBuffer();
			?>
				<script type="text/javascript">
					window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
				</script>
			<?
			die();
		}

		// private

		private function getColumnName($arHeader){
			return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_".$arHeader["id"]);
		}

		private function cmpBySort($array1, $array2){
			if (!isset($array1["SORT"]) || !isset($array2["SORT"])){
				return -1;
			}
			if ($array1["SORT"] > $array2["SORT"]){
				return 1;
			}
			if ($array1["SORT"] < $array2["SORT"]){
				return -1;
			}
			if ($array1["SORT"] == $array2["SORT"]){
				return 0;
			}
		}
	}

	$form = new OrderAjaxForm(
		$APPLICATION,
		$USER,
		$arParams,
		$arResult
	);

	$form->checkRedirect();
	$form->js->main();

	if($form->isAutoRegDisabled()){
		$form->showErrors();
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php"); // хз что это такое блять
		return;
	}

	if($form->isConfirmedAndNeedRedirect()){
		if(strlen($arResult["REDIRECT_URL"]) == 0){
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
		}
		return;
	}

	$form->js->submitForm();

	if($form->isAjax()){
		$APPLICATION->RestartBuffer();
	}
	else{
		$form->html->before();
	}

	if($arResult["USER_VALS"]["FINAL_STEP"] == "Y"){
		$form->showErrors();
		$form->js->scrollUp();
	}

	$files = array(
		0 => 'person_type',
		1 => 'props',
		2 => 'delivery',
		3 => 'paysystem',
		4 => 'related_props',
		5 => 'summary',
	);

	if($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d"){
		$files[2] = 'paysystem';
		$files[3] = 'delivery';
	}

	foreach($files as $name){
		$form->show($name);
		//include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/$name.php");
	}

	if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0){
		echo $arResult["PREPAY_ADIT_FIELDS"];
	}

	if($form->isAjax()){
		$form->js->ajaxAfter();
		die();
	}

	$form->html->after();

?>
