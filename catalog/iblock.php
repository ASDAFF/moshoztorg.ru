<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$bFound = false;

if ( isset($_GET['IBLOCK_CODE']) && !empty($_GET['IBLOCK_CODE'])){


CModule::IncludeModule("iblock");
$res = CIBlock::GetList(
    Array(), 
    Array(
        'TYPE'=>'mht_products', 
        'SITE_ID'=>SITE_ID, 
        'ACTIVE'=>'Y', 
        "CODE"=>$_GET['IBLOCK_CODE']
    ), true
);

if($ar_res = $res->Fetch()){
	
/*	echo '<pre>';
	print_r( $ar_res );
	echo '</pre>';
*/

	$APPLICATION->SetTitle( $ar_res['NAME'] );

	$bFound = true;

}


}
	

if ( !$bFound ){
if (!defined("ERROR_404"))
   define("ERROR_404", "Y");

\CHTTP::setStatus("404 Not Found");
   
if ($APPLICATION->RestartWorkarea()) {
   require(\Bitrix\Main\Application::getDocumentRoot()."/404.php");
   die();
}

}


?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	"mht", 
	array(
		"SEF_FOLDER" => $ar_res['LIST_PAGE_URL'],
		"IBLOCK_ID" => $ar_res['ID'],
		"IBLOCK_TYPE" => "mht_products",
		"HIDE_NOT_AVAILABLE" => "N",
		"TEMPLATE_THEME" => "",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "N",
		"DETAIL_SHOW_MAX_QUANTITY" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"DETAIL_USE_VOTE_RATING" => "N",
		"DETAIL_USE_COMMENTS" => "N",
		"DETAIL_BRAND_USE" => "N",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SEF_MODE" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_FILTER" => "Y",
		"FILTER_VIEW_MODE" => "HORIZONTAL",
		"USE_REVIEW" => "N",
		"USE_COMPARE" => "N",
		"PRICE_CODE" => array(
			0 => PRICE_CODE,
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/catalog/basket/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRODUCT_QUANTITY" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"SHOW_TOP_ELEMENTS" => "Y",
		"TOP_ELEMENT_COUNT" => "9",
		"TOP_LINE_ELEMENT_COUNT" => "3",
		"TOP_ELEMENT_SORT_FIELD" => "",
		"TOP_ELEMENT_SORT_ORDER" => "",
		"TOP_ELEMENT_SORT_FIELD2" => "",
		"TOP_ELEMENT_SORT_ORDER2" => "",
		"TOP_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_TOP_DEPTH" => "2",
		"SECTIONS_VIEW_MODE" => "LIST",
		"SECTIONS_SHOW_PARENT_NAME" => "Y",
		"PAGE_ELEMENT_COUNT" => "30",
		"LINE_ELEMENT_COUNT" => "3",
		"ELEMENT_SORT_FIELD" => "",
		"ELEMENT_SORT_ORDER" => "",
		"ELEMENT_SORT_FIELD2" => "",
		"ELEMENT_SORT_ORDER2" => "",
		"LIST_PROPERTY_CODE" => array(
			0 => "CML2_MANUFACTURER",
			1 => "",
		),
		"INCLUDE_SUBSECTIONS" => "Y",
		"LIST_META_KEYWORDS" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_BROWSER_TITLE" => "-",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_DISPLAY_NAME" => "Y",
		"DETAIL_DETAIL_PICTURE_MODE" => "IMG",
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "H",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"USE_ALSO_BUY" => "N",
		"USE_STORE" => "N",
		"PAGER_TEMPLATE" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"TOP_VIEW_MODE" => "BANNER",
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => "-",
		"PRODUCT_DISPLAY_MODE" => "N",
		"OFFER_ADD_PICT_PROP" => "-",
		"OFFER_TREE_PROPS" => array(
		),
		"OFFERS_CART_PROPERTIES" => array(
		),
		"TOP_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"TOP_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"TOP_OFFERS_LIMIT" => "5",
		"TOP_ROTATE_TIMER" => "30",
		"LIST_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_OFFERS_LIMIT" => "5",
		"DETAIL_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_SORT_FIELD" => "",
		"OFFERS_SORT_ORDER" => "",
		"OFFERS_SORT_FIELD2" => "",
		"OFFERS_SORT_ORDER2" => "",
		"AJAX_OPTION_ADDITIONAL" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"FILTER_NAME" => "arrFilter",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_PROPERTY_CODE" => array(
		),
		"FILTER_PRICE_CODE" => array(
			0 => PRICE_CODE,
		),
		"FILTER_OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_OFFERS_PROPERTY_CODE" => array(),
		"COMPARE_PROPERTY_CODE" => array(
			"0" => "CML2_BAR_CODE",
			"1" => "CML2_ARTICLE",
			"2" => "CML2_ATTRIBUTES",
			"3" => "CML2_TRAITS",
			"4" => "CML2_BASE_UNIT",
			"5" => "CML2_TAXES",
			"6" => "MORE_PHOTO",
			"7" => "FILES",
			"8" => "CML2_MANUFACTURER",
			"9" => "ETONOVINKA",
			"10" => "SAYT_NOVINKA",
			"11" => "SAYT_NA_GLAVNUYU",
			"12" => "SAYT_AKTSIONNYY_TOVAR",
			"13" => "OBORUDOVANIE",
			"14" => "OVOSHCHI",
			"15" => "NAIMENOVANIE_DLYA_SAYTA",
			"16" => "MOSHCHNOST",
			"17" => "PROIZVODITEL_I_ADRES_PROIZVODSTVA",
			"18" => "IMPORTER",
			"19" => "PRIEM_PRETENZIY",
			"20" => "STRANA_PROIZVODITEL",
			"21" => "USLOVIYA_KHRANENIYA",
			"22" => "DATA_PROIZVODSTVA",
			"23" => "GARANTIYNYY_SROK",
			"24" => "PRIMENENIE",
			"25" => "SERTIFIKATSIYA",
			"26" => "SROK_GODNOSTI",
			"27" => "STANDART",
			"28" => "TIPOBSHCHIY",
			"29" => "MATERIALOBSHCHIY",
			"30" => "NAZNACHENIEOBSHCHIY",
			"31" => "MOSHCHNOSTOBSHCHAYA",
			"32" => "DIAMETROBSHCHIY",
			"33" => "DLINNAOBSHCHIY",
			"34" => "SHIRINAOBSHCHIY",
			"35" => "VYSOTAOBSHCHIY",
			"36" => "RAZMEROBSHCHIY",
			"37" => "SHTRIKHKODA",
			"38" => "NOVINKA",
			"39" => "OBEMOBSHCHIY",
			"40" => "VESOBSHCHIY",
			"41" => "NAZNACHENIE",
			"42" => "MATERIAL",
			"43" => "NAIMENOVANIE",
			"44" => "OLD_PRICE_1",
			"45" => "FORMA",
			"46" => "TSVET",
			"47" => "NABOR",
			"48" => "DLYA_DETEY",
			"49" => "POKRYTIE",
			"50" => "DLYA_TORTA",
			"51" => "LETNIE",
			"52" => "POL",
			"53" => "OTZHIM",
			"54" => "KOMPLEKT",
			"55" => "vote_count",
			"56" => "vote_sum",
			"57" => "rating",
			"58" => "SAYT_BLACK_FRIDAY_TOVAR",
			"59" => "RAZMER",
			"60" => "KOLICHESTVO",
			"61" => "SOSTAV",
			"62" => "MODEL",
			"63" => "VOZRAST",
			"64" => "MARKET",
			"65" => "ANTIPRIGARNOE_POKRYTIE",
			"66" => "KRYSHKA_V_KOMPLEKTE",
			"67" => "SEMNYE_RUCHKI",
			"68" => "VODOOTTALKIVAYUSHCHIY_EFFEKT",
			"69" => "S_VOSKOM",
			"70" => "BESTSVETNYY",
			"71" => "PRAZDNIK",
			"72" => "AROMATIZIROVANNAYA",
			"73" => "CHAYNAYA",
			"74" => "V_STAKANE",
			"75" => "S_PODOGREVOM",
			"76" => "MASSAZHNYE",
			"77" => "NATURALNAYA_SHCHETINA",
			"78" => "AROMAT",
			"79" => "ZONT_TROST",
			"80" => "MINI_ZONT",
			"81" => "CHEKHOL",
			"82" => "SISTEMA_ANTIVETER",
			"83" => "CHISLO_SPITS",
			"84" => "PROZRACHNYY",
			"85" => "KUPOL",
			"86" => "SOSTOYANIE_SBORKI",
			"87" => "TELESKOPICHESKAYA_RUCHKA",
			"88" => "MATERIAL_MOPA",
			"89" => "KONSTRUKTSIYA_NASADKI",
			"90" => "TIP_NASADKI",
			"91" => "TERMOINDIKATOR",
			"92" => "GRUZOPODEMNOST",
			"93" => "VYSOTA_RULYA",
			"94" => "NAGRUZKA",
			"95" => "TIP_KOLES",
			"96" => "KOLICHESTVO_KOLES",
			"97" => "DIAMETR_KOLES",
			"98" => "TORMOZ",
			"99" => "DLINA_DEKI",
			"100" => "DLYA_KURYASHCHIKH",
			"101" => "TRYAPKI",
			"102" => "SALFETKI",
			"103" => "EDIZMRAZMERA",
			"104" => "ANKER_BOLT",
			"105" => "TIP",
			"106" => "PISTOLETY_GORYACHEGO_VOZDUKHA",
			"107" => "FENY_PROMYSHLENNYE",
			"108" => "VOZDUKHODUVKI",
			"109" => "ZHESTKOST",

		),
		"SEF_URL_TEMPLATES" => array(
			"sections" => "#SECTION_CODE_PATH#/",
			"section" => "#SECTION_ID#/",
			"element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
			"compare" => "compare/?action=#ACTION_CODE#",
		),
		"VARIABLE_ALIASES" => array(
			"compare" => array(
				"ACTION_CODE" => "action",
			),
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
