<?	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Каталог брендов, представленных в сети магазинов Мосхозторг. Многие из них продаются только в нашей сети на территории Российской Федерации. Спешите ознакомиться и приобрести европейский товар по привлекательным ценам");
$APPLICATION->SetPageProperty("keywords", "Мосхозторг бренды производители марки");
$APPLICATION->SetPageProperty("title", "Бренды магазина Мосхозторг");	$APPLICATION->SetTitle("Бренды");?>
<?
	WP::loadScript('/js_/brands.js');
?>
<?$APPLICATION->IncludeComponent('mht:brands', '', array(
	'SEF_MODE' => 'Y',
	'SEF_FOLDER' => '/brands/',
    'IS_GROUP_BY_LETTERS' => 'Y'
))?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>