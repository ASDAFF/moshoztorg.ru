<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказ");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order", 
	"mht1", 
	array(
		"PROP_1" => array(
		),
		"PROP_3" => "",
		"PROP_2" => array(
		),
		"PROP_4" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SEF_MODE" => "N",
		"SEF_FOLDER" => "/personal/orders-completed/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"ORDERS_PER_PAGE" => "20",
		"PATH_TO_PAYMENT" => "make/payment.php",
		"PATH_TO_BASKET" => "/catalog/basket/",
		"SET_TITLE" => "Y",
		"SAVE_IN_SESSION" => "Y",
		"NAV_TEMPLATE" => "",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
			0 => "",
		),
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_P" => "yellow",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red",
		"COMPONENT_TEMPLATE" => "mht1",
		"STATUS_COLOR_H" => "gray",
		"STATUS_COLOR_W" => "gray",
		"STATUS_COLOR_C" => "gray",
		"STATUS_COMPLETE" => "Y"

	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>