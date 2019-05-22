<? 
if(isset($_POST['ajax']) && $_POST['ajax'] == '1' && isset($_POST['action']) && $_POST['action'] == 'formsubmit' && intval($_POST['WEB_FORM_ID']) == intval($arParams['WEB_FORM_ID'])) {
	if(count($arResult['FORM_ERRORS']) > 0){
		$result = array(
			'status' => 'error',
			'errors' => $arResult['FORM_ERRORS']
		);

		// json response
		while (ob_get_level() > 1)
			ob_end_clean();
		header("Content-type: application/json");
		echo json_encode($result);
		die();
	}
}
if(isset($_GET['formresult']) && $_GET['formresult'] == 'addok' && !(isset($_GET['ajax']) && $_GET['ajax'] == '0' && intval($_GET['WEB_FORM_ID']) == intval($arParams['WEB_FORM_ID']))) {
	$result = array(
		'status' => 'success',
		'message' => $arResult['FORM_NOTE']
	);
	if(trim($arParams['THANKYOU_URL']) != ""){
		$result['redirect'] = $arParams['THANKYOU_URL'];
	}
	
	// json response
	while (ob_get_level() > 1)
		ob_end_clean();
	header("Content-type: application/json");
	echo json_encode($result);
	die();
}

/* Заполнение скрытых полей из параметров компонента. Плюс передаём через data-fieldname оригинальное название поля */

foreach ($arResult['QUESTIONS'] as $key => $field) {
	//cdump($field);
	if($field['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden'){
		$arResult['QUESTIONS'][$key]['HTML_CODE'] = '<input type="hidden"  name="form_hidden_'.$field['STRUCTURE'][0]['ID'].'" value="'.$arParams[strtoupper($key)."_VALUE"].'" data-fieldname="'.$key.'" />';
	} else if($field['STRUCTURE'][0]['FIELD_TYPE'] == 'textarea'){
		$arResult['QUESTIONS'][$key]['HTML_CODE'] = str_replace('class="inputtextarea"', 'class="inputtextarea" data-fieldname="'.$key.'"', $arResult['QUESTIONS'][$key]['HTML_CODE']);
	} else {
		$arResult['QUESTIONS'][$key]['HTML_CODE'] = str_replace('/>','data-fieldname="'.$key.'" />', $arResult['QUESTIONS'][$key]['HTML_CODE']);
	}
}

if (mb_strlen($arParams['ORDER_ID'])>0 && intval($arParams['USER_ID'])>0) {
	//Получаем список товаров
	CModule::IncludeModule('sale');
	$res = CSaleUser::GetList(array("USER_ID"=>$arParams['USER_ID']));
	$FUSER_ID = $res['ID'];

	$dbBasketItems = CSaleBasket::GetList(
		array("NAME" => "ASC", "ID" => "ASC"),
		array(
				"LID" => SITE_ID,
				"ORDER_ID" => $arParams['ORDER_ID'],
				"FUSER_ID" => $FUSER_ID
			),
		false,
		false,
		array("ID", "PRODUCT_ID", "QUANTITY", "PRICE")
	);
	while ($arItems = $dbBasketItems->Fetch()){
		$productsIDs[] = $arItems["PRODUCT_ID"];
		$productsQuantity[$arItems["PRODUCT_ID"]] = $arItems["QUANTITY"];
		$productsPrice[$arItems["PRODUCT_ID"]] = $arItems["PRICE"];
	}
	if (count($productsIDs)>0) {
		# code...
		foreach ($productsIDs as $key => $value) {
			# code...
		}

		if (\Bitrix\Main\Loader::includeModule("iblock")){
			$dbSku = CIBlockElement::GetList(
				array(),
				array("IBLOCK_ID" => SKU_IBLOCK_ID, "ID" => $productsIDs),
				false,
				false,
				array("ID", "NAME", "PROPERTY_CITY", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_ARTICLE", "PROPERTY_STORE", "PROPERTY_UNIQUE_ID", "PROPERTY_SIZE", "DETAIL_PAGE_URL")
			);
			
			$arUsedLocs = array();
			
			while ($arSku = $dbSku->GetNext()){

				//Изображение
				$url = DEFAULT_TEMPLATE_PATH."/img/no_picture.png";
				$arImage = false;

				if (strlen($arSku["PREVIEW_PICTURE"]) > 0){
					$arImage = CFile::GetFileArray($arSku["PREVIEW_PICTURE"]);
				}elseif (strlen($arSku["DETAIL_PICTURE"]) > 0){
					$arImage = CFile::GetFileArray($arSku["DETAIL_PICTURE"]);
				}

				if ($arImage){
					$arFileTmp = CFile::ResizeImageGet(
						$arImage,
						array("width" => "120", "height" =>"120"),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$url = $arFileTmp["src"];
				}
			}
		}
	}
	dump($productsIDs);
}

?>