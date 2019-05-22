<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Каталог товаров Мосхозторга. Здесь вы можете найти широкий ассортимент товаров для дома, для дачи и сада, множество хозяйственных мелочей");
$APPLICATION->SetPageProperty("title", "Каталог товаров");
$APPLICATION->SetTitle("Каталог");
?><?$APPLICATION->IncludeComponent('itsfera:catalog_categories', '', array("CACHE_TIME"=>10800));?><?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>