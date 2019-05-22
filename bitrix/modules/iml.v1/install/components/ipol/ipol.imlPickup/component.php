<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!cmodule::includeModule('iml.v1'))
	return false;

$allCities = false;
if(!is_array($arParams['CITIES']))
	$arParams['CITIES'] = array();
if(count($arParams['CITIES'])==0)
	$allCities=true;

$propAddr = Coption::GetOptionString(CDeliveryIML::$MODULE_ID,'pvzPicker','');//определяем инпуты, куда писать адреса
$props = CSaleOrderProps::GetList(array(),array('CODE' => $propAddr));
$propAddr='';
while($prop=$props->Fetch())
	$propAddr.=$prop['ID'].',';
	
$arResult['propAddr'] = $propAddr;
$arResult['Regions'] = array();

$arList = CDeliveryIML::getListFile();
foreach($arList['Region'] as $code => $city)
	if($allCities || in_array($code,$arParams['CITIES']))
		$arResult['Regions'][$code] = $city;
foreach($arList['SelfDelivery'] as $code => $PVZs)
	if(($allCities || in_array($code,$arParams['CITIES'])) && array_key_exists($code,$arList['Region'])){
		if(!$arParams['LOAD_ACTUAL_PVZ'] || $arParams['LOAD_ACTUAL_PVZ']!='Y' || imlHelper::checkAvPVZ($code,$PVZs))
			$arResult['PVZ'][$code] = $PVZs;
}

if(array_key_exists(GetMessage('IPOLIML_ROSTOV_CAPITAL'),$arResult['Regions']))
	$arResult['Regions'][GetMessage('IPOLIML_ROSTOV_CAPITAL')] = GetMessage('IPOLIML_ROSTOV_SMALL');

if($_SESSION['IPOLIML_city'] && array_key_exists(CDeliveryIML::toUpper($_SESSION['IPOLIML_city']),$arResult['Regions']))
	$arResult['city']=$_SESSION['IPOLIML_city'];
elseif(array_key_exists('DEFAULT_CITY',$arParams) && $arParams['DEFAULT_CITY'])
	$arResult['city']=$arParams['DEFAULT_CITY'];
else
	$arResult['city']=(count($arParams['CITIES'])==1)?$arParams['CITIES'][0]:GetMessage('IPOLIML_MOSCOW');

if($arParams['CNT_DELIV'] == 'Y'){
	$arResult['DELIVERY'] = imlHelper::cntDelivs(array(
		'cityTo'  => $arResult['city'],
	));
}

$this->IncludeComponentTemplate();
?>