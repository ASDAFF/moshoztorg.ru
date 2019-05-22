<?
/*$arResult['SECTIONS'] = array();
foreach($arResult['SEARCH'] as $item){
	if($item['PARAM1'] == 'mht_products' && substr($item['ITEM_ID'], 0, 1) == 'S'){
		$arSection = array(
			'ID' => substr($item['ITEM_ID'], 1),
			'TITLE_FORMATED' => $item['TITLE_FORMATED'],
			'URL' => $item['URL_WO_PARAMS'],
			'IBLOCK_ID' => $item['PARAM2']
		);

		$arSectionPath = array();

		$res = CIBlock::GetByID($arSection['IBLOCK_ID']);
		$ar_res = $res->GetNext();
		$arSectionPath[] = array(
			"NAME" => $ar_res['NAME'],
			"SECTION_PAGE_URL" => $ar_res['LIST_PAGE_URL']
		);
		$ob = GetIBlockSectionPath($arSection['IBLOCK_ID'], $arSection['ID']);
		while($ar = $ob->GetNext()){
			$arSectionPath[] = $ar;
		}
		$arSection['PATH'] = $arSectionPath;
		$arResult['SECTIONS'][] = $arSection;
	}
}*/