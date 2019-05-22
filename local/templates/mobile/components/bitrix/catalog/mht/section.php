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


ob_start();

$APPLICATION->IncludeComponent(
	"bitrix:catalog.smart.filter",
	'mht',
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
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);
$GLOBALS['smartFilterHTML'] = ob_get_clean();

$iSectionId = $arSectionInfo = false;
if (isset( $arResult['VARIABLES']['SECTION_ID']) && !empty( $arResult['VARIABLES']['SECTION_ID'] ) ){
	//$iSectionId = $arResult['VARIABLES']['SECTION_ID'];
	//$arSectionInfo = MobileCatalog::getSectionInfo($iSectionId);
	//замена в рамках задачи #4062
	$ipropValues = new 	Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arResult['VARIABLES']['SECTION_ID']);
	$arSeo = $ipropValues->getValues();
	$arSectionInfo['NAME'] = $arSeo['SECTION_PAGE_TITLE'];
}else {
	$arIblock = MobileCatalog::checkIblockCode( $_GET['IBLOCK_CODE'] );
}
MobileHTMLHelper::sectionsTree();
?>
<section class="padding">
	
	<?
	/*echo '<pre>';
	print_r($arParams);
	echo '</pre>';*/
	?>

	<div class="headingline inner">
		<p class="heading">
			<?if ($arSectionInfo):?>
				<?php echo $arSectionInfo['NAME'];?>
			<?elseif($arIblock):?>
				<?php echo $arIblock['NAME'];?>
			<?else:?>
				<?$GLOBALS['APPLICATION']->ShowTitle();?>
			<?endif?>
		</p>
	</div>
	<div class="categoryitems">
		<div class="categoryfilterblock">
			<a href="javascript:void(0)" class="togglefilter">Расширенный поиск <i class="flaticon-bottom"></i></a>
			<div class="filterwrap" <?if (isset($_GET['set_filter'])):?>style="display: block;"<?endif?> >
				<?php echo $GLOBALS['smartFilterHTML'];?>
			</div>
		</div>
		<?
		MobileHTMLHelper::getSubsections($arParams["IBLOCK_ID"],$arResult["VARIABLES"]["SECTION_ID"]);?>
		<?$intSectionID = $APPLICATION->IncludeComponent(
			"bitrix:catalog.section",
			"",
			array(
			"SHOW_ALL_WO_SECTION" => 'Y',
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => 'PROPERTY_IS_IN_STOCK',
			"ELEMENT_SORT_ORDER" => 'DESC',
			"ELEMENT_SORT_FIELD2" => $sorter->get('field'),
			"ELEMENT_SORT_ORDER2" => $sorter->get('order'),
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
		);?>
	</div>
</section>