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
$this->setFrameMode(true);

$perPage = MHT\CatalogPerPage::getInstance();
$sorter = MHT\CatalogSort::getInstance();

if(isset($arParams['FILTER_NAME'])){
	$index = $arParams['FILTER_NAME'];
	$value = @$GLOBALS[$index];
	$GLOBALS[$index] = array_merge(
		empty($value) ? array() : $value,
		array(
/*			'>CATALOG_PRICE_1' => 0,
			'>CATAL4OG_QUANTITY' => 0*/
		)
	);
}

$arCurSection = $arResult['CUR_SECTION']; //задается в result_modifier.php



ob_start();
$APPLICATION->IncludeComponent(
	"bitrix:catalog.smart.filter",
	'only_price',
	Array(
		"IBLOCK_TYPE"         => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"           => $arParams["IBLOCK_ID"],
		"SECTION_ID"          => $arResult["VARIABLES"]["SECTION_ID"],
		"FILTER_NAME"         => $arParams["FILTER_NAME"],
		"PRICE_CODE"          => $arParams["PRICE_CODE"],
		"CACHE_TYPE"          => $arParams["CACHE_TYPE"],
		"CACHE_TIME"          => $arParams["CACHE_TIME"],
		"CACHE_GROUPS"        => $arParams["CACHE_GROUPS"],
		'HIDE_NOT_AVAILABLE'  => $arParams["HIDE_NOT_AVAILABLE"],
		"TEMPLATE_THEME"      => $arParams["TEMPLATE_THEME"],
		"SAVE_IN_SESSION"     => "N",
		"XML_EXPORT"          => "Y",
		"SECTION_TITLE"       => "NAME",
		"SECTION_DESCRIPTION" => "DESCRIPTION",
		"DISPLAY_ELEMENT_COUNT" => "Y",
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);
GLOBAL $USER;
$GLOBALS['smartFilterHTML'] = ob_get_clean();
?>

<div class="catalog_page">
	<div class="catalog_block">
		<div class="section-title-block">
			<h1><?=$arCurSection['NAME'] ? $arCurSection['NAME'] : $arResult['IBLOCK']['NAME']?></h1>
		</div>
		<div class="slider-block">
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list", 
				"slider-khimiya", 
				array(
					"COMPONENT_TEMPLATE" => "slider-khimiya",
					"IBLOCK_TYPE" => "mht",
					"IBLOCK_ID" => "553",
					"NEWS_COUNT" => "20",
					"SORT_BY1" => "ACTIVE_FROM",
					"SORT_ORDER1" => "DESC",
					"SORT_BY2" => "SORT",
					"SORT_ORDER2" => "ASC",
					"FILTER_NAME" => "",
					"FIELD_CODE" => array(
						0 => "",
						1 => "",
					),
					"PROPERTY_CODE" => array(
						0 => "",
						1 => "SLIDER_LINK",
						2 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_LAST_MODIFIED" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "24968",
					"PARENT_SECTION_CODE" => "sliders",
					"INCLUDE_SUBSECTIONS" => "Y",
					"DISPLAY_DATE" => "Y",
					"DISPLAY_NAME" => "Y",
					"DISPLAY_PICTURE" => "Y",
					"DISPLAY_PREVIEW_TEXT" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"SET_STATUS_404" => "N",
					"SHOW_404" => "N",
					"MESSAGE_404" => ""
				),
				false
			);?>
			<div class="static-pics">
				<?$APPLICATION->IncludeComponent(
				"bitrix:news.list", 
				"banner-khimiya", 
				array(
					"COMPONENT_TEMPLATE" => "banner-khimiya",
					"IBLOCK_TYPE" => "mht",
					"IBLOCK_ID" => "553",
					"NEWS_COUNT" => "2",
					"SORT_BY1" => "ACTIVE_FROM",
					"SORT_ORDER1" => "DESC",
					"SORT_BY2" => "SORT",
					"SORT_ORDER2" => "ASC",
					"FILTER_NAME" => "",
					"FIELD_CODE" => array(
						0 => "",
						1 => "",
					),
					"PROPERTY_CODE" => array(
						0 => "",
						1 => "SLIDER_LINK",
						2 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_LAST_MODIFIED" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "24969",
					"PARENT_SECTION_CODE" => "banners",
					"INCLUDE_SUBSECTIONS" => "Y",
					"DISPLAY_DATE" => "Y",
					"DISPLAY_NAME" => "Y",
					"DISPLAY_PICTURE" => "Y",
					"DISPLAY_PREVIEW_TEXT" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"SET_STATUS_404" => "N",
					"SHOW_404" => "N",
					"MESSAGE_404" => ""
				),
				false
			);?>
			</div>
		</div>
	</div>

	<?
  	$arFilter = Array('IBLOCK_ID'=>$arResult['IBLOCK']['ID'],'GLOBAL_ACTIVE'=>'Y', 'DEPTH_LEVEL'=>1);
  	$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false);
  	while($ar_result = $db_list->GetNext()){
		$arSections[] = array('NAME'=>$ar_result['NAME'], 'PICTURE'=>$ar_result['PICTURE'], 'SECTION_PAGE_URL'=>$ar_result['SECTION_PAGE_URL']);	
	}

	$arFilter = Array("IBLOCK_ID"=>$arResult['IBLOCK']['ID'], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, Array());
    $elements = $res->SelectedRowsCount();
	?>
	<div class="sections">
		<div class="sections-content">
			<div class="sections-amount"><span><?=$elements;?> <?=true_wordform($elements, 'товар', 'товара', 'товаров');?></span> в <?=count($arSections);?> разделах</div>
			<h2>У нас представлены все ведущие бренды и производители бытовой химии</h2>
			<div class="sections-list">
		  	<?foreach($arSections as $key=>$section):?>
				<?$picture = CFile::GetPath($section["PICTURE"]);?>
				<div>
					<a href="<?=$section['SECTION_PAGE_URL']?>">
						<div class="section-list-img" style="background-image: url(<?=$picture;?>)"></div>
						<div class="subsection-title"><?=$section['NAME'];?></div>
					</a>
				</div>
			<?endforeach;?>		
			</div>
		</div>
	</div>
    <div class="catalog_block">

    	<?
        $arRecomData = array();
        $recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
        $obCache = new CPHPCache();
        if ($obCache->InitCache(36000, serialize($recomCacheID), "/catalog/bytovaya_khimiya"))
        {
            $arRecomData = $obCache->GetVars();
        }
        elseif ($obCache->StartDataCache())
        {
            if (\Bitrix\Main\Loader::includeModule("catalog"))
            {
                $arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
                $arRecomData['OFFER_IBLOCK_ID'] = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
            }
            $obCache->EndDataCache($arRecomData);
        }
        if (!empty($arRecomData)) {

            $APPLICATION->IncludeComponent("bitrix:sale.bestsellers", ".default", array(
                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                "PAGE_ELEMENT_COUNT" => "4",
                "SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
                "PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
                "SHOW_NAME" => "Y",
                "SHOW_IMAGE" => "Y",
                "MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
                "MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
                "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                "LINE_ELEMENT_COUNT" => 4,
                "TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "BY" => array(
                    0 => "AMOUNT",
                ),
                "PERIOD" => array(
                    0 => "15",
                ),
                "FILTER" => array(
                    0 => "CANCELED",
                    1 => "ALLOW_DELIVERY",
                    2 => "PAYED",
                    3 => "DEDUCTED",
                    4 => "N",
                    5 => "P",
                    6 => "F",
                ),
                "FILTER_NAME" => $arParams["FILTER_NAME"],
                "ORDER_FILTER_NAME" => "arOrderFilter",
                "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                "SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                "SHOW_PRODUCTS_" . $arParams["IBLOCK_ID"] => "Y",
                "OFFER_TREE_PROPS_" . $arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"]
            ),
                $component
            );

        }
        ?>

        <?/*$APPLICATION->IncludeComponent('mht:catalog_menu', '', array(
                    'IBLOCK_ID' => $arResult['IBLOCK_ID'],
                    'SECTION_ID' => $arResult['ID']
                ))*/?>

		<?/*
        <div class="catalog_menu">

            <?

            //если текущий раздел находится на 2 уровне, то устанавливаем родительский раздел как корневой для компонента catalog.section.list
            $iSidebarParentSectionId = $arResult["VARIABLES"]["SECTION_ID"];
            if ( ( isset($arCurSection['IBLOCK_SECTION_ID']) && isset($arCurSection['DEPTH_LEVEL'])
            && ($arCurSection['DEPTH_LEVEL']==3 && $arCurSection['IBLOCK_SECTION_ID']>0))
                || !$arCurSection['HAS_SUBSECTIONS'] //если нет подразделов выводим верхний уровень
            ){
                $iSidebarParentSectionId = $arCurSection['IBLOCK_SECTION_ID'];
            }

            //[IBLOCK_SECTION_ID] => 20059
            //[DEPTH_LEVEL] => 2
            ?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "left_sidebar",
                array(
                    "ITEMS_IN_BLOCK"=>8,

                    "FOR_CACHE_CHANGE" => $arResult["VARIABLES"]["SECTION_ID"], //для изменение кэша

                    "SECTION_ID" => $iSidebarParentSectionId, //
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],

                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "COUNT_ELEMENTS" => "Y",//$arParams["SECTION_COUNT_ELEMENTS"],
                    "TOP_DEPTH" => 1, //$arParams["SECTION_TOP_DEPTH"],
                    "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                    "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
                    "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
                    "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                    "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
                ),
                $component
            );
            ?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                'only_brands',
                Array(
                        "ITEMS_IN_BLOCK"=>8,
                    "IBLOCK_TYPE"         => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID"           => $arParams["IBLOCK_ID"],
                    "SECTION_ID"          => $arResult["VARIABLES"]["SECTION_ID"],
                    "FILTER_NAME"         => $arParams["FILTER_NAME"],
                    "PRICE_CODE"          => $arParams["PRICE_CODE"],
                    "CACHE_TYPE"          => $arParams["CACHE_TYPE"],
                    "CACHE_TIME"          => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS"        => $arParams["CACHE_GROUPS"],
                    'HIDE_NOT_AVAILABLE'  => $arParams["HIDE_NOT_AVAILABLE"],
                    "TEMPLATE_THEME"      => $arParams["TEMPLATE_THEME"],
                    "SAVE_IN_SESSION"     => "N",
                    "XML_EXPORT"          => "Y",
                    "SECTION_TITLE"       => "NAME",
                    "SECTION_DESCRIPTION" => "DESCRIPTION",
					"DISPLAY_ELEMENT_COUNT" => "Y",
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );?>
        </div>
		*/?>

        <div class="catalog-khimiya catalog">
            <div class="filter_block">
                <div class="filter_block_top">
                    <div class="sort_block">
                        <span class="sort_block_title">сортировать по</span>
                        <div class="sort_block_list">
                            <select id="change_sort">
                                <?=$sorter->getOptions()?>
                            </select>
                        </div>
                    </div>
					
                    <?
                    echo $GLOBALS['smartFilterHTML'];
                    unset($GLOBALS['smartFilterHTML']);
                    ?>
                    
					
                    <?/*
                    <div class="filter-prices">
                        <label>Цена:</label>
                        <div class="range_col1">
                            <div class="filter_title_block">
                                <div class="filter_title">от</div>
                                <input class="cost-min" value="20" data-sel-value="20" name="arrFilter_P1_MIN" type="text" id="minCost" disabled="disabled">
                            </div>
                            <div class="filter_title_block">
                                <div class="filter_title">до</div>
                                <input class="cost-max" value="13283" data-sel-value="13283" name="arrFilter_P1_MAX" type="text" id="maxCost" disabled="disabled">
                            </div>
                        </div>
                        <div class="range_col2">
                            <div class="cost_range ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 0%;"></span><span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0" style="left: 100%;"></span></div>
                        </div>
                    </div>*/?>
                    <!--
                    --><div class="product_count">
                        <span class="product_count_title">выводить по</span><!--
		                    --><div class="product_count_list">
                            <select id="change_per_page">
                                <?=$perPage->getOptions()?>
                            </select>
                        </div>
                    </div>
                    <div class="group_block js-change-catalog-view"><div class="block_group js-trigger<?if(!empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> active<?}?>"><a href="#"></a></div><div class="col_group js-trigger<?if(empty($_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"])){?> active<?}?>"><a href="#"></a></div></div>
                </div>
			</div>
			<?if($_GET['set_filter']){?>
			<a href="<?=$APPLICATION->GetCurPage();?>" class="resetfilter">Сбросить фильтр</a>
			<?}?>



            <?
$intSectionID = $APPLICATION->IncludeComponent(
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
		'FAV_PRODUCTS' => MHT\Product::_get('fav_cache')
	),
	$component
);
?></div>
    </div>
    </div><?


if (isset($arResult['SEO_TEXT']) && !empty($arResult['SEO_TEXT'])):?>
	<div class="catalog_page"><div class="catalog_block">
		<div class="seotext_block">
			<p><?=$arResult['SEO_TEXT']?></p>
			<div class="viewfulltext"><p>Развернуть</p></div>
		</div>
	</div></div>
<?endif?>
<?

return;

if (!$arParams['FILTER_VIEW_MODE']){
	$arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
}
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');
$verticalGrid = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");

if ($verticalGrid)
{
	?><div class="workarea grid2x1"><?
}
if ($arParams['USE_FILTER'] == 'Y')
{


	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);
	if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
	{
		$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
	}
	elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
	{
		$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
	}


	//$arCurSection - определяется сверху

	/*$obCache = new CPHPCache();
	if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
	{
		$arCurSection = $obCache->GetVars();
	}
	elseif ($obCache->StartDataCache())
	{
		$arCurSection = array();
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID"));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/iblock/catalog");

				if ($arCurSection = $dbRes->Fetch())
				{
					$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
				}
				$CACHE_MANAGER->EndTagCache();
			}
			else
			{
				if(!$arCurSection = $dbRes->Fetch())
					$arCurSection = array();
			}
		}
		$obCache->EndDataCache($arCurSection);
	}
	if (!isset($arCurSection))
	{
		$arCurSection = array();
	}*/




	if ($verticalGrid)
	{
		?><div class="bx_sidebar"><?
	}
	WP::log(Array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_ID" => $arCurSection['ID'],
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SAVE_IN_SESSION" => "N",
			"XML_EXPORT" => "Y",
			"SECTION_TITLE" => "NAME",
			"SECTION_DESCRIPTION" => "DESCRIPTION",
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
			"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"]
		));
	?><?$APPLICATION->IncludeComponent(
		"bitrix:catalog.smart.filter",
		"visual_".($arParams["FILTER_VIEW_MODE"] == "HORIZONTAL" ? "horizontal" : "vertical"),
		Array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_ID" => $arCurSection['ID'],
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SAVE_IN_SESSION" => "N",
			"XML_EXPORT" => "Y",
			"SECTION_TITLE" => "NAME",
			"SECTION_DESCRIPTION" => "DESCRIPTION",
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
			"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"]
		),
		$component,
		array('HIDE_ICONS' => 'Y')
	);?><?
	if ($verticalGrid)
	{
		?></div><?
	}
}
if ($verticalGrid)
{
	?><div class="bx_content_section"><?
}
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		//"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_TIME" => 3600,
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
		"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
		"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
	),
	$component
);?><?
if($arParams["USE_COMPARE"]=="Y")
{
	?><?$APPLICATION->IncludeComponent(
		"bitrix:catalog.compare.list",
		"",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"NAME" => $arParams["COMPARE_NAME"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
		),
		$component
	);?><?
}

$intSectionID = 0;

?><?$intSectionID = $APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
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
		"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

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
		"ADD_SECTIONS_CHAIN" => "N"
	),
	$component
);?><?
if ($verticalGrid)
{
	?></div>
	<div style="clear: both;"></div>
</div><?
}
?>
<?
if (\Bitrix\Main\ModuleManager::isModuleInstalled("sale"))
{
	$arRecomData = array();
	$recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
	$obCache = new CPHPCache();
	if ($obCache->InitCache(36000, serialize($recomCacheID), "/sale/bestsellers"))
	{
		$arRecomData = $obCache->GetVars();
	}
	elseif ($obCache->StartDataCache())
	{
		if (\Bitrix\Main\Loader::includeModule("catalog"))
		{
			$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
			$arRecomData['OFFER_IBLOCK_ID'] = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
		}
		$obCache->EndDataCache($arRecomData);
	}
	if (!empty($arRecomData))
	{
		?><?$APPLICATION->IncludeComponent("bitrix:sale.bestsellers", ".default", array(
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"PAGE_ELEMENT_COUNT" => "4",
			"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
			"PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
			"SHOW_NAME" => "Y",
			"SHOW_IMAGE" => "Y",
			"MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
			"MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
			"MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
			"MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
			"LINE_ELEMENT_COUNT" => 4,
			"TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"BY" => array(
				0 => "AMOUNT",
			),
			"PERIOD" => array(
				0 => "15",
			),
			"FILTER" => array(
				0 => "CANCELED",
				1 => "ALLOW_DELIVERY",
				2 => "PAYED",
				3 => "DEDUCTED",
				4 => "N",
				5 => "P",
				6 => "F",
			),
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"ORDER_FILTER_NAME" => "arOrderFilter",
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
			"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
			"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
			"OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"]
		),
		$component
	);
	}
}
?>
