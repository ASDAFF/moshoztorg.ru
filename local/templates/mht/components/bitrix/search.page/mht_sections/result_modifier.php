<?
$arResult['SECTIONS'] = array();
$arSectionIds = array();

foreach($arResult['SEARCH'] as $item){
	//myPrintR( $item , __FILE__, __LINE__ );
	if($item['PARAM1'] == 'mht_products' && substr($item['ITEM_ID'], 0, 1) == 'S'){ 
		$arSection = array(
			'ID' => substr($item['ITEM_ID'], 1),
			'TITLE_FORMATED' => $item['TITLE_FORMATED'],
			'URL' => $item['URL_WO_PARAMS'],
			'IBLOCK_ID' => $item['PARAM2']
		);
		$arSectionIds[] = $arSection['ID'];

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
}



$arSectionsByCode = array();
$arSectionIds = array();
$arResult['SECTIONS_FILTER'] = array();
$arResult['SECTIONS_ELEMENT_COUNTS'] = array();

$number = 0;
//получаем разделы для фильтрации
foreach($arResult['SEARCH'] as $item){
	//myPrintR( $item , __FILE__, __LINE__ );

	$sSectionCode =  isset($item['TAGS'][0]['TAG_NAME'])?$item['TAGS'][0]['TAG_NAME']:0; //md5( $item['CHAIN_PATH'] );

	if($item['PARAM1'] == 'mht_products' && substr($item['ITEM_ID'], 0, 1) != 'S'){


		if ( !array_key_exists($sSectionCode,$arSectionsByCode) && $item['PARAM2']>0 ){


			//получаем раздел элемента
			$arSelect = Array("ID", "IBLOCK_SECTION_ID");
			$arFilter = Array("IBLOCK_ID"=>$item['PARAM2'],"ID"=>$item['ITEM_ID']);

			$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
			if($ob = $res->GetNextElement()){
			  	$arFields = $ob->GetFields();

				//если такой раздел уже есть
				if (in_array($arFields['IBLOCK_SECTION_ID'],$arSectionIds)) continue;

				$res = CIBlockSection::GetByID(  $arFields['IBLOCK_SECTION_ID']  );
				if($ar_res_section = $res->GetNext()){

					$arSection = array(
						'ID' => $ar_res_section['ID'],
						'TITLE_FORMATED' => $ar_res_section['NAME'],
						'URL' => '/search/?q='.strip_tags($_GET['q']).'&tags='.$ar_res_section['ID'], /*$ar_res_section['SECTION_PAGE_URL'],*/
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

					$arSection['SECTION_CODE'] = $sSectionCode;
					$arResult['SECTIONS_FILTER'][] = $arSection;

					$arSectionsByCode[$sSectionCode] = 1; //end($arResult['SECTIONS_FILTER']);

					if (isset( $arResult['SECTIONS_ELEMENT_COUNTS'][$sSectionCode])) {
						$arResult['SECTIONS_ELEMENT_COUNTS'][$sSectionCode]++;
					}else
						$arResult['SECTIONS_ELEMENT_COUNTS'][$sSectionCode] = 1;


				}
			}

		}elseif( array_key_exists($sSectionCode,$arSectionsByCode) && $item['PARAM2']>0 ) {

			if (isset( $arResult['SECTIONS_ELEMENT_COUNTS'][$sSectionCode])) {
				$arResult['SECTIONS_ELEMENT_COUNTS'][$sSectionCode]++;
			}else
				$arResult['SECTIONS_ELEMENT_COUNTS'][$sSectionCode] = 1;

		}
	}/*else {

		echo '<!-- [[';
		myPrintR( $sSectionCode , __FILE__, __LINE__ );
		echo '-->';

	}*/
}

/*echo '<!-- [[';
myPrintR( $arResult['SECTIONS_ELEMENT_COUNTS'] , __FILE__, __LINE__ );
echo '-->';*/

$arResult['SHOW_ALL_SECTION_FILTER_LINK'] = '/search/?q='.strip_tags($_GET['q']);