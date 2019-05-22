<?
define ("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
?><?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "mht", Array(
	"REGISTER_URL" => "/personal/register/",	// Страница регистрации
		"FORGOT_PASSWORD_URL" => "/personal/forgot/",	// Страница забытого пароля
		"PROFILE_URL" => "/personal/",	// Страница профиля
		"SHOW_ERRORS" => "N",	// Показывать ошибки
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>