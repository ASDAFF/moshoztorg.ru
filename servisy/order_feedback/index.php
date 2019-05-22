<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Анкета покупателя");
?>
<?
//ZmL5Zwq8ZwN2
//MzY5Mjd8MjA2
//MzY5Mjd8MjAz
//ZmL5Zwq8ZwNm
$userKey = 'ZmL5Zwq8ZwNm';
//echo str_rot13($userKey);
$decodedUserKey = base64_decode(str_rot13($userKey));
$arDecode = explode('|', $decodedUserKey);
print_r($arDecode);
$userId = $arDecode[0];
$orderId = $arDecode[1];
dump();
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	"order_feedback", 
	array(
		"WEB_FORM_ID" => "1",
		"WEB_FORM_ID2" => "Y",
		"FORM_CONTAINER_CLASS" => "form-standart",
		"FORM_CONTAINER_ID" => "webform_showroom",
		"THANKYOU_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"USE_EXTENDED_ERRORS" => "Y",
		"SEF_MODE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"LIST_URL" => "",
		"EDIT_URL" => "",
		"SUCCESS_URL" => "",
		"CHAIN_ITEM_TEXT" => "",
		"CHAIN_ITEM_LINK" => "",
		"SHOW_TITLE" => "N",
		"USER_ID" => $userId,
		"ORDER_ID" => $orderId,
		"NAME_VALUE" => $USER->GetParam("FIRST_NAME"), // Наполнить из профиля
		"LASTNAME_VALUE" => $USER->GetParam("LAST_NAME"), // Наполнить из профиля
		"PHONE_VALUE" => "", // Наполнить из профиля
		"EMAIL_VALUE" => $USER->GetParam("EMAIL"), // Наполнить из профиля
		"DATE_VALUE" => "",
		"TIME_VALUE" => "",
		"ITEMS_VALUE" => "", // Список артикулов через запятую
		"ITEMS_TEXT_VALUE" => "", // Форматированный список товаров (будет отправляться в письме)
		"_UTM_VALUE" => "", // Это само наполняется
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>