<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Адреса и телефоны магазинов сети МОСХОЗТОРГ. Вы также можете посмотреть на карте расположение магазинов и узнать график работы");
$APPLICATION->SetPageProperty("keywords", "магазины, пункты выдачи товара, контактная информация, график работы");
$APPLICATION->SetPageProperty("title", "Магазины Мосхозторг");
$APPLICATION->SetTitle("Магазины");
WP::loadScript('/js_/contacts.js');
$APPLICATION->IncludeComponent('mht:shops', 'more');
?>

<!--Скрипт -->
<script src="ya-taxi-widget-v2.js"></script>

<br><!--Дисклеймер-->
<div class="ya-taxi-widget"
data-CLID="moshoztorg"
data-APIKEY="4b6e4fcc5e99490eb6a152fe02dd1927"
data-use-location="true"
data-point-b="37.55359450000001,55.6681070690493"
data-custom-layout="true"
>
<div style="font-family: Yandex Sans Text,Arial,sans-serif; color: #8c8c8c; line-height: 12px; padding-left: 15px; font-size: 10px" data-disclaimer="true"></div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>