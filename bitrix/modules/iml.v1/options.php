<?
#################################################
#        Company developer: IPOL
#        Developers: Nikta Egorov
#        Site: http://www.ipol.com
#        E-mail: om-sv2@mail.ru
#        Copyright (c) 2006-2012 IPOL
#################################################
?>
<?
//Ётот модуль прекрасен (с)
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

$module_id = "iml.v1";
$canWork = CModule::IncludeModuleEx($module_id);
CModule::IncludeModule('sale');

if($canWork == 3){?>
	<table><tr><td colspan='2'>
		<div class="adm-info-message-wrap adm-info-message-red">
		  <div class="adm-info-message">
			<div class="adm-info-message-title"><?=GetMessage('IPOLIML_OTHR_DEMOOVER_HDR')?></div>
				<?=GetMessage('IPOLIML_OTHR_DEMOOVER_DESCR')?>
			<div class="adm-info-message-icon"></div>
		  </div>
		</div>
	</td></tr></table>
<?}else{
	CJSCore::Init(array("jquery"));

	$isLogged  = imldriver::isLogged();
	$converted = imldriver::isConverted();

	//определ€ем статусы заказов
	$orderState=array(''=>'');
	$tmpValue = CSaleStatus::GetList(array("SORT" => "ASC"), array("LID" => LANGUAGE_ID));
	while($tmpVal=$tmpValue->Fetch())
	{
		if(!array_key_exists($tmpVal['ID'],$orderState))
			$orderState[$tmpVal['ID']]=$tmpVal['NAME']." [".$tmpVal['ID']."]";
	}
	//регионы
	$IPOLIML_list = imlHelper::getListFile();
	if(COption::GetOptionString($module_id,"departure",false) == false){ // город по умолчанию: тот, что в настройках магазина, или ћосква
		$defaultCity = false;
		$shopCity = COption::GetOptionString('sale', 'location', false, SITE_ID);
		if($shopCity){
			$shopCity = CSaleLocation::GetById($shopCity);
			if(is_array($IPOLIML_list['Region'])){
				if(in_array($shopCity['CITY_NAME'],$IPOLIML_list['Region']))
					$defaultCity = array_search($shopCity['CITY_NAME'],$IPOLIML_list['Region']);
				elseif(in_array($shopCity['CITY_NAME_LANG'],$IPOLIML_list['Region'])){
					$defaultCity = array_search($shopCity['CITY_NAME_LANG'],$IPOLIML_list['Region']);
				}
			}
		}
		if(!$defaultCity)
			$defaultCity = GetMessage('IPOLIML_BGMSC');
	}

	$arAllOptions = array(
		"logData" => array(
			array("logIml",GetMessage("IPOLIML_OPT_logIml"),false,array("text")),
			array("pasIml",GetMessage("IPOLIML_OPT_pasIml"),false,array("password")),
			array("logged","logged",false,array('text')),//залогинен ли пользователь
		),
		"common" => Array(
			array("isTest",GetMessage("IPOLIML_OPT_isTest"),"Y",array("checkbox")),
			array("strName",GetMessage("IPOLIML_OPT_strName"),false,array("text")),
			array("delReqOrdr",GetMessage("IPOLIML_OPT_delReqOrdr"),false,array("checkbox")),
			// array("addJQ",GetMessage("IPOLIML_OPT_addJQ"),"N",array("checkbox")),
			array("prntActOrdr",GetMessage("IPOLIML_OPT_prntActOrdr"),"O",array("selectbox"),array("O" => GetMessage('IPOLIML_OTHR_ACTSORDRS'),"A" => GetMessage('IPOLIML_OTHR_ACTSONLY'))),
			array("orderIdMode",GetMessage("IPOLIML_OPT_orderIdMode"),imldriver::defiDefON(),array("checkbox")),
			array("showInOrders",GetMessage("IPOLIML_OPT_showInOrders"),"Y",array("selectbox"),array("Y" => GetMessage('IPOLIML_OTHR_ALWAYS'),"N" => GetMessage('IPOLIML_OTHR_DELIVERY'))),
			array("noVats",GetMessage("IPOLIML_OPT_noVats"),"N",array("checkbox")),
			array("editStatisticalValue",GetMessage("IPOLIML_OPT_editStatisticalValue"),"N",array("checkbox")),
		),
		"status" => Array(
			array("setDeliveryId", GetMessage("IPOLIML_OPT_setDeliveryId"),"Y",array("checkbox")),
			array("markPayed", GetMessage("IPOLIML_OPT_markPayed"),"N",array("checkbox")),
			array("statusOK",GetMessage("IPOLIML_OPT_statusOK"),false,array("selectbox"),$orderState),
			array("statusFAIL", GetMessage("IPOLIML_OPT_statusFAIL"),false,array("selectbox"),$orderState),
			array("statusSTORE", GetMessage("IPOLIML_OPT_statusSTORE"),false,array("selectbox"),$orderState),
			array("statusCORIER", GetMessage("IPOLIML_OPT_statusCORIER"),false,array("selectbox"),$orderState),
			array("statusPVZ", GetMessage("IPOLIML_OPT_statusPVZ"),false,array("selectbox"),$orderState),
			array("statusDELIVD", GetMessage("IPOLIML_OPT_statusDELIVD"),false,array("selectbox"),$orderState),
			array("statusOTKAZ", GetMessage("IPOLIML_OPT_statusOTKAZ"),false,array("selectbox"),$orderState),
		),
		"orderParams" =>Array(
			array("departure",GetMessage("IPOLIML_OPT_depature"),$defaultCity,array("selectbox"),$IPOLIML_list['Region']),//регион отправлени€
			array("selectDeparture",GetMessage("IPOLIML_OPT_selectDeparture"),'N',array("checkbox")),
			array("name",GetMessage("IPOLIML_JS_SOD_name"),"#PROP_FIO#",array("text")),//контактное лицо
			array("city",GetMessage("IPOLIML_JS_SOD_city"),"#PROP_LOCATION#",array("text")),//город
			array("line",GetMessage("IPOLIML_JS_SOD_line"),"#PROP_ADDRESS#",array("text")),//адрес
			array("postCode",GetMessage("IPOLIML_JS_SOD_postCode"),false,array("text")),//почтовый индекс
			array("telephone1",GetMessage("IPOLIML_JS_SOD_telephone1"),"#PROP_PHONE#",array("text")),// онтактный телефон
			array("email",GetMessage("IPOLIML_JS_SOD_email"),"#PROP_EMAIL#",array("text")),// онтактный телефон
			array("comment",GetMessage("IPOLIML_JS_SOD_comment"),"#USER_DESCRIPTION#",array("text")),//комментарий
		),
		"itemProps" => Array(//свойства товара откуда брать
			Array("loadGoods", GetMessage("IPOLIML_OPT_loadGoods"), 'Y', Array("checkbox")),
			Array("VATRate", GetMessage("IPOLIML_OPT_VATRate"), 'NONDS', Array("selectbox"),array('NONDS'=>GetMessage('IPOLIML_SIGN_NONDS'),'0'=>'0%','10'=>'10%','18'=>'18%'),''),
			Array("NDSUseCatalog", GetMessage("IPOLIML_NDSUseCatalog"), 'N', Array("checkbox")),
			Array("articul", GetMessage("IPOLIML_OPT_articul"), 'ARTNUMBER', Array("text")),
			// Array("additional", GetMessage("IPOLIML_OPT_articul"), '', Array("text")),
			Array("barcode", GetMessage("IPOLIML_OPT_barcode"), '', Array("text")),
		),
		"basket" => array(
			array("noPVZnoOrder",GetMessage("IPOLIML_OPT_noPVZnoOrder"),"N",array("checkbox")),
			array("hideNal",GetMessage("IPOLIML_OPT_hideNal"),"Y",array("checkbox")),
			array("pvzID",GetMessage("IPOLIML_OPT_pvzID"),"",array("text")),
			array("pvzPicker",GetMessage("IPOLIML_OPT_pvzPicker"),"ADDRESS",array("text")),
			array("autoSelOne",GetMessage("IPOLIML_OPT_autoSelOne"),"",array("checkbox")),
			array("labelDays",GetMessage("IPOLIML_OPT_labelDays"),"",array("selectbox"),array("N" => GetMessage('IPOLIML_OPT_labelDays_NONE'),"D" => GetMessage('IPOLIML_OPT_labelDays_DAY'),'A' => GetMessage('IPOLIML_OPT_labelDays_ALL'))),
		),
		"deliverySys" => array(
			array("countType",GetMessage("IPOLIML_OPT_countType"),"T",array("selectbox"),array("T" => GetMessage('IPOLIML_OPT_countType_TABLE'),"S" => GetMessage('IPOLIML_OPT_countType_SERVER'))),
			array("serverToTable",GetMessage("IPOLIML_OPT_serverToTable"),"",array("checkbox")),
			array("defaultWeight",GetMessage("IPOLIML_OPT_defaultWeight"),1,array("text")),
		),
		"paySystems" => array(
			array("paySystems",GetMessage("IPOLIML_OPT_paySystems"),"",array("text")),
		),
		"termsDeliv" => array(
			array("timeSend",GetMessage("IPOLIML_OPT_timeSend"),"",array("text")),
			array("commonHold",GetMessage("IPOLIML_OPT_commonHold"),"",array("text")),
			array("addHold",GetMessage("IPOLIML_OPT_addHold"),"",array("text")),
		),
		"addingService" => array(
			array("services",GetMessage("IPOLIML_OPT_services"),GetMessage("IPOLIML_OPTION_DEFSERVICES"),array("text")),
			array("blockedServices",GetMessage("IPOLIML_OPT_blockedServices"),GetMessage("IPOLIML_OPTION_DEFSERVICES"),array("text")),
		),
		"service"=>array(
			array("last",GetMessage("IPOLIML_JS_SOD_last"),false,array("text")),//последн€ за€вка
			array("schet",GetMessage("IPOLIML_JS_SOD_schet"),'0',array("text")),//количество за€вок
			array("getOutLst",GetMessage("IPOLIML_OPT_getOutLst"),'0',array("text")),//дата последнего запроса к outbox
			array("lstShtPr",GetMessage("IPOLIML_OPT_lstShtPr"),'0',array("text")),//дата последней печати штрихкодов к за€вкам
			array("useOldAPI",GetMessage("IPOLIML_OPT_useOldAPI"),'N',array("checkbox")),//использовать старый API дл§ запросов
			array("turnOffRestrictsOS",GetMessage("IPOLIML_OPT_turnOffRestrictsOS"),'Y',array("checkbox")),//проверка на услуги
		),
	);

	if($isLogged){
		$aTabs = array(
			array("DIV" => "edit1", "TAB" => GetMessage("IPOLIML_TAB_FAQ"), "TITLE" => GetMessage("IPOLIML_TAB_TITLE_FAQ")),
			array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
			array("DIV" => "edit3", "TAB" => GetMessage("IPOLIML_TAB_LIST"), "TITLE" => GetMessage("IPOLIML_TAB_TITLE_LIST")),
		);
		$divId = count($aTabs);
		foreach(GetModuleEvents($module_id,"onTabsBuild",true) as $arEvent)
			ExecuteModuleEventEx($arEvent,Array(&$arTabs));
		if(count($arTabs))
			foreach($arTabs as $tabName => $path)
				$aTabs[]=array("DIV" => "edit".(++$divId), "TAB" => $tabName, "TITLE" => $tabName);
	}else
		$aTabs = array(array("DIV" => "edit1", "TAB" => GetMessage("IPOLIML_TAB_LOGIN"), "TITLE" => GetMessage("IPOLIML_TAB_TITLE_LOGIN")));

	//Restore defaults
	if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
		COption::RemoveOption($module_id);

	//Save options
	if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
	{
		if(strlen($RestoreDefaults)>0)
			COption::RemoveOption($module_id);
		else
		{
			// blockPVZ
			if($_REQUEST['noPVZnoOrder'] == 'Y' && COption::GetOptionString($module_id,'noPVZnoOrder','N') == 'N'){
			if($converted){
				RegisterModuleDependences("sale", "OnSaleOrderBeforeSaved", $module_id, "CDeliveryIML", "noPVZNewTemplate");
				RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliveryIML", "noPVZOldTemplate");
			}else
				RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliveryIML", "noPVZOldTemplate");
			}elseif((!array_key_exists('noPVZnoOrder',$_REQUEST) || $_REQUEST['noPVZnoOrder'] == 'N') && COption::GetOptionString($module_id,'noPVZnoOrder','N') == 'Y'){
			if($converted){
				UnRegisterModuleDependences("sale", "OnSaleOrderBeforeSaved", $module_id, "CDeliveryIML", "noPVZNewTemplate");
				UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliveryIML", "noPVZOldTemplate");
			}else
				UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $module_id, "CDeliveryIML", "noPVZOldTemplate");
			}

			$_REQUEST['paySystems']      = ($_REQUEST['paySystems'])      ? serialize($_REQUEST['paySystems'])      : 'a:0:{}';
			$_REQUEST['services']        = ($_REQUEST['services'])        ? serialize($_REQUEST['services'])        : 'a:0:{}';
			$_REQUEST['blockedServices'] = ($_REQUEST['blockedServices']) ? serialize($_REQUEST['blockedServices']) : 'a:0:{}';
			$holdCity = array();
			foreach($_REQUEST['addHoldCity'] as $ind => $val){
				if(!$val) continue;
				$term = intval($_REQUEST['addHoldTerm'][$ind]);
				if($term)
					$holdCity[$val]=$term;
			}
			$_REQUEST['addHold']=($holdCity)?serialize($holdCity):'a:0:{}';
			foreach($arAllOptions as $aOptGroup)
			{
				foreach($aOptGroup as $option)
				{
					__AdmSettingsSaveOption($module_id, $option);
				}
			}
			if(COption::GetOptionString($module_id,'delReqOrdr','')=='Y')
				RegisterModuleDependences("sale","OnOrderDelete",$module_id,"imldriver","delReqOD");
			else
				UnRegisterModuleDependences("sale","OnOrderDelete",$module_id,"imldriver","delReqOD");
		}

		if($_REQUEST["back_url_settings"] <> "" && $_REQUEST["Apply"] == "")
			 echo '<script type="text/javascript">window.location="'.CUtil::addslashes($_REQUEST["back_url_settings"]).'";</script>';				
	}

	function ShowParamsHTMLByArray($arParams)
	{
		global $module_id;
		foreach($arParams as $Option)
		{
			if($Option[3][0]!='selectbox')
				__AdmSettingsDrawRow($module_id, $Option);
			else
			{
				$optVal=COption::GetOptionString($module_id,$Option['0'],$Option['2']);
				$str='';
				foreach($Option[4] as $key => $val)
				{
					$chkd='';
					if((string)$optVal==(string)$key)
						$chkd='selected';
					$str.='<option '.$chkd.' value="'.$key.'">'.$val.'</option>';
				}
				echo '<tr>
						<td width="50%" class="adm-detail-content-cell-l">'.$Option[1].'</td>  
						<td width="50%" class="adm-detail-content-cell-r"><select name="'.$Option['0'].'">'.$str.'</select></td>
					</tr>';
			}
		}
	}




	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	?>
	<?if($canWork == 2){?>
		<table><tr><td colspan='2'>
			<div class="adm-info-message-wrap">
				<div class="adm-info-message">
					<div class="adm-info-message-title"><?=GetMessage('IPOLIML_OTHR_DEMOON_HDR')?></div>
					<?=GetMessage('IPOLIML_OTHR_DEMOON_DESCR')?>
				</div>
			</div>
		</td></tr></table>
	<?}?>
	<?if($isLogged){?>
	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>">
		<?
		$tabControl->Begin();
		$tabControl->BeginNextTab();
		include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id ."/optionsInclude/faq.php");
		$tabControl->BeginNextTab();
		include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id ."/optionsInclude/setups.php");
		$tabControl->BeginNextTab();
		include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id ."/optionsInclude/table.php");
		if(count($arTabs))
			foreach($arTabs as $tabName => $path){
				$tabControl->BeginNextTab();
				include_once($_SERVER['DOCUMENT_ROOT'].$path);
			}
		$tabControl->Buttons();
		?>
		<div align="left">
			<input type="hidden" name="Update" value="Y">
			<input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
		</div>
		<?$tabControl->End();?>
		<?=bitrix_sessid_post();?>
	</form>
	<?}
	else{
		$tabControl->Begin();
		$tabControl->BeginNextTab();
		include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$module_id ."/optionsInclude/login.php");
		$tabControl->End();
	}
}