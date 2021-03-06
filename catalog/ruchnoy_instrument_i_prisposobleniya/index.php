<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Ручной инструмент и приспособления");
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	"mht", 
	array(
		"SEF_FOLDER" => "/catalog/ruchnoy_instrument_i_prisposobleniya/",
		"IBLOCK_ID" => "525",
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
			"9" => "ETONOVINKA",
			"10" => "SAYT_NOVINKA",
			"11" => "SAYT_NA_GLAVNUYU",
			"12" => "SAYT_AKTSIONNYY_TOVAR",
			"13" => "SAMOVYVOZ",
			"14" => "OPISANIE",
			"15" => "SOBRAN",
			"16" => "OBORUDOVANIE",
			"17" => "NAZNACHENIE",
			"18" => "PRIEM_PRETENZIY",
			"19" => "STRANA_PROIZVODITEL",
			"20" => "USLOVIYA_KHRANENIYA",
			"21" => "DATA_PROIZVODSTVA",
			"22" => "GARANTIYNYY_SROK",
			"23" => "PRIMENENIE",
			"24" => "SERTIFIKATSIYA",
			"25" => "SROK_GODNOSTI",
			"26" => "STANDART",
			"27" => "TIPOBSHCHIY",
			"28" => "MATERIALOBSHCHIY",
			"29" => "NAZNACHENIEOBSHCHIY",
			"30" => "MOSHCHNOSTOBSHCHAYA",
			"31" => "DIAMETROBSHCHIY",
			"32" => "DLINNAOBSHCHIY",
			"33" => "SHIRINAOBSHCHIY",
			"34" => "VYSOTAOBSHCHIY",
			"35" => "RAZMEROBSHCHIY",
			"36" => "EDIZMRAZMERAOBSHCHIY",
			"37" => "SHTRIKHKODA",
			"38" => "NOVINKA",
			"39" => "OBEMOBSHCHIY",
			"40" => "VESOBSHCHIY",
			"41" => "OLD_PRICE_1",
			"42" => "vote_count",
			"43" => "vote_sum",
			"44" => "rating",
			"45" => "MOSHCHNOST",
			"46" => "IMPORTER",
			"47" => "NAIMENOVANIE",
			"48" => "OVOSHCHI",
			"49" => "NAIMENOVANIE_DLYA_SAYTA",
			"50" => "FORMA",
			"51" => "TSVET",
			"52" => "PROIZVODITEL_I_ADRES_PROIZVODSTVA",
			"53" => "NABOR",
			"54" => "DLYA_DETEY",
			"55" => "POKRYTIE",
			"56" => "POL",
			"57" => "KOMPLEKT",
			"58" => "SAYT_BLACK_FRIDAY_TOVAR",
			"59" => "KOLICHESTVO",
			"60" => "SOSTAV",
			"61" => "MODEL",
			"62" => "MARKET",
			"63" => "OTZHIM",
			"64" => "TELESKOPICHESKAYA_RUCHKA",
			"65" => "MATERIAL_MOPA",
			"66" => "KONSTRUKTSIYA_NASADKI",
			"67" => "TERMOINDIKATOR",
			"68" => "GRUZOPODEMNOST",
			"69" => "KOLICHESTVO_KOLES",
			"70" => "DIAMETR_KOLES",
			"71" => "TORMOZ",
			"72" => "DLINA_DEKI",
			"73" => "DLYA_KURYASHCHIKH",
			"74" => "TRYAPKI",
			"75" => "SALFETKI",
			"76" => "ANKER_BOLT",
			"77" => "ZHESTKOST",
			"78" => "TIP_NASADKI",
			"79" => "ANTIPRIGARNOE_POKRYTIE",
			"80" => "VOZRAST",
			"81" => "VYSOTA_RULYA",
			"82" => "NAGRUZKA",
			"83" => "TIP_KOLES",
			"84" => "PISTOLETY_GORYACHEGO_VOZDUKHA",
			"85" => "FENY_PROMYSHLENNYE",
			"86" => "VOZDUKHODUVKI",
			"87" => "ODNORAZOVYY",
			"88" => "SAYTBEZSKIDKI",
			"89" => "AROMAT",
			"90" => "ADMITAD_TARIFF",
			"91" => "OTPRAVLENO",
			"92" => "TOLKO_V_INTERNET_MAGAZINE",
			"93" => "NE_PRODAVAT_V_MINUS",
			"94" => "POSADICHNYI_KVADRAT",
			"95" => "RAZMER_MM",
			"96" => "DLINNEY_SPECH",
			"97" => "RUKOYTKA",
			"98" => "SHLICH",
			"99" => "TIP",
			"100" => "TIP_1",
			"101" => "MATERIAL",
			"102" => "TIP_ORGANAIZERA",
			"103" => "SOGLASOVANO",
			"104" => "OTPRAVLENY_ORIGINALY",
			"105" => "VOZVRAT_DOKUMENTOV",

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