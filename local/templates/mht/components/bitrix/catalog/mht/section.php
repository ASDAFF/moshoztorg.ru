<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use Bitrix\Main\Application;

$this->setFrameMode(true);

//отключаем стандартный вывод тайтла и описания
$arParams["SET_TITLE"] = 'N';
$arParams["SET_META_DESCRIPTION"] = 'N';

if (isset($arParams['IBLOCK_ID'],$arResult['CUR_SECTION']['ID']))
    if ($arShowFilter = shortUrl::checkShortUrl($arParams['IBLOCK_ID'],$arResult['CUR_SECTION']['ID'])) {

        //добавляем в параметры флаг для показа фильтра
        $arParams['SHOW_FILTER']['QUERY_STRING'] = $arShowFilter[ shortUrl::FIELD_NAME ];
        $arParams['SHOW_FILTER']['PATH'] = shortUrl::makeNavChain($arParams['IBLOCK_ID'],$arResult['CUR_SECTION']['ID']);

        //сео параметры
        $ipropValues = new 	Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arResult['CUR_SECTION']['ID']);
        $arParams['SHOW_FILTER']['SEO'] = $ipropValues->getValues();

        shortUrl::setFilter($arResult,$arParams, $arShowFilter[ shortUrl::FIELD_NAME ]);
    }


    $perPage = MHT\CatalogPerPage::getInstance();
    $sorter  = MHT\CatalogSort::getInstance();

    if (isset($arParams['FILTER_NAME'])) {
        $index           = $arParams['FILTER_NAME'];
        $value           = @$GLOBALS[$index];
        $GLOBALS[$index] = array_merge(
            empty($value) ? array() : $value,
            array(/*			'>CATALOG_PRICE_1' => 0,
			'>CATAL4OG_QUANTITY' => 0*/
            )
        );
    }

    if ($arResult['CUR_SECTION']==[] && $arResult['VARIABLES']['SECTION_CODE_PATH']){
        CHTTP::SetStatus("404 Not Found");
        LocalRedirect('https://'.SITE_SERVER_NAME.'/404.php', false, "404 Not Found");
    }

    $arCurSection = $arResult['CUR_SECTION']; //задается в result_modifier.php


    ob_start();
    if (is_null($arParams['SHOW_FILTER'])) {
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.smart.filter",
            'only_price',
            Array(
                "IBLOCK_TYPE"           => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID"             => $arParams["IBLOCK_ID"],
                "SECTION_ID"            => $arResult["VARIABLES"]["SECTION_ID"],
                "FILTER_NAME"           => $arParams["FILTER_NAME"],
                "PRICE_CODE"            => $arParams["PRICE_CODE"],
                "CACHE_TYPE"            => $arParams["CACHE_TYPE"],
                "CACHE_TIME"            => $arParams["CACHE_TIME"],
                "CACHE_GROUPS"          => $arParams["CACHE_GROUPS"],
                'HIDE_NOT_AVAILABLE'    => $arParams["HIDE_NOT_AVAILABLE"],
                "TEMPLATE_THEME"        => $arParams["TEMPLATE_THEME"],
                "SAVE_IN_SESSION"       => "N",
                "XML_EXPORT"            => "Y",
                "SECTION_TITLE"         => "NAME",
                "SECTION_DESCRIPTION"   => "DESCRIPTION",
                "DISPLAY_ELEMENT_COUNT" => "Y",
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    );
    }

    GLOBAL $USER;
    $GLOBALS['smartFilterHTML'] = ob_get_clean();



$isAjaxRequest = ($arParams['AJAX_MODE'] == 'Y' && isset($_GET['bxajaxid']));

if ($isAjaxRequest) {

    $APPLICATION->RestartBuffer();

    ?><?
    $APPLICATION->IncludeComponent(
	"itsfera:catalog.section",
	"",
	array(
		"SHOW_ALL_WO_SECTION" => 'Y',
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => 'PROPERTY_IS_IN_STOCK',
		"ELEMENT_SORT_ORDER" => 'DESC',
        "ELEMENT_SORT_FIELD2" => 'PROPERTY_SAYT_NOVINKA',
        "ELEMENT_SORT_ORDER2" => 'DESC',
        "ELEMENT_SORT_FIELD3" => $sorter->get('field'),
        "ELEMENT_SORT_ORDER3" => $sorter->get('order'),

		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
		"PAGE_ELEMENT_COUNT" => $perPage->get(),
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"SEF_FOLDER" => $arParams["SEF_FOLDER"],

		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

		'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
		'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
		'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
		'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
		'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
		'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
		'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
		'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

		'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
		"ADD_SECTIONS_CHAIN" => "N",

		'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
		'FAV_PRODUCTS' => MHT\Product::_get('fav_cache'),
        "AJAX_MODE"                 => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_SHADOW" => "N",
        "AJAX_OPTION_STYLE" => "N"
	),
	$component
);?>

    <?
} else {



    ?>
    <div class="catalog_page">
    <div class="only-mobile">
        <a href="#catalog-menu" class="catalog-button js-menu-toggler">Каталог</a>
    </div>
    <div class="catalog_block">

        <div class="catalog_menu">

            <?

            //если текущий раздел находится на 2 уровне, то устанавливаем родительский раздел как корневой для компонента catalog.section.list
            $iSidebarParentSectionId = $arResult["VARIABLES"]["SECTION_ID"];
            if ((isset($arCurSection['IBLOCK_SECTION_ID']) && isset($arCurSection['DEPTH_LEVEL'])
                 && ($arCurSection['DEPTH_LEVEL'] == 3 && $arCurSection['IBLOCK_SECTION_ID'] > 0))
                || ! $arCurSection['HAS_SUBSECTIONS'] //если нет подразделов выводим верхний уровень
            ) {
                $iSidebarParentSectionId = $arCurSection['IBLOCK_SECTION_ID'];
            }

            ?>
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "left_sidebar",
                array(
                    "ITEMS_IN_BLOCK" => 8,

                    "FOR_CACHE_CHANGE" => $arResult["VARIABLES"]["SECTION_ID"], //для изменение кэша

                    "SECTION_ID"   => $iSidebarParentSectionId, //
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],

                    "IBLOCK_TYPE"        => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID"          => $arParams["IBLOCK_ID"],
                    "CACHE_TYPE"         => $arParams["CACHE_TYPE"],
                    "CACHE_TIME"         => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS"       => $arParams["CACHE_GROUPS"],
                    "COUNT_ELEMENTS"     => "Y",//$arParams["SECTION_COUNT_ELEMENTS"],
                    "TOP_DEPTH"          => 1, //$arParams["SECTION_TOP_DEPTH"],
                    "SECTION_URL"        => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "VIEW_MODE"          => $arParams["SECTIONS_VIEW_MODE"],
                    "SHOW_PARENT_NAME"   => $arParams["SECTIONS_SHOW_PARENT_NAME"],
                    "HIDE_SECTION_NAME"  => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                    "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
                    "AJAX_MODE" => "N",
                ),
                $component
            );
            ?>
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                'only_brands',
                Array(
                    "ITEMS_IN_BLOCK"        => 8,
                    "IBLOCK_TYPE"           => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID"             => $arParams["IBLOCK_ID"],
                    "SECTION_ID"            => $arResult["VARIABLES"]["SECTION_ID"],
                    "FILTER_NAME"           => $arParams["FILTER_NAME"],
                    "PRICE_CODE"            => $arParams["PRICE_CODE"],
                    "CACHE_TYPE"            => $arParams["CACHE_TYPE"],
                    "CACHE_TIME"            => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS"          => $arParams["CACHE_GROUPS"],
                    'HIDE_NOT_AVAILABLE'    => $arParams["HIDE_NOT_AVAILABLE"],
                    "TEMPLATE_THEME"        => $arParams["TEMPLATE_THEME"],
                    "SAVE_IN_SESSION"       => "N",
                    "XML_EXPORT"            => "Y",
                    "SECTION_TITLE"         => "NAME",
                    "SECTION_DESCRIPTION"   => "DESCRIPTION",
                    "DISPLAY_ELEMENT_COUNT" => "Y",
                    "AJAX_MODE" => "N",
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            ); ?>
        </div>
        <div class="catalog">
            <h1>
                <?
                if (isset($arParams["IBLOCK_ID"], $arResult['CUR_SECTION']['ID'])) {
                    $ipropValues = new 	Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arResult['CUR_SECTION']['ID']);
                    $arSeo = $ipropValues->getValues();
                    echo $arSeo['SECTION_PAGE_TITLE'];
                } else {
                    $arResult['IPROPERTY_VALUES'] = Itsfera::getSeoParamsForIblock($arResult);
                    echo $arResult['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'];
                }
                ?>
            </h1>

            <?$APPLICATION->IncludeComponent(
            "itsfera:sections_banners",
            "",
            Array(
            )
            );?>

            <div class="filter_block">
                <div class="filter_block_top">
                    <div class="sort_block">
                        <span class="sort_block_title">сортировать по</span>
                        <div class="sort_block_list">
                            <select id="change_sort">
                                <?= $sorter->getOptions() ?>
                            </select>
                        </div>
                    </div>

                    <?
                    echo $GLOBALS['smartFilterHTML'];
                    unset($GLOBALS['smartFilterHTML']);
                    ?>


                    <div class="product_count">
                        <span class="product_count_title">выводить по</span><!--
		                    -->
                        <div class="product_count_list">
                            <select id="change_per_page">
                                <?= $perPage->getOptions() ?>
                            </select>
                        </div>
                    </div>
                    <div class="group_block js-change-catalog-view">
                        <div class="block_group js-trigger<?
                        if ( ! empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])) { ?> active<?
                        } ?>"><a href="#"></a></div>
                        <div class="col_group js-trigger<?
                        if (empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])) { ?> active<?
                        } ?>"><a href="#"></a></div>
                    </div>
                </div>
            </div>
            <?
            if ($_GET['set_filter']) { ?>
                <a href="<?= $APPLICATION->GetCurPage(); ?>" class="resetfilter">Сбросить фильтр</a>
            <?
            } ?>



            <?
            $intSectionID = $APPLICATION->IncludeComponent(
                "itsfera:catalog.section",
                "",
                array(
                    "SHOW_ALL_WO_SECTION" => 'Y',
                    "IBLOCK_TYPE"         => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID"           => $arParams["IBLOCK_ID"],
                    "ELEMENT_SORT_FIELD"  => 'PROPERTY_IS_IN_STOCK',
                    "ELEMENT_SORT_ORDER"  => 'DESC',
                    "ELEMENT_SORT_FIELD2" => 'PROPERTY_SAYT_NOVINKA',
                    "ELEMENT_SORT_ORDER2" => 'DESC',
                    "ELEMENT_SORT_FIELD3" => $sorter->get('field'),
                    "ELEMENT_SORT_ORDER3" => $sorter->get('order'),

                    "PROPERTY_CODE"             => $arParams["LIST_PROPERTY_CODE"],
                    "META_KEYWORDS"             => $arParams["LIST_META_KEYWORDS"],
                    "META_DESCRIPTION"          => $arParams["LIST_META_DESCRIPTION"],
                    "BROWSER_TITLE"             => $arParams["LIST_BROWSER_TITLE"],
                    "INCLUDE_SUBSECTIONS"       => $arParams["INCLUDE_SUBSECTIONS"],
                    "BASKET_URL"                => $arParams["BASKET_URL"],
                    "ACTION_VARIABLE"           => $arParams["ACTION_VARIABLE"],
                    "PRODUCT_ID_VARIABLE"       => $arParams["PRODUCT_ID_VARIABLE"],
                    "SECTION_ID_VARIABLE"       => $arParams["SECTION_ID_VARIABLE"],
                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                    "PRODUCT_PROPS_VARIABLE"    => $arParams["PRODUCT_PROPS_VARIABLE"],
                    "FILTER_NAME"               => $arParams["FILTER_NAME"],
                    "CACHE_TYPE"                => $arParams["CACHE_TYPE"],
                    "CACHE_TIME"                => $arParams["CACHE_TIME"],
                    "CACHE_FILTER"              => $arParams["CACHE_FILTER"],
                    "CACHE_GROUPS"              => $arParams["CACHE_GROUPS"],
                    "SET_TITLE"                 => $arParams["SET_TITLE"],
                    "SET_META_DESCRIPTION"      => $arParams["SET_META_DESCRIPTION"],
                    "SET_STATUS_404"            => $arParams["SET_STATUS_404"],
                    "SHOW_404" => $arParams["SHOW_404"],
                    "FILE_404" => $arParams["FILE_404"],
                    "DISPLAY_COMPARE"           => $arParams["USE_COMPARE"],
                    "PAGE_ELEMENT_COUNT"        => $perPage->get(),
                    "LINE_ELEMENT_COUNT"        => $arParams["LINE_ELEMENT_COUNT"],
                    "PRICE_CODE"                => $arParams["PRICE_CODE"],
                    "USE_PRICE_COUNT"           => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT"          => $arParams["SHOW_PRICE_COUNT"],
                    "SEF_FOLDER"                => $arParams["SEF_FOLDER"],

                    "PRICE_VAT_INCLUDE"          => $arParams["PRICE_VAT_INCLUDE"],
                    "USE_PRODUCT_QUANTITY"       => $arParams['USE_PRODUCT_QUANTITY'],
                    "ADD_PROPERTIES_TO_BASKET"   => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                    "PRODUCT_PROPERTIES"         => $arParams["PRODUCT_PROPERTIES"],

                    "DISPLAY_TOP_PAGER"               => $arParams["DISPLAY_TOP_PAGER"],
                    "DISPLAY_BOTTOM_PAGER"            => $arParams["DISPLAY_BOTTOM_PAGER"],
                    "PAGER_TITLE"                     => $arParams["PAGER_TITLE"],
                    "PAGER_SHOW_ALWAYS"               => $arParams["PAGER_SHOW_ALWAYS"],
                    "PAGER_TEMPLATE"                  => $arParams["PAGER_TEMPLATE"],
                    "PAGER_DESC_NUMBERING"            => $arParams["PAGER_DESC_NUMBERING"],
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                    "PAGER_SHOW_ALL"                  => $arParams["PAGER_SHOW_ALL"],

                    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                    "OFFERS_FIELD_CODE"      => $arParams["LIST_OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE"   => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                    "OFFERS_SORT_FIELD"      => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER"      => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2"     => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2"     => $arParams["OFFERS_SORT_ORDER2"],
                    "OFFERS_LIMIT"           => $arParams["LIST_OFFERS_LIMIT"],

                    "SECTION_ID"         => $arResult["VARIABLES"]["SECTION_ID"],
                    "SECTION_CODE"       => $arResult["VARIABLES"]["SECTION_CODE"],
                    "SECTION_URL"        => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "DETAIL_URL"         => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                    'CONVERT_CURRENCY'   => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID'        => $arParams['CURRENCY_ID'],
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

                    'LABEL_PROP'           => $arParams['LABEL_PROP'],
                    'ADD_PICT_PROP'        => $arParams['ADD_PICT_PROP'],
                    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

                    'OFFER_ADD_PICT_PROP'    => $arParams['OFFER_ADD_PICT_PROP'],
                    'OFFER_TREE_PROPS'       => $arParams['OFFER_TREE_PROPS'],
                    'PRODUCT_SUBSCRIPTION'   => $arParams['PRODUCT_SUBSCRIPTION'],
                    'SHOW_DISCOUNT_PERCENT'  => $arParams['SHOW_DISCOUNT_PERCENT'],
                    'SHOW_OLD_PRICE'         => $arParams['SHOW_OLD_PRICE'],
                    'MESS_BTN_BUY'           => $arParams['MESS_BTN_BUY'],
                    'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                    'MESS_BTN_SUBSCRIBE'     => $arParams['MESS_BTN_SUBSCRIBE'],
                    'MESS_BTN_DETAIL'        => $arParams['MESS_BTN_DETAIL'],
                    'MESS_NOT_AVAILABLE'     => $arParams['MESS_NOT_AVAILABLE'],

                    'TEMPLATE_THEME'     => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                    "ADD_SECTIONS_CHAIN" => "N",

                    'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
                    'FAV_PRODUCTS'              => MHT\Product::_get('fav_cache'),
                    "AJAX_MODE"                 => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_SHADOW" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "SHOW_FILTER" => ( $arParams['SHOW_FILTER'] ? $arParams['SHOW_FILTER'] : false)
                ),
                $component
            );
            ?></div>
    </div>
    </div><?


    if (isset($arResult['SEO_TEXT']) && ! empty($arResult['SEO_TEXT'])):?>
        <div class="catalog_page">
            <div class="catalog_block">
                <div class="seotext_block">
                    <p><?= $arResult['SEO_TEXT'] ?></p>
                    <div class="viewfulltext"><p>Развернуть</p></div>
                </div>
            </div>
        </div>
    <?endif ?>
    <?

    return;

    if ( ! $arParams['FILTER_VIEW_MODE']) {
        $arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
    }
    $arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');
    $verticalGrid           = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");

    if ($verticalGrid) {
        ?><div class="workarea grid2x1"><?
    }
    if ($arParams['USE_FILTER'] == 'Y') {


        $arFilter = array(
            "IBLOCK_ID"     => $arParams["IBLOCK_ID"],
            "ACTIVE"        => "Y",
            "GLOBAL_ACTIVE" => "Y",
        );
        if (0 < intval($arResult["VARIABLES"]["SECTION_ID"])) {
            $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
        } else if ('' != $arResult["VARIABLES"]["SECTION_CODE"]) {
            $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
        }


        if ($verticalGrid) {
            ?><div class="bx_sidebar"><?
        }
        WP::log(Array(
            "IBLOCK_TYPE"         => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID"           => $arParams["IBLOCK_ID"],
            "SECTION_ID"          => $arCurSection['ID'],
            "FILTER_NAME"         => $arParams["FILTER_NAME"],
            "PRICE_CODE"          => $arParams["PRICE_CODE"],
            "CACHE_TYPE"          => $arParams["CACHE_TYPE"],
            "CACHE_TIME"          => $arParams["CACHE_TIME"],
            "CACHE_GROUPS"        => $arParams["CACHE_GROUPS"],
            "SAVE_IN_SESSION"     => "N",
            "XML_EXPORT"          => "Y",
            "SECTION_TITLE"       => "NAME",
            "SECTION_DESCRIPTION" => "DESCRIPTION",
            'HIDE_NOT_AVAILABLE'  => $arParams["HIDE_NOT_AVAILABLE"],
            "TEMPLATE_THEME"      => $arParams["TEMPLATE_THEME"]
        ));
        ?><?
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.smart.filter",
            "visual_" . ($arParams["FILTER_VIEW_MODE"] == "HORIZONTAL" ? "horizontal" : "vertical"),
            Array(
                "IBLOCK_TYPE"         => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID"           => $arParams["IBLOCK_ID"],
                "SECTION_ID"          => $arCurSection['ID'],
                "FILTER_NAME"         => $arParams["FILTER_NAME"],
                "PRICE_CODE"          => $arParams["PRICE_CODE"],
                "CACHE_TYPE"          => $arParams["CACHE_TYPE"],
                "CACHE_TIME"          => $arParams["CACHE_TIME"],
                "CACHE_GROUPS"        => $arParams["CACHE_GROUPS"],
                "SAVE_IN_SESSION"     => "N",
                "XML_EXPORT"          => "Y",
                "SECTION_TITLE"       => "NAME",
                "SECTION_DESCRIPTION" => "DESCRIPTION",
                'HIDE_NOT_AVAILABLE'  => $arParams["HIDE_NOT_AVAILABLE"],
                "TEMPLATE_THEME"      => $arParams["TEMPLATE_THEME"],
                "AJAX_MODE" => "N",
            ),
            $component,
            array('HIDE_ICONS' => 'Y')
        ); ?><?
        if ($verticalGrid) {
            ?></div><?
        }
    }
if ($verticalGrid)
{
    ?>
    <div class="bx_content_section"><?
}
    ?><?
    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section.list",
        "",
        array(
            "IBLOCK_TYPE"        => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID"          => $arParams["IBLOCK_ID"],
            "SECTION_ID"         => $arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE"       => $arResult["VARIABLES"]["SECTION_CODE"],
            "CACHE_TYPE"         => $arParams["CACHE_TYPE"],
            //"CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_TIME"         => 3600,
            "CACHE_GROUPS"       => $arParams["CACHE_GROUPS"],
            "COUNT_ELEMENTS"     => $arParams["SECTION_COUNT_ELEMENTS"],
            "TOP_DEPTH"          => $arParams["SECTION_TOP_DEPTH"],
            "SECTION_URL"        => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "VIEW_MODE"          => $arParams["SECTIONS_VIEW_MODE"],
            "SHOW_PARENT_NAME"   => $arParams["SECTIONS_SHOW_PARENT_NAME"],
            "HIDE_SECTION_NAME"  => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
            "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
            "AJAX_MODE" => "N",
        ),
        $component
    ); ?><?
    if ($arParams["USE_COMPARE"] == "Y") {
        ?><?
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.compare.list",
            "",
            array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID"   => $arParams["IBLOCK_ID"],
                "NAME"        => $arParams["COMPARE_NAME"],
                "DETAIL_URL"  => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                "COMPARE_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["compare"],
                "AJAX_MODE" => "N"
            ),
            $component
        ); ?><?
    }

    $intSectionID = 0;

    ?><?
    $intSectionID = $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "",
        array(
            "IBLOCK_TYPE"               => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID"                 => $arParams["IBLOCK_ID"],
            "ELEMENT_SORT_FIELD"        => $arParams["ELEMENT_SORT_FIELD"],
            "ELEMENT_SORT_ORDER"        => $arParams["ELEMENT_SORT_ORDER"],
            "ELEMENT_SORT_FIELD2"       => $arParams["ELEMENT_SORT_FIELD2"],
            "ELEMENT_SORT_ORDER2"       => $arParams["ELEMENT_SORT_ORDER2"],
            "PROPERTY_CODE"             => $arParams["LIST_PROPERTY_CODE"],
            "META_KEYWORDS"             => $arParams["LIST_META_KEYWORDS"],
            "META_DESCRIPTION"          => $arParams["LIST_META_DESCRIPTION"],
            "BROWSER_TITLE"             => $arParams["LIST_BROWSER_TITLE"],
            "INCLUDE_SUBSECTIONS"       => $arParams["INCLUDE_SUBSECTIONS"],
            "BASKET_URL"                => $arParams["BASKET_URL"],
            "ACTION_VARIABLE"           => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE"       => $arParams["PRODUCT_ID_VARIABLE"],
            "SECTION_ID_VARIABLE"       => $arParams["SECTION_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE"    => $arParams["PRODUCT_PROPS_VARIABLE"],
            "FILTER_NAME"               => $arParams["FILTER_NAME"],
            "CACHE_TYPE"                => $arParams["CACHE_TYPE"],
            "CACHE_TIME"                => $arParams["CACHE_TIME"],
            "CACHE_FILTER"              => $arParams["CACHE_FILTER"],
            "CACHE_GROUPS"              => $arParams["CACHE_GROUPS"],
            "SET_TITLE"                 => $arParams["SET_TITLE"],
            "SET_STATUS_404"            => $arParams["SET_STATUS_404"],
            "DISPLAY_COMPARE"           => $arParams["USE_COMPARE"],
            "PAGE_ELEMENT_COUNT"        => $arParams["PAGE_ELEMENT_COUNT"],
            "LINE_ELEMENT_COUNT"        => $arParams["LINE_ELEMENT_COUNT"],
            "PRICE_CODE"                => $arParams["PRICE_CODE"],
            "USE_PRICE_COUNT"           => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT"          => $arParams["SHOW_PRICE_COUNT"],

            "PRICE_VAT_INCLUDE"          => $arParams["PRICE_VAT_INCLUDE"],
            "USE_PRODUCT_QUANTITY"       => $arParams['USE_PRODUCT_QUANTITY'],
            "ADD_PROPERTIES_TO_BASKET"   => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
            "PRODUCT_PROPERTIES"         => $arParams["PRODUCT_PROPERTIES"],

            "DISPLAY_TOP_PAGER"               => $arParams["DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER"            => $arParams["DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE"                     => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS"               => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE"                  => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING"            => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL"                  => $arParams["PAGER_SHOW_ALL"],

            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
            "OFFERS_FIELD_CODE"      => $arParams["LIST_OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE"   => $arParams["LIST_OFFERS_PROPERTY_CODE"],
            "OFFERS_SORT_FIELD"      => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER"      => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2"     => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2"     => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT"           => $arParams["LIST_OFFERS_LIMIT"],

            "SECTION_ID"         => $arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE"       => $arResult["VARIABLES"]["SECTION_CODE"],
            "SECTION_URL"        => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "DETAIL_URL"         => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
            'CONVERT_CURRENCY'   => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID'        => $arParams['CURRENCY_ID'],
            'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

            'LABEL_PROP'           => $arParams['LABEL_PROP'],
            'ADD_PICT_PROP'        => $arParams['ADD_PICT_PROP'],
            'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

            'OFFER_ADD_PICT_PROP'    => $arParams['OFFER_ADD_PICT_PROP'],
            'OFFER_TREE_PROPS'       => $arParams['OFFER_TREE_PROPS'],
            'PRODUCT_SUBSCRIPTION'   => $arParams['PRODUCT_SUBSCRIPTION'],
            'SHOW_DISCOUNT_PERCENT'  => $arParams['SHOW_DISCOUNT_PERCENT'],
            'SHOW_OLD_PRICE'         => $arParams['SHOW_OLD_PRICE'],
            'MESS_BTN_BUY'           => $arParams['MESS_BTN_BUY'],
            'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
            'MESS_BTN_SUBSCRIBE'     => $arParams['MESS_BTN_SUBSCRIBE'],
            'MESS_BTN_DETAIL'        => $arParams['MESS_BTN_DETAIL'],
            'MESS_NOT_AVAILABLE'     => $arParams['MESS_NOT_AVAILABLE'],

            'TEMPLATE_THEME'     => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
            "ADD_SECTIONS_CHAIN" => "N",
            "AJAX_MODE" => "N"
        ),
        $component
    ); ?><?
    if ($verticalGrid) {
        ?></div>
        <div style="clear: both;"></div>
        </div><?
    }
    ?>
    <?
    if (\Bitrix\Main\ModuleManager::isModuleInstalled("sale")) {
        $arRecomData  = array();
        $recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
        $obCache      = new CPHPCache();
        if ($obCache->InitCache(36000, serialize($recomCacheID), "/sale/bestsellers")) {
            $arRecomData = $obCache->GetVars();
        } else if ($obCache->StartDataCache()) {
            if (\Bitrix\Main\Loader::includeModule("catalog")) {
                $arSKU                          = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
                $arRecomData['OFFER_IBLOCK_ID'] = (! empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
            }
            $obCache->EndDataCache($arRecomData);
        }
        if ( ! empty($arRecomData)) {
            ?><?
            $APPLICATION->IncludeComponent("bitrix:sale.bestsellers", ".default", array(
                "HIDE_NOT_AVAILABLE"                                  => $arParams["HIDE_NOT_AVAILABLE"],
                "PAGE_ELEMENT_COUNT"                                  => "4",
                "SHOW_DISCOUNT_PERCENT"                               => $arParams['SHOW_DISCOUNT_PERCENT'],
                "PRODUCT_SUBSCRIPTION"                                => $arParams['PRODUCT_SUBSCRIPTION'],
                "SHOW_NAME"                                           => "Y",
                "SHOW_IMAGE"                                          => "Y",
                "MESS_BTN_BUY"                                        => $arParams['MESS_BTN_BUY'],
                "MESS_BTN_DETAIL"                                     => $arParams['MESS_BTN_DETAIL'],
                "MESS_NOT_AVAILABLE"                                  => $arParams['MESS_NOT_AVAILABLE'],
                "MESS_BTN_SUBSCRIBE"                                  => $arParams['MESS_BTN_SUBSCRIBE'],
                "LINE_ELEMENT_COUNT"                                  => 4,
                "TEMPLATE_THEME"                                      => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                "DETAIL_URL"                                          => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                "CACHE_TYPE"                                          => $arParams["CACHE_TYPE"],
                "CACHE_TIME"                                          => $arParams["CACHE_TIME"],
                "BY"                                                  => array(
                    0 => "AMOUNT",
                ),
                "PERIOD"                                              => array(
                    0 => "15",
                ),
                "FILTER"                                              => array(
                    0 => "CANCELED",
                    1 => "ALLOW_DELIVERY",
                    2 => "PAYED",
                    3 => "DEDUCTED",
                    4 => "N",
                    5 => "P",
                    6 => "F",
                ),
                "FILTER_NAME"                                         => $arParams["FILTER_NAME"],
                "ORDER_FILTER_NAME"                                   => "arOrderFilter",
                "DISPLAY_COMPARE"                                     => $arParams["USE_COMPARE"],
                "SHOW_OLD_PRICE"                                      => $arParams['SHOW_OLD_PRICE'],
                "PRICE_CODE"                                          => $arParams["PRICE_CODE"],
                "SHOW_PRICE_COUNT"                                    => $arParams["SHOW_PRICE_COUNT"],
                "PRICE_VAT_INCLUDE"                                   => $arParams["PRICE_VAT_INCLUDE"],
                "CONVERT_CURRENCY"                                    => $arParams['CONVERT_CURRENCY'],
                "BASKET_URL"                                          => $arParams["BASKET_URL"],
                "ACTION_VARIABLE"                                     => $arParams["ACTION_VARIABLE"],
                "PRODUCT_ID_VARIABLE"                                 => $arParams["PRODUCT_ID_VARIABLE"],
                "PRODUCT_QUANTITY_VARIABLE"                           => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "ADD_PROPERTIES_TO_BASKET"                            => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                "PRODUCT_PROPS_VARIABLE"                              => $arParams["PRODUCT_PROPS_VARIABLE"],
                "PARTIAL_PRODUCT_PROPERTIES"                          => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                "USE_PRODUCT_QUANTITY"                                => $arParams['USE_PRODUCT_QUANTITY'],
                "SHOW_PRODUCTS_" . $arParams["IBLOCK_ID"]             => "Y",
                "OFFER_TREE_PROPS_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
                "AJAX_MODE" => "N",
            ),
                $component
            );
        }
    }
    ?>

    <?
}
    ?>