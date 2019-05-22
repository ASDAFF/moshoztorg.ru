<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Распродажа");
?><?$APPLICATION->IncludeComponent('mht:offers', '', array(
	'NAME' => 'Распродажа',
	'TYPE' => 'SAYT_AKTSIONNYY_TOVAR',
    'SECTION_ID' => $_REQUEST['SECTION_ID'],
	//'SORT_BY' => 'NAME',
	//'SORT_ORDER' => 'ASC',
    'SHOW_SECTIONS' => 'N',
    'SHOW_LINKS_TYPE' => '',
	'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
	'HIDE_ACTION_LABEL' => 'Y'
))?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>