<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Строительная химия");
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	"mht", 
	array(
		"SEF_FOLDER" => "/catalog/stroitelnaya_khimiya/",
		"IBLOCK_ID" => "493",
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
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"SET_STATUS_404" => "Y",         "SHOW_404" => "Y",         "FILE_404" => "404.php",
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
		"PAGER_TEMPLATE" => "ajax",
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
			"9" => "PROIZVODITEL_I_ADRES_PROIZVODSTVA",
			"10" => "IMPORTER",
			"11" => "PRIEM_PRETENZIY",
			"12" => "STRANA_PROIZVODITEL",
			"13" => "USLOVIYA_KHRANENIYA",
			"14" => "DATA_PROIZVODSTVA",
			"15" => "GARANTIYNYY_SROK",
			"16" => "PRIMENENIE",
			"17" => "SERTIFIKATSIYA",
			"18" => "SROK_GODNOSTI",
			"19" => "STANDART",
			"20" => "TIPOBSHCHIY",
			"21" => "MATERIALOBSHCHIY",
			"22" => "NAZNACHENIEOBSHCHIY",
			"23" => "MOSHCHNOSTOBSHCHAYA",
			"24" => "DIAMETROBSHCHIY",
			"25" => "DLINNAOBSHCHIY",
			"26" => "SHIRINAOBSHCHIY",
			"27" => "VYSOTAOBSHCHIY",
			"28" => "RAZMEROBSHCHIY",
			"29" => "EDIZMRAZMERAOBSHCHIY",
			"30" => "SHTRIKHKODA",
			"31" => "NOVINKA",
			"32" => "OBEMOBSHCHIY",
			"33" => "VESOBSHCHIY",
			"34" => "vote_count",
			"35" => "vote_sum",
			"36" => "rating",
			"37" => "ETONOVINKA",
			"38" => "SAYT_NOVINKA",
			"39" => "SAYT_NA_GLAVNUYU",
			"40" => "SAYT_AKTSIONNYY_TOVAR",
			"41" => "SAMOVYVOZ",
			"42" => "OPISANIE",
			"43" => "SOBRAN",
			"44" => "SAYT_BLACK_FRIDAY_TOVAR",
			"45" => "OLD_PRICE_1",
			"46" => "NAIMENOVANIE",
			"47" => "OBORUDOVANIE",
			"48" => "NE_VYGRUZHAT_NA_SAYT",
			"49" => "NAIMENOVANIE_DLYA_SAYTA",
			"50" => "FORMA",
			"51" => "TSVET",
			"52" => "NABOR",
			"53" => "DLYA_DETEY",
			"54" => "RAZMER",
			"55" => "POL",
			"56" => "KOLICHESTVO",
			"57" => "SOSTAV",
			"58" => "MODEL",
			"59" => "OVOSHCHI",
			"60" => "TRYAPKI",
			"61" => "SALFETKI",
			"62" => "MOSHCHNOST",
			"63" => "ANKER_BOLT",
			"64" => "NAZNACHENIE",
			"65" => "SHVABRY_S_OTZHIMOM",
			"66" => "SHVABRY_NASADKI",
			"67" => "OKNOMOYKI",
			"68" => "TIP",
			"69" => "MATERIAL",
			"70" => "PISTOLETY_GORYACHEGO_VOZDUKHA",
			"71" => "FENY_PROMYSHLENNYE",
			"72" => "VOZDUKHODUVKI",
			"73" => "GRUZOPODEMNOST",
			"74" => "VOZRAST",
			"75" => "VYSOTA_RULYA",
			"76" => "NAGRUZKA",
			"77" => "TIP_KOLES",
			"78" => "KOLICHESTVO_KOLES",
			"79" => "DIAMETR_KOLES",
			"80" => "TORMOZ",
			"81" => "DLINA_DEKI",
			"82" => "DLYA_KURYASHCHIKH",
			"83" => "MARKET",
			"84" => "ZHESTKOST",
			"85" => "ODNORAZOVYY",
			"86" => "SAYTBEZSKIDKI",
			"87" => "AROMAT",
			"88" => "ADMITAD_TARIFF",
			"89" => "OTPRAVLENO",
			"90" => "TOLKO_V_INTERNET_MAGAZINE",
			"91" => "NE_PRODAVAT_V_MINUS",			"0" => "CML2_BAR_CODE",
			"1" => "CML2_ARTICLE",
			"2" => "CML2_ATTRIBUTES",
			"3" => "CML2_TRAITS",
			"4" => "CML2_BASE_UNIT",
			"5" => "CML2_TAXES",
			"6" => "MORE_PHOTO",
			"7" => "FILES",
			"8" => "CML2_MANUFACTURER",
			"9" => "PROIZVODITEL_I_ADRES_PROIZVODSTVA",
			"10" => "IMPORTER",
			"11" => "PRIEM_PRETENZIY",
			"12" => "STRANA_PROIZVODITEL",
			"13" => "USLOVIYA_KHRANENIYA",
			"14" => "DATA_PROIZVODSTVA",
			"15" => "GARANTIYNYY_SROK",
			"16" => "PRIMENENIE",
			"17" => "SERTIFIKATSIYA",
			"18" => "STANDART",
			"19" => "TIPOBSHCHIY",
			"20" => "MATERIALOBSHCHIY",
			"21" => "NAZNACHENIEOBSHCHIY",
			"22" => "MOSHCHNOSTOBSHCHAYA",
			"23" => "DIAMETROBSHCHIY",
			"24" => "DLINNAOBSHCHIY",
			"25" => "SHIRINAOBSHCHIY",
			"26" => "VYSOTAOBSHCHIY",
			"27" => "RAZMEROBSHCHIY",
			"28" => "SHTRIKHKODA",
			"29" => "NOVINKA",
			"30" => "OBEMOBSHCHIY",
			"31" => "VESOBSHCHIY",
			"32" => "NAZNACHENIE",
			"33" => "ETONOVINKA",
			"34" => "SAYT_NOVINKA",
			"35" => "SAYT_NA_GLAVNUYU",
			"36" => "SAYT_AKTSIONNYY_TOVAR",
			"37" => "SAMOVYVOZ",
			"38" => "OPISANIE",
			"39" => "SOBRAN",
			"40" => "SAYT_BLACK_FRIDAY_TOVAR",
			"41" => "OLD_PRICE_1",
			"42" => "vote_count",
			"43" => "vote_sum",
			"44" => "rating",
			"45" => "MOSHCHNOST",
			"46" => "NAIMENOVANIE",
			"47" => "NE_VYGRUZHAT_NA_SAYT",
			"48" => "NAIMENOVANIE_DLYA_SAYTA",
			"49" => "FORMA",
			"50" => "TSVET",
			"51" => "NABOR",
			"52" => "DLYA_DETEY",
			"53" => "POKRYTIE",
			"54" => "SROK_GODNOSTI",
			"55" => "RAZMER",
			"56" => "POL",
			"57" => "KOLICHESTVO",
			"58" => "SOSTAV",
			"59" => "MODEL",
			"60" => "OBORUDOVANIE",
			"61" => "VOZRAST",
			"62" => "NAGRUZKA",
			"63" => "TORMOZ",
			"64" => "MARKET",
			"65" => "OVOSHCHI",
			"66" => "GRUZOPODEMNOST",
			"67" => "VYSOTA_RULYA",
			"68" => "TIP_KOLES",
			"69" => "KOLICHESTVO_KOLES",
			"70" => "DIAMETR_KOLES",
			"71" => "DLINA_DEKI",
			"72" => "DLYA_KURYASHCHIKH",
			"73" => "TRYAPKI",
			"74" => "SALFETKI",
			"75" => "EDIZMRAZMERA",
			"76" => "ANKER_BOLT",
			"77" => "SHVABRY_S_OTZHIMOM",
			"78" => "SHVABRY_NASADKI",
			"79" => "OKNOMOYKI",
			"80" => "TIP",
			"81" => "MATERIAL",
			"82" => "PISTOLETY_GORYACHEGO_VOZDUKHA",
			"83" => "FENY_PROMYSHLENNYE",
			"84" => "VOZDUKHODUVKI",
			"85" => "ZHESTKOST",
			"86" => "ODNORAZOVYY",
			"87" => "AROMAT",
			"88" => "ADMITAD_TARIFF",
			"89" => "OTPRAVLENO",
			"90" => "TOLKO_V_INTERNET_MAGAZINE",
			"91" => "NE_PRODAVAT_V_MINUS",
			"92" => "NAZNACHENIR_KLEY",
			"93" => "EXPOSIDNEY",
			"94" => "PODRAZDELI",

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