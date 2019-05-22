<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? // Компонент не исполняет шаблон, если список элементов пуст, так что перехватываем здесь
if(!$arParams["COMPARE_NAME"]){ $arParams["COMPARE_NAME"] = 'CATALOG_COMPARE_LIST'; }
if($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"] || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ADD_TO_COMPARE_RESULT')): ?>

<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.result",
	"",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"FIELD_CODE" => $arParams["COMPARE_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["COMPARE_PROPERTY_CODE"],
		"NAME" => $arParams["COMPARE_NAME"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
		"DISPLAY_ELEMENT_SELECT_BOX" => $arParams["DISPLAY_ELEMENT_SELECT_BOX"],
		"ELEMENT_SORT_FIELD_BOX" => $arParams["ELEMENT_SORT_FIELD_BOX"],
		"ELEMENT_SORT_ORDER_BOX" => $arParams["ELEMENT_SORT_ORDER_BOX"],
		"ELEMENT_SORT_FIELD_BOX2" => $arParams["ELEMENT_SORT_FIELD_BOX2"],
		"ELEMENT_SORT_ORDER_BOX2" => $arParams["ELEMENT_SORT_ORDER_BOX2"],
		"ELEMENT_SORT_FIELD" => $arParams["COMPARE_ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["COMPARE_ELEMENT_SORT_ORDER"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"OFFERS_FIELD_CODE" => $arParams["COMPARE_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["COMPARE_OFFERS_PROPERTY_CODE"],
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
	),
	$component
);?>

<? else: ?>

<? $APPLICATION->SetTitle('Сравнение товаров'); ?>
<div class="comparation_page">
      <div class="comparation">
        <h1>Сравнение товаров</h1>

		<div class="styles_page"><div class="styles">
			<div class="no-compare-items-notice">
				<h2>В этой категории товаров список для сравнения пуст.</h2>
				<p>Добавьте товары к сравнению, либо перейдите к другой категории.</p>
			</div>
		</div></div>

        <ul class="categories">
        <?
        	$i = 0;
        	foreach($_SESSION['CATALOG_COMPARE_LIST'] as $id => $data){
        		if(count($data['ITEMS']) == 0)
        			continue;
        		$iblock = WP::iblock(array('filter' => array('ID' => $id)));
        		$active = $arParams['IBLOCK_ID'] == $id;
        		?><li data-index="<?=$i?>" <?=$active ? 'class="active"' : ''?>><a href="<?=$iblock['LIST_PAGE_URL']?>/compare/"><?=$iblock['NAME']?></a> (<?=count($data['ITEMS'])?>)</li><?
        		$i++;
        	}
        ?>
        </ul>
      </div>
</div>

<? endif; ?>