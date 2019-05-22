<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Черная пятница");
?>
<div class="blackfridaysale2015">
	<div class="attention-block"></div>
	<?$APPLICATION->IncludeComponent('mht:offers', 'blackfriday', array(
		'NAME' => 'Черная пятница',
		'TYPE' => 'SAYT_BLACK_FRIDAY_TOVAR',
		'PRODUCTS_BLOCK_VIEW_BLOCK' => (bool)$_SESSION["PRODUCTS_BLOCK_VIEW_BLOCK"],
		'HIDE_BLACKFRIDAY_LABEL' => 'Y',
		'SECTION_ID' => $_REQUEST['SECTION_ID'],
		'SHOW_SECTIONS' => 'Y',
		'CACHE_TYPE' => 'A',
		'CACHE_TIME' => 86400,
	))?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>