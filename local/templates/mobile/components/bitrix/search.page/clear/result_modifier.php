<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["TAGS_CHAIN"] = array();
if($arResult["REQUEST"]["~TAGS"])
{
	$res = array_unique(explode(",", $arResult["REQUEST"]["~TAGS"]));
	$url = array();
	foreach ($res as $key => $tags)
	{
		$tags = trim($tags);
		if(!empty($tags))
		{
			$url_without = $res;
			unset($url_without[$key]);
			$url[$tags] = $tags;
			$result = array(
				"TAG_NAME" => htmlspecialcharsex($tags),
				"TAG_PATH" => $APPLICATION->GetCurPageParam("tags=".urlencode(implode(",", $url)), array("tags")),
				"TAG_WITHOUT" => $APPLICATION->GetCurPageParam((count($url_without) > 0 ? "tags=".urlencode(implode(",", $url_without)) : ""), array("tags")),
			);
			$arResult["TAGS_CHAIN"][] = $result;
		}
	}
}
//добавляем картинки
if ($arResult["SEARCH"]) {
	$arID = array();
	foreach ($arResult["SEARCH"] as $i => $arItem) {

	    // тут могут быть разделы
	    if ($arItem["MODULE_ID"] == "iblock" && substr($arItem["ITEM_ID"], 0, 1) !== "S")
            $arID[$arItem["ITEM_ID"]] = $i;

	}


	$el = CIBlockElement::GetList(array(), array(
		"ID" => array_keys($arID)
	), false, false, array(
		"ID",
		"IBLOCK_ID",
		"DETAIL_PICTURE"
	));
	while ($ar = $el->Fetch()) {
		$arResult["SEARCH"][$arID[$ar["ID"]]]["DETAIL_PICTURE"] = CFile::ResizeImageGet(CFile::GetFileArray($ar["DETAIL_PICTURE"]), array("width"=>70, "height"=>70), BX_RESIZE_IMAGE_PROPORTIONAL, true);

        //добавляем цену товара
		if ( in_array($arResult["SEARCH"][$arID[$ar["ID"]]]["PARAM2"],MHT::getCatalogIDs() )) {
            $product = new MHT\Product(['ID' => $arResult["SEARCH"][$arID[$ar["ID"]]]["ITEM_ID"]]);
            $arResult["SEARCH"][$arID[$ar["ID"]]]['PRICE'] = $product->get('price');
        }
	}


}

?>