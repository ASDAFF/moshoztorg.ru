<?php
global $$arParams['FILTER_NAME'];
$curFilter = $$arParams['FILTER_NAME'];
$userFilter = $curFilter;


/*
foreach($arResult["ITEMS"] as &$arItem){
	if($arItem['CODE'] != 'PROP_YEAR'){
		continue;
	}

	$v = array();
	foreach($arItem['VALUES'] as $i => $ar){
		if($ar['VALUE'] > g::getMinYear()){
			$v[$i] = $ar;
		}
	}
	$arItem['VALUES'] = $v;
}
*/

CModule::IncludeModule('iblock');
$list = CIBlockProperty::GetList(array('SORT' => 'ASC'), array(
	'IBLOCK_ID' => 1
));

while(($element = $list->GetNext()) !== false){
	if(!isset($arResult['ITEMS'][$element['ID']]) || !($element['HINT'])){
		continue;
	}
	$arResult['ITEMS'][$element['ID']]['HINT'] = $element['HINT'];
}
if($_REQUEST['ajax']!="y"){
	/*
	 * Редиректы на псевдоразделы
	 * */
	include_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/CustomCCatalogCondTree.php");

	$userFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
	$userFilter['SECTION_ID'] = $arParams['SECTION_ID'];
	unset($userFilter['><CATALOG_PRICE_1']);
	asort($userFilter);
	$arSectionFilters = array();
	if($_REQUEST['set_filter']){

		$cachefileiblockprops = $_SERVER['DOCUMENT_ROOT'].'/upload/custom_cache/custom_pseudosection_admin_props.php';
		$handle = fopen($cachefileiblockprops, "r");
		$contents = fread($handle, filesize($cachefileiblockprops));
		$allFilters = json_decode($contents, true);
		fclose($handle);

		foreach($allFilters as $arPseudoFilter){
			if($arPseudoFilter['SPECIAL']){f::cdump($arPseudoFilter['SPECIAL']);}
			if($arPseudoFilter['FILTER'] == $userFilter){
				//Сохраняем цены при редиректе
				if($_REQUEST['arrFilter_P1_MIN'] && $_REQUEST['arrFilter_P1_MIN']!=''){
					$arAdditionalGet[] = 'arrFilter_P1_MIN='.$_REQUEST['arrFilter_P1_MIN'];
				}
				if($_REQUEST['arrFilter_P1_MAX'] && $_REQUEST['arrFilter_P1_MAX']!=''){
					$arAdditionalGet[] = 'arrFilter_P1_MAX='.$_REQUEST['arrFilter_P1_MAX'];
				}
				if(count($arAdditionalGet)>0){
					$arAdditionalGet[] = 'set_filter='.$_REQUEST['set_filter'];
					$AdditionalGet = '?'.implode('&', $arAdditionalGet);
				}
				$sectionPath = $arPseudoFilter['URL'].$AdditionalGet;
				LocalRedirect($sectionPath);
			}
		}
	}
	if($arParams['PSEUDOFILTER']){
		foreach($arParams['PSEUDOFILTER'] as $pFilter){
			$arResult['ITEMS'][$pFilter['PROP_ID']]['VALUES'][$pFilter['PROP_VALUE']]['CHECKED']=true;
		}
	}
	if($arParams['PSEUDOFILTER_ACTION']){
		$arResult["FORM_ACTION"] = $arParams['PSEUDOFILTER_ACTION'];
	}

	/*
	foreach($arResult['ITEMS'][125]['VALUES'] as $id => &$v){
		$v['VALUE'] = $id.'. '.$v['VALUE'];
	}
	*/

	// скрываем лишние значения фильтра
	foreach(array(
		127, 128, 129, 130, 131,
		134, 135, 136, 137, 138, 139, 140, 141, 142
	) as $id){
		unset($arResult['ITEMS'][125]['VALUES'][$id]);
	}
}