<?
	foreach ($arResult['SEARCH'] as $key => $arItem) {
		if(substr($arItem['URL_WO_PARAMS'], 0, strlen("/catalog/")) == "/catalog/"){
			unset($arResult['SEARCH'][$key]);
		}
	}
