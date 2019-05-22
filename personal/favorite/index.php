<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");
WP::loadScript('/js_/fave.js');
?>
<?
$APPLICATION->IncludeComponent('mht:favorites', '', array(
));
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>