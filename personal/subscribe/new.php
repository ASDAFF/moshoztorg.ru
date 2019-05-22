<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Рассылка");
?><?$APPLICATION->IncludeComponent(
	"unisender.integration:subscribe", 
	".default", 
	array(
		"AJAX_MODE" => "Y",
		"LIST_ID" => "7282678",
		"USE_CACHE" => "Y",
		"WEB_FORM_ID" => "2",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>