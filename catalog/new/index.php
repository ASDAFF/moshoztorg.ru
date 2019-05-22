<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новинки");
?>
<?$APPLICATION->IncludeComponent('mht:offers', '', array(
	'NAME' => 'Новинки',
	'TYPE' => 'SAYT_NOVINKA',
    'SECTION_ID' => $_REQUEST['SECTION_ID'],
	//'SORT_BY' => 'NAME',
	//'SORT_ORDER' => 'ASC',
	'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
    'SHOW_SECTIONS' => 'Y',
	'HIDE_NEW_LABEL' => 'Y'
))?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>