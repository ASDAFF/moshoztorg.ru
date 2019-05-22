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
  	$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
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

    	<?$APPLICATION->IncludeComponent(
	"bitrix:sale.bestsellers", 
	"bestsellers-slider", 
	array(
		"LINE_ELEMENT_COUNT" => "3",
		"TEMPLATE_THEME" => "blue",
		"BY" => "AMOUNT",
		"PERIOD" => "0",
		"FILTER" => array(
			0 => "N",
		),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"AJAX_MODE" => "N",
		"DETAIL_URL" => "",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"DISPLAY_COMPARE" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"PRICE_CODE" => array(
		),
		"SHOW_PRICE_COUNT" => "1",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PRICE_VAT_INCLUDE" => "Y",
		"USE_PRODUCT_QUANTITY" => "N",
		"SHOW_NAME" => "Y",
		"SHOW_IMAGE" => "Y",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"PAGE_ELEMENT_COUNT" => "30",
		"SHOW_PRODUCTS_3" => "Y",
		"PROPERTY_CODE_3" => array(
			0 => "MANUFACTURER",
			1 => "MATERIAL",
		),
		"CART_PROPERTIES_3" => array(
			0 => "CORNER",
		),
		"ADDITIONAL_PICT_PROP_3" => "MORE_PHOTO",
		"LABEL_PROP_3" => "SPECIALOFFER",
		"PROPERTY_CODE_4" => array(
			0 => "COLOR",
		),
		"CART_PROPERTIES_4" => "",
		"OFFER_TREE_PROPS_4" => array(
			0 => "-",
		),
		"HIDE_NOT_AVAILABLE" => "N",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"COMPONENT_TEMPLATE" => "bestsellers-slider",
		"AJAX_OPTION_ADDITIONAL" => "",
		"SHOW_PRODUCTS_455" => "N",
		"PROPERTY_CODE_455" => array(
		),
		"CART_PROPERTIES_455" => array(
		),
		"ADDITIONAL_PICT_PROP_455" => "MORE_PHOTO",
		"LABEL_PROP_455" => "-",
		"PROPERTY_CODE_456" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_456" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_456" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_456" => "",
		"SHOW_PRODUCTS_457" => "N",
		"PROPERTY_CODE_457" => array(
		),
		"CART_PROPERTIES_457" => array(
		),
		"ADDITIONAL_PICT_PROP_457" => "MORE_PHOTO",
		"LABEL_PROP_457" => "-",
		"SHOW_PRODUCTS_459" => "N",
		"PROPERTY_CODE_459" => array(
		),
		"CART_PROPERTIES_459" => array(
		),
		"ADDITIONAL_PICT_PROP_459" => "MORE_PHOTO",
		"LABEL_PROP_459" => "-",
		"SHOW_PRODUCTS_461" => "N",
		"PROPERTY_CODE_461" => array(
		),
		"CART_PROPERTIES_461" => array(
		),
		"ADDITIONAL_PICT_PROP_461" => "MORE_PHOTO",
		"LABEL_PROP_461" => "-",
		"SHOW_PRODUCTS_463" => "Y",
		"PROPERTY_CODE_463" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_463" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_463" => "MORE_PHOTO",
		"LABEL_PROP_463" => "-",
		"SHOW_PRODUCTS_471" => "N",
		"PROPERTY_CODE_471" => array(
		),
		"CART_PROPERTIES_471" => array(
		),
		"ADDITIONAL_PICT_PROP_471" => "MORE_PHOTO",
		"LABEL_PROP_471" => "-",
		"SHOW_PRODUCTS_473" => "N",
		"PROPERTY_CODE_473" => array(
		),
		"CART_PROPERTIES_473" => array(
		),
		"ADDITIONAL_PICT_PROP_473" => "MORE_PHOTO",
		"LABEL_PROP_473" => "-",
		"SHOW_PRODUCTS_477" => "N",
		"PROPERTY_CODE_477" => array(
		),
		"CART_PROPERTIES_477" => array(
		),
		"ADDITIONAL_PICT_PROP_477" => "MORE_PHOTO",
		"LABEL_PROP_477" => "-",
		"SHOW_PRODUCTS_483" => "N",
		"PROPERTY_CODE_483" => array(
		),
		"CART_PROPERTIES_483" => array(
		),
		"ADDITIONAL_PICT_PROP_483" => "MORE_PHOTO",
		"LABEL_PROP_483" => "-",
		"SHOW_PRODUCTS_493" => "N",
		"PROPERTY_CODE_493" => array(
		),
		"CART_PROPERTIES_493" => array(
		),
		"ADDITIONAL_PICT_PROP_493" => "MORE_PHOTO",
		"LABEL_PROP_493" => "-",
		"SHOW_PRODUCTS_495" => "N",
		"PROPERTY_CODE_495" => array(
		),
		"CART_PROPERTIES_495" => array(
		),
		"ADDITIONAL_PICT_PROP_495" => "MORE_PHOTO",
		"LABEL_PROP_495" => "-",
		"SHOW_PRODUCTS_497" => "N",
		"PROPERTY_CODE_497" => array(
		),
		"CART_PROPERTIES_497" => array(
		),
		"ADDITIONAL_PICT_PROP_497" => "MORE_PHOTO",
		"LABEL_PROP_497" => "-",
		"SHOW_PRODUCTS_499" => "N",
		"PROPERTY_CODE_499" => array(
		),
		"CART_PROPERTIES_499" => array(
		),
		"ADDITIONAL_PICT_PROP_499" => "MORE_PHOTO",
		"LABEL_PROP_499" => "-",
		"SHOW_PRODUCTS_503" => "N",
		"PROPERTY_CODE_503" => array(
		),
		"CART_PROPERTIES_503" => array(
		),
		"ADDITIONAL_PICT_PROP_503" => "MORE_PHOTO",
		"LABEL_PROP_503" => "-",
		"SHOW_PRODUCTS_523" => "N",
		"PROPERTY_CODE_523" => array(
		),
		"CART_PROPERTIES_523" => array(
		),
		"ADDITIONAL_PICT_PROP_523" => "MORE_PHOTO",
		"LABEL_PROP_523" => "-",
		"SHOW_PRODUCTS_525" => "N",
		"PROPERTY_CODE_525" => array(
		),
		"CART_PROPERTIES_525" => array(
		),
		"ADDITIONAL_PICT_PROP_525" => "MORE_PHOTO",
		"LABEL_PROP_525" => "-",
		"SHOW_PRODUCTS_527" => "N",
		"PROPERTY_CODE_527" => array(
		),
		"CART_PROPERTIES_527" => array(
		),
		"ADDITIONAL_PICT_PROP_527" => "MORE_PHOTO",
		"LABEL_PROP_527" => "-",
		"SHOW_PRODUCTS_528" => "N",
		"PROPERTY_CODE_528" => array(
		),
		"CART_PROPERTIES_528" => array(
		),
		"ADDITIONAL_PICT_PROP_528" => "MORE_PHOTO",
		"LABEL_PROP_528" => "-",
		"SHOW_PRODUCTS_536" => "N",
		"PROPERTY_CODE_536" => array(
		),
		"CART_PROPERTIES_536" => array(
		),
		"ADDITIONAL_PICT_PROP_536" => "MORE_PHOTO",
		"LABEL_PROP_536" => "-",
		"SHOW_PRODUCTS_538" => "N",
		"PROPERTY_CODE_538" => array(
		),
		"CART_PROPERTIES_538" => array(
		),
		"ADDITIONAL_PICT_PROP_538" => "MORE_PHOTO",
		"LABEL_PROP_538" => "-",
		"SHOW_PRODUCTS_540" => "N",
		"PROPERTY_CODE_540" => array(
		),
		"CART_PROPERTIES_540" => array(
		),
		"ADDITIONAL_PICT_PROP_540" => "MORE_PHOTO",
		"LABEL_PROP_540" => "-",
		"SHOW_PRODUCTS_542" => "N",
		"PROPERTY_CODE_542" => array(
		),
		"CART_PROPERTIES_542" => array(
		),
		"ADDITIONAL_PICT_PROP_542" => "MORE_PHOTO",
		"LABEL_PROP_542" => "-",
		"SHOW_PRODUCTS_544" => "N",
		"PROPERTY_CODE_544" => array(
		),
		"CART_PROPERTIES_544" => array(
		),
		"ADDITIONAL_PICT_PROP_544" => "MORE_PHOTO",
		"LABEL_PROP_544" => "-",
		"SHOW_PRODUCTS_550" => "N",
		"PROPERTY_CODE_550" => array(
		),
		"CART_PROPERTIES_550" => array(
		),
		"ADDITIONAL_PICT_PROP_550" => "MORE_PHOTO",
		"LABEL_PROP_550" => "-",
		"PROPERTY_CODE_464" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_464" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_464" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_464" => array(
		),
		"PROPERTY_CODE_458" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_458" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_458" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_458" => "",
		"PROPERTY_CODE_460" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_460" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_460" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_460" => "",
		"PROPERTY_CODE_462" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_462" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_462" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_462" => "",
		"PROPERTY_CODE_472" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_472" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_472" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_472" => "",
		"PROPERTY_CODE_474" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_474" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_474" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_474" => "",
		"PROPERTY_CODE_478" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_478" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_478" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_478" => "",
		"PROPERTY_CODE_484" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_484" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_484" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_484" => "",
		"PROPERTY_CODE_494" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_494" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_494" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_494" => "",
		"PROPERTY_CODE_496" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_496" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_496" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_496" => "",
		"PROPERTY_CODE_498" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_498" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_498" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_498" => "",
		"PROPERTY_CODE_500" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_500" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_500" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_500" => "",
		"PROPERTY_CODE_504" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_504" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_504" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_504" => "",
		"PROPERTY_CODE_524" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_524" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_524" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_524" => "",
		"PROPERTY_CODE_526" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_526" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_526" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_526" => "",
		"PROPERTY_CODE_522" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_522" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_522" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_522" => "",
		"PROPERTY_CODE_529" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_529" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_529" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_529" => "",
		"PROPERTY_CODE_537" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_537" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_537" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_537" => "",
		"PROPERTY_CODE_539" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_539" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_539" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_539" => "",
		"PROPERTY_CODE_541" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_541" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_541" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_541" => "",
		"PROPERTY_CODE_543" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_543" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_543" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_543" => "",
		"PROPERTY_CODE_545" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_545" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_545" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_545" => "",
		"PROPERTY_CODE_551" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_551" => array(
			0 => "",
			1 => "",
		),
		"ADDITIONAL_PICT_PROP_551" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_551" => ""
	),
	false
);?>
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
