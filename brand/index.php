<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\Application;

$uri = Application::getInstance()->getContext()->getRequest()->getRequestUri();
if ($getStart = strpos($uri, '?')) {
    $uri = substr($uri, 0, $getStart);
}

if ((substr_count($uri, '/') != 3) ||
    (substr($uri, strlen($uri) - 1, 1) != '/')) {

    header('HTTP/1.1 404 Not Found');
    header("Refresh:0; url=../../404.php");
}


$APPLICATION->SetTitle("Бренд");
$sBrand = WP::matchDir('/brand/*/', 0);
$bBrandInfo = false;
$sBrand = Itsfera::convertCode( $sBrand );

//Добавляем ссылку на разден в цепочку
$APPLICATION->AddChainItem('Бренды','/brands/');

?>
<div class="catalog_page">
	<div class="catalog_block"><!--
        --><div class="catalog wide">

            <?
            if ($arBrand = Itsfera::getBrandByCode($sBrand)){
            $bBrandInfo = true;

            //элемент
            $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues( getIBlockIdByCode('brands'), $arBrand["ID"]);
            $arBrandSeo = $ipropValues->getValues();

            $APPLICATION->AddChainItem($arResult['NAME']);
			?>

	<?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"brand_info",
	Array(
		"TITLE"=>isset($arBrandSeo['ELEMENT_PAGE_TITLE'])?$arBrandSeo['ELEMENT_PAGE_TITLE']:"",
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "mht",
		"IBLOCK_ID" => getIBlockIdByCode("brands"),
		"ELEMENT_ID" => "",
		"ELEMENT_CODE" => $sBrand,
		"CHECK_DATES" => "Y",
		"FIELD_CODE" => array("PREVIEW_TEXT", "PREVIEW_PICTURE", ""),
		"PROPERTY_CODE" => array("SEO_TEXT_VALUE", ""),
		"IBLOCK_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "N",
		"SET_TITLE" => "Y",
		"SET_CANONICAL_URL" => "N",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_ELEMENT_CHAIN" => "N",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"USE_PERMISSIONS" => "N",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Страница",
		"PAGER_SHOW_ALL" => "N"
	)
);?>

            <?} else {


// получаем название бренда, если нет такого элемента в ИБ Бренды
//http://moshoztorg.ru/bitrix/admin/iblock_list_admin.php?PAGEN_1=1&SIZEN_1=291&IBLOCK_ID=534&type=mht&lang=ru&find_section_section=0&by=name&order=asc

                    $property_enums = \CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("CODE"=>"CML2_MANUFACTURER"));
                    while($enum_fields = $property_enums->GetNext())
                    {
                        if ( \CUtil::translit($enum_fields["VALUE"],"ru") == $sBrand ) {
                            $arBrand['NAME'] = $enum_fields["VALUE"];
                            break;
                        }
                    }

		$APPLICATION->SetTitle("Продукты бренда ".$arBrand['NAME']);
        $APPLICATION->AddChainItem($arResult['NAME']);
        ?><h1><?=$arBrand['NAME']?></h1><?


            }?>


</div></div></div>


<?

$APPLICATION->IncludeComponent('itsfera:brand_with_filters', '', array(
	'SHOW_NAME'=> !$bBrandInfo,
	'BRAND' => $arBrand['NAME'],
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "/brand/",
	'ADD_CHAIN' => 'Y',
	'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"]
));
?>
<?
if (isset($arBrandSeo['ELEMENT_META_TITLE']) && !empty($arBrandSeo['ELEMENT_META_TITLE'])){

	$GLOBALS['APPLICATION']->SetTitle($arBrandSeo['ELEMENT_META_TITLE']);
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>